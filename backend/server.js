const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');
const compression = require('compression');
const rateLimit = require('express-rate-limit');
const sqlite3 = require('sqlite3').verbose();
const { isInColombia, detectCity } = require('./colombiaCities');

const app = express();

// Compression middleware - reduces response size by ~70%
app.use(compression());

// Rate limiting - protect API from abuse (increased for development)
const apiLimiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 100000, // limit each IP to 100000 requests per windowMs (development mode)
    message: { error: 'Demasiadas solicitudes. Intenta de nuevo en 15 minutos.' },
    standardHeaders: true,
    legacyHeaders: false,
});
app.use('/api/', apiLimiter);

app.use(cors());
app.use(express.json());

const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: '*',
        methods: ['GET', 'POST']
    }
});

// Database initialization
const db = new sqlite3.Database('./database.sqlite', (err) => {
    if (err) {
        console.error('Error opening database', err.message);
    } else {
        console.log('Connected to the SQLite database.');

        // Supervisors Table
        db.run(`CREATE TABLE IF NOT EXISTS supervisors (
            id TEXT PRIMARY KEY,
            name TEXT,
            cedula TEXT,
            phone TEXT,
            gender TEXT,
            email TEXT,
            password TEXT,
            assignedCities TEXT
        )`);

        // Vehicles Table
        db.run(`CREATE TABLE IF NOT EXISTS vehicles (
            id TEXT PRIMARY KEY,
            name TEXT,
            lat REAL,
            lng REAL,
            city TEXT,
            department TEXT,
            type TEXT,
            lastUpdate TEXT
        )`);

        // NEW: Location History Table
        db.run(`CREATE TABLE IF NOT EXISTS location_history (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            vehicle_id TEXT,
            lat REAL,
            lng REAL,
            timestamp TEXT
        )`);

        // NEW: Geofences Table
        db.run(`CREATE TABLE IF NOT EXISTS geofences (
            id TEXT PRIMARY KEY,
            name TEXT,
            lat REAL,
            lng REAL,
            radius REAL,
            description TEXT
        )`);

        // NEW: Alerts Table
        db.run(`CREATE TABLE IF NOT EXISTS alerts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            vehicle_id TEXT,
            type TEXT,
            message TEXT,
            timestamp TEXT,
            read INTEGER DEFAULT 0
        )`);
    }
});

// Store active clients (vehicles/supervisors) for real-time updates
let activeClients = {};

// Simple in-memory cache for frequently accessed data
const cache = {
    stats: null,
    statsTimestamp: 0,
    CACHE_TTL: 30 * 1000, // 30 seconds TTL

    getStats: function () {
        if (this.stats && (Date.now() - this.statsTimestamp) < this.CACHE_TTL) {
            return this.stats;
        }
        return null;
    },

    setStats: function (data) {
        this.stats = data;
        this.statsTimestamp = Date.now();
    },

    invalidate: function () {
        this.stats = null;
        this.statsTimestamp = 0;
    }
};

// Helper to calculate distance in meters (Haversine formula)
const calculateDistance = (lat1, lng1, lat2, lng2) => {
    const R = 6371e3; // Earth radius in meters
    const φ1 = lat1 * Math.PI / 180;
    const φ2 = lat2 * Math.PI / 180;
    const Δφ = (lat2 - lat1) * Math.PI / 180;
    const Δλ = (lng2 - lng1) * Math.PI / 180;

    const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
        Math.cos(φ1) * Math.cos(φ2) *
        Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c;
};

// Helper to check geofences
const checkGeofences = (vehicleId, lat, lng, vehicleName) => {
    db.all("SELECT * FROM geofences", [], (err, fences) => {
        if (err) return;

        fences.forEach(fence => {
            const distance = calculateDistance(lat, lng, fence.lat, fence.lng);
            // Alert if OUTSIDE of the geofence (Exit alert)
            if (distance > fence.radius) {
                // Check if we recently alerted for this to avoid spam (last 5 mins)
                db.get("SELECT * FROM alerts WHERE vehicle_id = ? AND type = 'geofence_exit' AND timestamp > datetime('now', '-5 minutes')", [vehicleId], (err, row) => {
                    if (!row) {
                        const msg = `El vehículo ${vehicleName} salió de la zona segura: ${fence.name}`;
                        const timestamp = new Date().toISOString();
                        db.run("INSERT INTO alerts (vehicle_id, type, message, timestamp) VALUES (?, ?, ?, ?)",
                            [vehicleId, 'geofence_exit', msg, timestamp]);
                        io.emit('newAlert', { vehicleId, message: msg, timestamp });
                    }
                });
            }
        });
    });
};

// Helper to get all markers (active clients + registered vehicles)
const getAllMarkers = (callback) => {
    const markers = Object.values(activeClients);
    const activeIds = new Set(markers.map(m => m.id));

    db.all("SELECT * FROM vehicles", [], (err, rows) => {
        if (err) {
            console.error(err);
            callback(markers);
            return;
        }

        rows.forEach(v => {
            if (v.lat !== null && v.lng !== null && !activeIds.has(v.id)) {
                markers.push({
                    id: v.id,
                    name: v.name,
                    lat: v.lat,
                    lng: v.lng,
                    city: v.city,
                    department: v.department,
                    type: 'vehicle',
                    lastUpdate: new Date(v.lastUpdate)
                });
            }
        });
        callback(markers);
    });
};

// ---------- Supervisors API ----------
app.get('/api/supervisors', (req, res) => {
    db.all("SELECT * FROM supervisors", [], (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });
        const supervisors = rows.map(row => ({
            ...row,
            assignedCities: row.assignedCities ? JSON.parse(row.assignedCities) : []
        }));
        res.json(supervisors);
    });
});

app.post('/api/supervisors', (req, res) => {
    let { name, cedula, phone, gender, email, password, assignedCities } = req.body;
    if (!name || !cedula || !password) return res.status(400).json({ error: 'Name, Cedula and Password required' });

    name = name.trim();
    cedula = cedula.trim();

    const id = Date.now().toString();
    const citiesJson = JSON.stringify(assignedCities || []);

    const sql = `INSERT INTO supervisors (id, name, cedula, phone, gender, email, password, assignedCities) VALUES (?, ?, ?, ?, ?, ?, ?, ?)`;
    const params = [id, name, cedula, phone, gender, email, password, citiesJson];

    db.run(sql, params, function (err) {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ id, name, cedula, phone, gender, email, password, assignedCities: assignedCities || [] });
    });
});

app.put('/api/supervisors/:id', (req, res) => {
    const { id } = req.params;
    let { name, cedula, phone, gender, email, password, assignedCities } = req.body;

    if (name) name = name.trim();
    if (cedula) cedula = cedula.trim();

    const citiesJson = JSON.stringify(assignedCities || []);

    const sql = `UPDATE supervisors SET name = ?, cedula = ?, phone = ?, gender = ?, email = ?, password = ?, assignedCities = ? WHERE id = ?`;
    const params = [name, cedula, phone, gender, email, password, citiesJson, id];

    db.run(sql, params, function (err) {
        if (err) return res.status(500).json({ error: err.message });
        if (this.changes === 0) return res.status(404).json({ error: 'Not found' });
        res.json({ id, name, cedula, phone, gender, email, password, assignedCities: assignedCities || [] });
    });
});

app.delete('/api/supervisors/:id', (req, res) => {
    const { id } = req.params;
    db.run("DELETE FROM supervisors WHERE id = ?", id, function (err) {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ success: true });
    });
});

// ---------- Vehicles API ----------
app.get('/api/vehicles', (req, res) => {
    db.all("SELECT * FROM vehicles", [], (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(rows);
    });
});

app.post('/api/vehicles', (req, res) => {
    const { name, lat, lng, city, department } = req.body;
    if (!name) return res.status(400).json({ error: 'Name required' });

    if (lat === undefined || lng === undefined || lat === null || lng === null) {
        return res.status(400).json({ error: 'Coordenadas requeridas' });
    }

    const latitude = parseFloat(lat);
    const longitude = parseFloat(lng);

    if (isNaN(latitude) || isNaN(longitude)) {
        return res.status(400).json({ error: 'Coordenadas inválidas' });
    }

    if (!isInColombia(latitude, longitude)) {
        return res.status(400).json({
            error: 'Las coordenadas deben estar dentro de Colombia. Por favor verifica la ubicación.'
        });
    }

    // Use frontend-provided city/department, or fall back to detection
    let cityName = city;
    let deptName = department;

    if (!cityName || !deptName) {
        const cityInfo = detectCity(latitude, longitude);
        cityName = cityName || cityInfo.name;
        deptName = deptName || cityInfo.department;
    }

    const id = Date.now().toString();
    const type = 'vehicle';
    const lastUpdate = new Date().toISOString();

    const sql = `INSERT INTO vehicles (id, name, lat, lng, city, department, type, lastUpdate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)`;
    const params = [id, name, latitude, longitude, cityName, deptName, type, lastUpdate];

    db.run(sql, params, function (err) {
        if (err) return res.status(500).json({ error: err.message });

        // Also save initial history
        db.run("INSERT INTO location_history (vehicle_id, lat, lng, timestamp) VALUES (?, ?, ?, ?)",
            [id, latitude, longitude, lastUpdate]);

        const newVeh = { id, name, lat: latitude, lng: longitude, city: cityName, department: deptName, type, lastUpdate };

        getAllMarkers((markers) => {
            io.emit('locationsUpdate', markers);
        });

        res.json(newVeh);
    });
});

app.put('/api/vehicles/:id', (req, res) => {
    const { id } = req.params;
    const { name, lat, lng, city, department } = req.body;

    let updates = [];
    let params = [];

    if (name) {
        updates.push("name = ?");
        params.push(name);
    }

    // Update city and department if provided
    if (city) {
        updates.push("city = ?");
        params.push(city);
    }
    if (department) {
        updates.push("department = ?");
        params.push(department);
    }

    if (lat !== undefined && lng !== undefined) {
        const latitude = parseFloat(lat);
        const longitude = parseFloat(lng);

        if (isNaN(latitude) || isNaN(longitude)) {
            return res.status(400).json({ error: 'Coordenadas inválidas' });
        }

        if (!isInColombia(latitude, longitude)) {
            return res.status(400).json({ error: 'Coordenadas fuera de Colombia' });
        }

        updates.push("lat = ?");
        params.push(latitude);
        updates.push("lng = ?");
        params.push(longitude);
        updates.push("lastUpdate = ?");
        params.push(new Date().toISOString());

        // Save history
        db.run("INSERT INTO location_history (vehicle_id, lat, lng, timestamp) VALUES (?, ?, ?, ?)",
            [id, latitude, longitude, new Date().toISOString()]);
    }

    if (updates.length === 0) {
        return res.status(400).json({ error: 'No fields to update' });
    }

    params.push(id);

    const sql = `UPDATE vehicles SET ${updates.join(', ')} WHERE id = ?`;

    db.run(sql, params, function (err) {
        if (err) return res.status(500).json({ error: err.message });
        if (this.changes === 0) return res.status(404).json({ error: 'Not found' });

        db.get("SELECT * FROM vehicles WHERE id = ?", [id], (err, row) => {
            if (err) return res.json({ id, name });

            getAllMarkers((markers) => {
                io.emit('locationsUpdate', markers);
            });

            res.json(row);
        });
    });
});

app.delete('/api/vehicles/:id', (req, res) => {
    const { id } = req.params;
    db.run("DELETE FROM vehicles WHERE id = ?", id, function (err) {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ success: true });
    });
});

// ---------- History API ----------
app.get('/api/history/:vehicleId', (req, res) => {
    const { vehicleId } = req.params;
    // Limit to last 100 points for performance
    db.all("SELECT lat, lng, timestamp FROM location_history WHERE vehicle_id = ? ORDER BY timestamp DESC LIMIT 100", [vehicleId], (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(rows.reverse()); // Send in chronological order
    });
});

// ---------- Geofences API ----------
app.get('/api/geofences', (req, res) => {
    db.all("SELECT * FROM geofences", [], (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(rows);
    });
});

app.post('/api/geofences', (req, res) => {
    const { name, lat, lng, radius, description } = req.body;
    const id = Date.now().toString();
    db.run("INSERT INTO geofences (id, name, lat, lng, radius, description) VALUES (?, ?, ?, ?, ?, ?)",
        [id, name, lat, lng, radius, description], (err) => {
            if (err) return res.status(500).json({ error: err.message });
            res.json({ id, name, lat, lng, radius, description });
        });
});

app.delete('/api/geofences/:id', (req, res) => {
    const { id } = req.params;
    db.run("DELETE FROM geofences WHERE id = ?", id, (err) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ success: true });
    });
});

// ---------- Dashboard Stats API (with caching) ----------
app.get('/api/stats', (req, res) => {
    // Check cache first
    const cachedStats = cache.getStats();
    if (cachedStats) {
        return res.json(cachedStats);
    }

    const stats = {
        totalVehicles: 0,
        activeVehicles: 0,
        totalAlerts: 0,
        recentAlerts: []
    };

    db.get("SELECT COUNT(*) as count FROM vehicles", (err, row) => {
        if (!err) stats.totalVehicles = row.count;

        // Count active (updated in last 24h)
        db.get("SELECT COUNT(*) as count FROM vehicles WHERE lastUpdate > datetime('now', '-1 day')", (err, row) => {
            if (!err) stats.activeVehicles = row.count;

            db.get("SELECT COUNT(*) as count FROM alerts", (err, row) => {
                if (!err) stats.totalAlerts = row.count;

                db.all("SELECT * FROM alerts ORDER BY timestamp DESC LIMIT 5", (err, rows) => {
                    if (!err) stats.recentAlerts = rows;

                    // Cache the result
                    cache.setStats(stats);
                    res.json(stats);
                });
            });
        });
    });
});

// ---------- Reports API ----------
app.get('/api/reports/vehicles', (req, res) => {
    const { startDate, endDate, vehicleId } = req.query;

    let sql = `
        SELECT v.id, v.name, v.city, v.department, v.lastUpdate,
               COUNT(h.id) as totalLocations,
               MIN(h.timestamp) as firstRecord,
               MAX(h.timestamp) as lastRecord
        FROM vehicles v
        LEFT JOIN location_history h ON v.id = h.vehicle_id
    `;

    let conditions = [];
    let params = [];

    if (startDate) {
        conditions.push("h.timestamp >= ?");
        params.push(startDate);
    }
    if (endDate) {
        conditions.push("h.timestamp <= ?");
        params.push(endDate);
    }
    if (vehicleId) {
        conditions.push("v.id = ?");
        params.push(vehicleId);
    }

    if (conditions.length > 0) {
        sql += " WHERE " + conditions.join(" AND ");
    }

    sql += " GROUP BY v.id ORDER BY v.name";

    db.all(sql, params, (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(rows);
    });
});

app.get('/api/reports/alerts', (req, res) => {
    const { startDate, endDate, vehicleId } = req.query;

    let sql = `
        SELECT a.*, v.name as vehicleName, v.city, v.department
        FROM alerts a
        LEFT JOIN vehicles v ON a.vehicle_id = v.id
    `;

    let conditions = [];
    let params = [];

    if (startDate) {
        conditions.push("a.timestamp >= ?");
        params.push(startDate);
    }
    if (endDate) {
        conditions.push("a.timestamp <= ?");
        params.push(endDate);
    }
    if (vehicleId) {
        conditions.push("a.vehicle_id = ?");
        params.push(vehicleId);
    }

    if (conditions.length > 0) {
        sql += " WHERE " + conditions.join(" AND ");
    }

    sql += " ORDER BY a.timestamp DESC";

    db.all(sql, params, (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(rows);
    });
});

app.get('/api/reports/history', (req, res) => {
    const { startDate, endDate, vehicleId } = req.query;

    if (!vehicleId) {
        return res.status(400).json({ error: 'vehicleId is required' });
    }

    let sql = `
        SELECT h.*, v.name as vehicleName
        FROM location_history h
        JOIN vehicles v ON h.vehicle_id = v.id
        WHERE h.vehicle_id = ?
    `;

    let params = [vehicleId];

    if (startDate) {
        sql += " AND h.timestamp >= ?";
        params.push(startDate);
    }
    if (endDate) {
        sql += " AND h.timestamp <= ?";
        params.push(endDate);
    }

    sql += " ORDER BY h.timestamp ASC";

    db.all(sql, params, (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(rows);
    });
});

// ---------- Login ----------
app.post('/api/login', (req, res) => {
    let { cedula, password } = req.body;

    if (cedula === 'admin' && password === 'admin123') {
        return res.json({
            success: true,
            user: { name: 'Administrador', cedula: 'admin', role: 'admin' }
        });
    }

    if (cedula) cedula = cedula.trim();
    if (password) password = password.trim();

    db.get("SELECT * FROM supervisors WHERE name = ?", [cedula], (err, row) => {
        if (err) return res.status(500).json({ error: err.message });

        if (row) {
            if (row.password === password) {
                const { password: pw, ...supData } = row;
                return res.json({ success: true, user: { ...supData, role: 'supervisor' } });
            }
        }

        res.status(401).json({ error: 'Credenciales inválidas' });
    });
});

// ---------- Socket.io ----------
io.on('connection', (socket) => {
    console.log('User connected:', socket.id);

    getAllMarkers((markers) => {
        socket.emit('locationsUpdate', markers);
    });

    socket.on('join', (data) => {
        activeClients[socket.id] = { ...data, lat: null, lng: null, lastUpdate: new Date() };
        console.log(`${data.type} joined: ${data.id}`);
        getAllMarkers((markers) => {
            io.emit('locationsUpdate', markers);
        });
    });

    socket.on('updateLocation', (coords) => {
        if (activeClients[socket.id]) {
            activeClients[socket.id].lat = coords.lat;
            activeClients[socket.id].lng = coords.lng;
            activeClients[socket.id].lastUpdate = new Date();

            if (activeClients[socket.id].type === 'vehicle') {
                const id = activeClients[socket.id].id;
                const name = activeClients[socket.id].id; // Usually name is sent in join, but here we use ID as fallback
                const lastUpdate = new Date().toISOString();

                // Update DB
                db.run("UPDATE vehicles SET lat = ?, lng = ?, lastUpdate = ? WHERE id = ?",
                    [coords.lat, coords.lng, lastUpdate, id],
                    (err) => {
                        if (err) console.error("Error updating vehicle location", err);
                    }
                );

                // Save History
                db.run("INSERT INTO location_history (vehicle_id, lat, lng, timestamp) VALUES (?, ?, ?, ?)",
                    [id, coords.lat, coords.lng, lastUpdate]);

                // Check Geofences
                checkGeofences(id, coords.lat, coords.lng, name);
            }

            getAllMarkers((markers) => {
                io.emit('locationsUpdate', markers);
            });
        }
    });

    socket.on('disconnect', () => {
        console.log('User disconnected:', socket.id);
        delete activeClients[socket.id];
        getAllMarkers((markers) => {
            io.emit('locationsUpdate', markers);
        });
    });
});

const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});
