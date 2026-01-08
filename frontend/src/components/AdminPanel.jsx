import React, { useState, useEffect } from 'react';
import MapComponent from './MapComponent';
import RouteCalculator from './RouteCalculator';
import ReportsPanel from './ReportsPanel';
import './AdminPanel.css';

import { COLOMBIA_CITIES, DEPARTMENT_NAMES, getMunicipios, getMunicipioData } from '../constants/cities';

const AdminPanel = ({ socket }) => {
    // City detection function (matches backend logic)
    const detectCityFromCoords = (lat, lng) => {
        const COLOMBIA_BOUNDS = { latMin: -4.2, latMax: 13.4, lngMin: -79.0, lngMax: -66.9 };
        if (lat < COLOMBIA_BOUNDS.latMin || lat > COLOMBIA_BOUNDS.latMax ||
            lng < COLOMBIA_BOUNDS.lngMin || lng > COLOMBIA_BOUNDS.lngMax) {
            return null;
        }

        for (const city of COLOMBIA_CITIES) {
            if (lat >= city.bounds.latMin && lat <= city.bounds.latMax &&
                lng >= city.bounds.lngMin && lng <= city.bounds.lngMax) {
                return { name: city.name, department: city.department };
            }
        }

        return { name: 'Colombia', department: 'Ubicación no identificada' };
    };

    const [activeTab, setActiveTab] = useState('dashboard'); // dashboard, supervisors, vehicles, history, geofences
    const [sidebarCollapsed, setSidebarCollapsed] = useState(false);
    const [items, setItems] = useState([]);
    const [stats, setStats] = useState({ totalVehicles: 0, activeVehicles: 0, totalAlerts: 0, recentAlerts: [] });
    const [geofences, setGeofences] = useState([]);
    const [historyPath, setHistoryPath] = useState([]);
    const [selectedHistoryVehicle, setSelectedHistoryVehicle] = useState('');

    // Vehicle specific state
    const [selectedDepartment, setSelectedDepartment] = useState('Cundinamarca');
    const [vehicleForm, setVehicleForm] = useState({
        name: '',
        lat: '4.6097',
        lng: '-74.0817',
        city: 'Bogotá D.C.',
        department: 'Cundinamarca'
    });

    // Supervisor specific state
    const [supForm, setSupForm] = useState({
        name: '',
        cedula: '',
        phone: '',
        gender: 'Masculino',
        email: '',
        password: '',
        assignedCities: []
    });

    // Geofence specific state
    const [geoForm, setGeoForm] = useState({
        name: '',
        lat: '4.6097',
        lng: '-74.0817',
        radius: 500,
        description: ''
    });

    const [editingId, setEditingId] = useState(null);
    const [editForm, setEditForm] = useState({ name: '', lat: '', lng: '' });
    const [editingSupervisor, setEditingSupervisor] = useState(null);
    const [focusedVehicle, setFocusedVehicle] = useState(null);
    const [message, setMessage] = useState({ text: '', type: '' });
    const [isCityDropdownOpen, setIsCityDropdownOpen] = useState(false); // State for custom dropdown
    const [calculatedRoute, setCalculatedRoute] = useState(null); // State for calculated route
    const [allVehicles, setAllVehicles] = useState([]); // State for all vehicles (for route calculator)

    // Sidebar navigation items
    const navItems = [
        { id: 'dashboard', icon: '📊', label: 'Dashboard' },
        { id: 'supervisors', icon: '👥', label: 'Supervisores' },
        { id: 'vehicles', icon: '🚗', label: 'Vehículos' },
        { id: 'history', icon: '📜', label: 'Historial' },
        { id: 'geofences', icon: '📍', label: 'Geocercas' },
        { id: 'reports', icon: '📈', label: 'Reportes' },
    ];

    // Helper for safe JSON parsing
    const safeParseCities = (jsonString) => {
        try {
            if (!jsonString) return [];
            if (Array.isArray(jsonString)) return jsonString; // Already an array
            const parsed = JSON.parse(jsonString);
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            console.warn('Error parsing cities:', e);
            return [];
        }
    };

    useEffect(() => {
        // Scroll to top when tab changes
        window.scrollTo({ top: 0, behavior: 'smooth' });

        setItems([]); // Clear items to avoid type mismatches during transition
        if (activeTab === 'dashboard') fetchStats();
        if (activeTab === 'supervisors') fetchItems('http://localhost:3000/api/supervisors');
        if (activeTab === 'vehicles') fetchItems('http://localhost:3000/api/vehicles');
        if (activeTab === 'geofences') fetchGeofences();
        if (activeTab === 'history') fetchItems('http://localhost:3000/api/vehicles'); // Need vehicles to select

        // Always fetch vehicles for route calculator
        fetchAllVehicles();
    }, [activeTab]);

    const fetchAllVehicles = async () => {
        try {
            const res = await fetch('http://localhost:3000/api/vehicles');
            const data = await res.json();
            setAllVehicles(data);
        } catch (error) {
            console.error('Error fetching all vehicles:', error);
        }
    };

    // Route Calculator handlers
    const handleRouteCalculated = (coordinates, routeInfo) => {
        setCalculatedRoute(coordinates);
    };

    const handleClearRoute = () => {
        setCalculatedRoute(null);
    };

    const fetchItems = async (url) => {
        try {
            const res = await fetch(url);
            const data = await res.json();
            setItems(data);
        } catch (error) {
            console.error('Error fetching items:', error);
        }
    };

    const fetchStats = async () => {
        try {
            const res = await fetch('http://localhost:3000/api/stats');
            const data = await res.json();
            setStats(data);
        } catch (error) {
            console.error('Error fetching stats:', error);
        }
    };

    const fetchGeofences = async () => {
        try {
            const res = await fetch('http://localhost:3000/api/geofences');
            const data = await res.json();
            setGeofences(data);
        } catch (error) {
            console.error('Error fetching geofences:', error);
        }
    };

    // --- History Playback State ---
    const [isPlaying, setIsPlaying] = useState(false);
    const [playbackIndex, setPlaybackIndex] = useState(0);
    const [playbackSpeed, setPlaybackSpeed] = useState(1);
    const [playbackPosition, setPlaybackPosition] = useState(null);

    // Reset playback when history changes
    useEffect(() => {
        setIsPlaying(false);
        setPlaybackIndex(0);
        setPlaybackPosition(null);
    }, [historyPath]);

    // Playback Loop
    useEffect(() => {
        let interval;
        if (isPlaying && historyPath.length > 0) {
            interval = setInterval(() => {
                setPlaybackIndex(prev => {
                    if (prev >= historyPath.length - 1) {
                        setIsPlaying(false);
                        return prev;
                    }
                    return prev + 1;
                });
            }, 1000 / playbackSpeed);
        }
        return () => clearInterval(interval);
    }, [isPlaying, historyPath, playbackSpeed]);

    // Update playback position when index changes
    useEffect(() => {
        if (historyPath.length > 0 && historyPath[playbackIndex]) {
            // historyPath is [[lat, lng], [lat, lng]...] (from fetchHistory)
            // But wait, fetchHistory maps it to [lat, lng]. We might need the original objects for timestamps if we want to show them.
            // Let's check fetchHistory. It does: const path = data.map(p => [p.lat, p.lng]);
            // We need to store the full history data to get timestamps.
            // Let's modify fetchHistory to store full data too.
        }
    }, [playbackIndex, historyPath]);

    // We need to modify fetchHistory to store full data
    const [fullHistory, setFullHistory] = useState([]);

    const fetchHistory = async (vehicleId) => {
        try {
            const res = await fetch(`http://localhost:3000/api/history/${vehicleId}`);
            const data = await res.json();
            setFullHistory(data); // Store full data
            // Convert to [lat, lng] format for Polyline
            const path = data.map(p => [p.lat, p.lng]);
            setHistoryPath(path);

            // Focus map on first point
            if (path.length > 0) {
                setFocusedVehicle({ lat: path[0][0], lng: path[0][1], type: 'vehicle' });
                setPlaybackIndex(0);
                setPlaybackPosition(data[0]);
            }
        } catch (error) {
            console.error('Error fetching history:', error);
        }
    };

    // Sync playbackPosition with playbackIndex using fullHistory
    useEffect(() => {
        if (fullHistory.length > 0 && fullHistory[playbackIndex]) {
            setPlaybackPosition(fullHistory[playbackIndex]);
        }
    }, [playbackIndex, fullHistory]);


    const handleSupChange = (e) => {
        const { name, value, checked } = e.target;
        if (name === 'assignedCities') {
            const currentCities = supForm.assignedCities || [];
            if (checked) {
                setSupForm({ ...supForm, assignedCities: [...currentCities, value] });
            } else {
                setSupForm({ ...supForm, assignedCities: currentCities.filter(city => city !== value) });
            }
        } else {
            setSupForm({ ...supForm, [name]: value });
        }
    };

    const handleVehicleChange = (e) => {
        const { name, value } = e.target;
        const updatedForm = { ...vehicleForm, [name]: value };

        if (name === 'lat' || name === 'lng') {
            const lat = parseFloat(name === 'lat' ? value : vehicleForm.lat);
            const lng = parseFloat(name === 'lng' ? value : vehicleForm.lng);

            if (!isNaN(lat) && !isNaN(lng)) {
                const cityInfo = detectCityFromCoords(lat, lng);
                if (cityInfo) {
                    updatedForm.city = cityInfo.name;
                    updatedForm.department = cityInfo.department;
                }
            }
        }
        setVehicleForm(updatedForm);
    };

    const handleGeoChange = (e) => {
        setGeoForm({ ...geoForm, [e.target.name]: e.target.value });
    };

    const handleAdd = async (e) => {
        e.preventDefault();
        setMessage({ text: '', type: '' });

        let url = '';
        let body = {};

        if (activeTab === 'supervisors') {
            url = 'http://localhost:3000/api/supervisors';
            body = { ...supForm };
        } else if (activeTab === 'vehicles') {
            url = 'http://localhost:3000/api/vehicles';
            body = { ...vehicleForm, lat: parseFloat(vehicleForm.lat), lng: parseFloat(vehicleForm.lng) };
        } else if (activeTab === 'geofences') {
            url = 'http://localhost:3000/api/geofences';
            body = { ...geoForm, lat: parseFloat(geoForm.lat), lng: parseFloat(geoForm.lng), radius: parseFloat(geoForm.radius) };
        }

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body),
            });

            if (!res.ok) {
                const errorData = await res.json();
                setMessage({ text: errorData.error || 'Error al agregar', type: 'error' });
                return;
            }

            const newItem = await res.json();

            if (activeTab === 'geofences') {
                setGeofences([...geofences, newItem]);
                setGeoForm({ name: '', lat: '4.6097', lng: '-74.0817', radius: 500, description: '' });
            } else {
                setItems([...items, newItem]);
                if (activeTab === 'supervisors') {
                    setSupForm({ name: '', cedula: '', phone: '', gender: 'Masculino', email: '', password: '', assignedCities: [] });
                } else {
                    setVehicleForm({ name: '', lat: '4.6097', lng: '-74.0817', city: 'Bogotá', department: 'Cundinamarca' });
                }
            }
            setMessage({ text: 'Agregado exitosamente', type: 'success' });
            setTimeout(() => setMessage({ text: '', type: '' }), 3000);
        } catch (error) {
            console.error('Error adding item:', error);
            setMessage({ text: 'Error de conexión', type: 'error' });
        }
    };

    const handleDelete = async (id, type) => {
        if (!window.confirm('¿Estás seguro de eliminar este elemento?')) return;
        const url = `http://localhost:3000/api/${type}/${id}`;
        try {
            await fetch(url, { method: 'DELETE' });
            if (type === 'geofences') {
                setGeofences(geofences.filter(item => item.id !== id));
            } else {
                setItems(items.filter(item => item.id !== id));
            }
        } catch (error) {
            console.error('Error deleting item:', error);
        }
    };

    const handleFollowVehicle = (vehicle) => {
        const vehicleWithType = { ...vehicle, type: 'vehicle', lat: parseFloat(vehicle.lat), lng: parseFloat(vehicle.lng) };
        setFocusedVehicle(vehicleWithType);
        setTimeout(() => {
            document.querySelector('.map-section-admin')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    };

    // --- Edit Logic ---
    const startEditing = (item) => {
        setEditingId(item.id);
        if (activeTab === 'supervisors') {
            setEditingSupervisor({ ...item, password: '', assignedCities: safeParseCities(item.assignedCities) });
        } else {
            setEditForm({
                name: item.name,
                lat: item.lat,
                lng: item.lng,
                city: item.city,
                department: item.department || ''
            });
        }
    };

    const handleEditChange = (e) => {
        const { name, value } = e.target;

        if (name === 'department') {
            // When department changes, clear city and coordinates
            setEditForm(prev => ({
                ...prev,
                department: value,
                city: '',
                lat: '',
                lng: ''
            }));
        } else if (name === 'city' && editForm.department) {
            // When municipality is selected, update coordinates
            const municipioData = getMunicipioData(editForm.department, value);
            if (municipioData) {
                setEditForm(prev => ({
                    ...prev,
                    city: value,
                    lat: municipioData.lat.toFixed(6),
                    lng: municipioData.lng.toFixed(6)
                }));
                // Focus map on new location
                setFocusedVehicle({ lat: municipioData.lat, lng: municipioData.lng, type: 'city_center' });
            } else {
                setEditForm(prev => ({ ...prev, city: value }));
            }
        } else {
            setEditForm(prev => ({ ...prev, [name]: value }));
        }
    };

    const handleEditSupChange = (e) => {
        const { name, value, checked } = e.target;
        if (name === 'assignedCities') {
            const current = editingSupervisor.assignedCities || [];
            if (checked) {
                setEditingSupervisor({ ...editingSupervisor, assignedCities: [...current, value] });
            } else {
                setEditingSupervisor({ ...editingSupervisor, assignedCities: current.filter(c => c !== value) });
            }
        } else {
            setEditingSupervisor({ ...editingSupervisor, [name]: value });
        }
    };

    const cancelEditing = () => {
        setEditingId(null);
        setEditForm({ name: '', lat: '', lng: '' });
        setEditingSupervisor(null);
    };

    const handleUpdate = async (id) => {
        const url = activeTab === 'supervisors'
            ? `http://localhost:3000/api/supervisors/${id}`
            : `http://localhost:3000/api/vehicles/${id}`;

        let body = activeTab === 'supervisors'
            ? { ...editingSupervisor }
            : { ...editForm };

        // If password is empty (unchanged), remove it so we don't overwrite it with blank
        if (activeTab === 'supervisors' && !body.password) {
            delete body.password;
        }

        try {
            const res = await fetch(url, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body)
            });

            if (res.ok) {
                const updatedItem = await res.json();
                setItems(items.map(item => item.id === id ? updatedItem : item));
                setMessage({ text: 'Actualizado correctamente', type: 'success' });
                cancelEditing();
            } else {
                setMessage({ text: 'Error al actualizar', type: 'error' });
            }
        } catch (error) {
            console.error('Error updating:', error);
        }
    };

    // --- Map Click Handler ---
    const handleMapClick = (latlng) => {
        const { lat, lng } = latlng;

        // Check if editing a vehicle first (takes priority)
        if (editingId && activeTab === 'vehicles') {
            setEditForm(prev => ({
                ...prev,
                lat: lat.toFixed(6),
                lng: lng.toFixed(6)
            }));
            setMessage({ text: '📍 Ubicación del vehículo actualizada en el mapa', type: 'success' });
            setTimeout(() => setMessage({ text: '', type: '' }), 2000);
        } else if (activeTab === 'vehicles') {
            // Adding new vehicle - only update coordinates
            setVehicleForm(prev => ({
                ...prev,
                lat: lat.toFixed(6),
                lng: lng.toFixed(6)
            }));
            setMessage({ text: '📍 Ubicación seleccionada. Recuerda seleccionar Departamento y Municipio.', type: 'success' });
            setTimeout(() => setMessage({ text: '', type: '' }), 3000);
        } else if (activeTab === 'geofences') {
            setGeoForm(prev => ({
                ...prev,
                lat: lat.toFixed(6),
                lng: lng.toFixed(6)
            }));
            setMessage({ text: 'Centro de geocerca seleccionado', type: 'success' });
            setTimeout(() => setMessage({ text: '', type: '' }), 2000);
        }
    };

    // Handle department change for vehicle form
    const handleDepartmentChange = (e) => {
        const deptName = e.target.value;
        setSelectedDepartment(deptName);
        // Reset city when department changes
        setVehicleForm({
            ...vehicleForm,
            department: deptName,
            city: '',
            lat: '',
            lng: ''
        });
    };

    // Handle municipality selection for vehicle form
    const handleMunicipioSelect = (e) => {
        const municipioName = e.target.value;
        const municipioData = getMunicipioData(selectedDepartment, municipioName);

        if (municipioData) {
            setVehicleForm({
                ...vehicleForm,
                city: municipioData.name,
                department: selectedDepartment,
                lat: municipioData.lat.toFixed(6),
                lng: municipioData.lng.toFixed(6)
            });
            // Fly to municipality
            setFocusedVehicle({ lat: municipioData.lat, lng: municipioData.lng, type: 'city_center' });
        } else {
            setVehicleForm({ ...vehicleForm, city: '' });
        }
    };

    // Get current section title
    const getCurrentTitle = () => {
        const item = navItems.find(nav => nav.id === activeTab);
        return item ? `${item.icon} ${item.label}` : 'Panel de Administración';
    };

    return (
        <div className={`admin-layout ${sidebarCollapsed ? 'sidebar-collapsed' : ''}`}>
            {/* Sidebar */}
            <aside className="admin-sidebar">
                <div className="sidebar-header">
                    <div className="sidebar-logo">
                        <span className="logo-icon">🚛</span>
                        {!sidebarCollapsed && <span className="logo-text">DriveGo</span>}
                    </div>
                    <button
                        className="sidebar-toggle"
                        onClick={() => setSidebarCollapsed(!sidebarCollapsed)}
                        title={sidebarCollapsed ? 'Expandir' : 'Colapsar'}
                    >
                        {sidebarCollapsed ? '▶' : '◀'}
                    </button>
                </div>

                <nav className="sidebar-nav">
                    {navItems.map(item => (
                        <button
                            key={item.id}
                            className={`nav-item ${activeTab === item.id ? 'active' : ''}`}
                            onClick={() => setActiveTab(item.id)}
                            title={sidebarCollapsed ? item.label : ''}
                        >
                            <span className="nav-icon">{item.icon}</span>
                            {!sidebarCollapsed && <span className="nav-label">{item.label}</span>}
                            {activeTab === item.id && <span className="nav-indicator"></span>}
                        </button>
                    ))}
                </nav>

                <div className="sidebar-footer">
                    {!sidebarCollapsed && (
                        <div className="sidebar-info">
                            <span className="version">v2.0</span>
                        </div>
                    )}
                </div>
            </aside>

            {/* Main Content */}
            <main className="admin-main">
                <header className="content-header">
                    <h1>{getCurrentTitle()}</h1>
                    <div className="header-actions">
                        <span className="current-date">
                            {new Date().toLocaleDateString('es-CO', {
                                weekday: 'long',
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            })}
                        </span>
                    </div>
                </header>

                <div className="content-body">
                    {activeTab === 'dashboard' && (
                        <div className="dashboard-grid">
                            <div className="stat-card">
                                <h3>Total Vehículos</h3>
                                <div className="stat-value">{stats.totalVehicles}</div>
                            </div>
                            <div className="stat-card secondary">
                                <h3>Vehículos Activos (24h)</h3>
                                <div className="stat-value">{stats.activeVehicles}</div>
                            </div>
                            <div className="stat-card warning">
                                <h3>Total Alertas</h3>
                                <div className="stat-value">{stats.totalAlerts}</div>
                            </div>
                            <div className="alerts-list">
                                <h3>🔔 Alertas Recientes</h3>
                                {stats.recentAlerts.length === 0 ? <p>No hay alertas recientes</p> : (
                                    <ul>
                                        {stats.recentAlerts.map(alert => (
                                            <li key={alert.id} className="alert-item">
                                                <strong>{new Date(alert.timestamp).toLocaleString()}</strong>: {alert.message}
                                            </li>
                                        ))}
                                    </ul>
                                )}
                            </div>
                        </div>
                    )}

                    {activeTab === 'history' && (
                        <div className="history-controls">
                            <h3>🎬 Reproducción de Rutas</h3>

                            {/* Vehicle and Date Selection */}
                            <div className="history-filters" style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '16px', marginBottom: '20px', padding: '16px', background: '#f8fafc', borderRadius: '8px' }}>
                                <div className="form-group">
                                    <label>🚗 Vehículo</label>
                                    <select
                                        value={selectedHistoryVehicle}
                                        onChange={(e) => {
                                            setSelectedHistoryVehicle(e.target.value);
                                            if (e.target.value) fetchHistory(e.target.value);
                                        }}
                                        className="history-select"
                                    >
                                        <option value="">-- Seleccione --</option>
                                        {items.map(v => <option key={v.id} value={v.id}>{v.name}</option>)}
                                    </select>
                                </div>
                            </div>

                            {historyPath.length > 0 && (
                                <div className="playback-controls" style={{ background: 'white', padding: '20px', borderRadius: '12px', boxShadow: '0 2px 8px rgba(0,0,0,0.1)' }}>
                                    {/* Main Controls */}
                                    <div className="playback-buttons" style={{ display: 'flex', gap: '8px', alignItems: 'center', justifyContent: 'center', marginBottom: '16px' }}>
                                        <button
                                            onClick={() => setPlaybackIndex(0)}
                                            className="control-btn"
                                            style={{ padding: '8px 12px', borderRadius: '8px', border: '2px solid #e2e8f0', background: 'white', cursor: 'pointer', fontWeight: '600' }}
                                            title="Ir al inicio"
                                        >
                                            ⏮️
                                        </button>
                                        <button
                                            onClick={() => setPlaybackIndex(Math.max(0, playbackIndex - 10))}
                                            className="control-btn"
                                            style={{ padding: '8px 12px', borderRadius: '8px', border: '2px solid #e2e8f0', background: 'white', cursor: 'pointer', fontWeight: '600' }}
                                            title="Retroceder 10 puntos"
                                        >
                                            ⏪ -10
                                        </button>
                                        <button
                                            onClick={() => setIsPlaying(!isPlaying)}
                                            className="play-btn"
                                            style={{ padding: '12px 24px', borderRadius: '8px', border: 'none', background: isPlaying ? 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)' : 'linear-gradient(135deg, #10b981 0%, #059669 100%)', color: 'white', cursor: 'pointer', fontWeight: '700', fontSize: '16px' }}
                                        >
                                            {isPlaying ? '⏸ Pausar' : '▶ Reproducir'}
                                        </button>
                                        <button
                                            onClick={() => setPlaybackIndex(Math.min(historyPath.length - 1, playbackIndex + 10))}
                                            className="control-btn"
                                            style={{ padding: '8px 12px', borderRadius: '8px', border: '2px solid #e2e8f0', background: 'white', cursor: 'pointer', fontWeight: '600' }}
                                            title="Avanzar 10 puntos"
                                        >
                                            +10 ⏩
                                        </button>
                                        <button
                                            onClick={() => setPlaybackIndex(historyPath.length - 1)}
                                            className="control-btn"
                                            style={{ padding: '8px 12px', borderRadius: '8px', border: '2px solid #e2e8f0', background: 'white', cursor: 'pointer', fontWeight: '600' }}
                                            title="Ir al final"
                                        >
                                            ⏭️
                                        </button>
                                        <select
                                            value={playbackSpeed}
                                            onChange={(e) => setPlaybackSpeed(Number(e.target.value))}
                                            className="speed-select"
                                            style={{ padding: '8px 12px', borderRadius: '8px', border: '2px solid #e2e8f0', fontWeight: '600' }}
                                        >
                                            <option value={0.5}>0.5x</option>
                                            <option value={1}>1x</option>
                                            <option value={2}>2x</option>
                                            <option value={5}>5x</option>
                                            <option value={10}>10x</option>
                                        </select>
                                    </div>

                                    {/* Timeline Slider */}
                                    <div style={{ marginBottom: '16px' }}>
                                        <input
                                            type="range"
                                            min="0"
                                            max={historyPath.length - 1}
                                            value={playbackIndex}
                                            onChange={(e) => {
                                                setPlaybackIndex(Number(e.target.value));
                                                setIsPlaying(false);
                                            }}
                                            className="playback-slider"
                                            style={{ width: '100%', height: '8px', cursor: 'pointer' }}
                                        />
                                        {/* Progress Bar Visual */}
                                        <div style={{ background: '#e2e8f0', borderRadius: '4px', height: '8px', marginTop: '8px', overflow: 'hidden' }}>
                                            <div
                                                style={{
                                                    background: 'linear-gradient(90deg, #3b82f6 0%, #10b981 100%)',
                                                    height: '100%',
                                                    width: `${(playbackIndex / (historyPath.length - 1)) * 100}%`,
                                                    transition: 'width 0.2s ease'
                                                }}
                                            />
                                        </div>
                                    </div>

                                    {/* Info Display */}
                                    <div className="playback-info" style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(150px, 1fr))', gap: '12px' }}>
                                        <div style={{ background: '#f0f9ff', padding: '12px', borderRadius: '8px', textAlign: 'center' }}>
                                            <div style={{ fontSize: '24px', fontWeight: '700', color: '#0369a1' }}>{playbackIndex + 1}</div>
                                            <div style={{ fontSize: '12px', color: '#64748b', textTransform: 'uppercase' }}>de {historyPath.length} puntos</div>
                                        </div>
                                        <div style={{ background: '#f0fdf4', padding: '12px', borderRadius: '8px', textAlign: 'center' }}>
                                            <div style={{ fontSize: '14px', fontWeight: '600', color: '#166534' }}>
                                                {playbackPosition ? new Date(playbackPosition.timestamp).toLocaleString() : '--'}
                                            </div>
                                            <div style={{ fontSize: '12px', color: '#64748b', textTransform: 'uppercase' }}>Fecha/Hora</div>
                                        </div>
                                        {playbackPosition && (
                                            <div style={{ background: '#fef3c7', padding: '12px', borderRadius: '8px', textAlign: 'center' }}>
                                                <div style={{ fontSize: '14px', fontWeight: '600', color: '#92400e' }}>
                                                    📍 {playbackPosition.lat?.toFixed(5)}, {playbackPosition.lng?.toFixed(5)}
                                                </div>
                                                <div style={{ fontSize: '12px', color: '#64748b', textTransform: 'uppercase' }}>Coordenadas</div>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            )}

                            {historyPath.length === 0 && selectedHistoryVehicle && (
                                <div style={{ textAlign: 'center', padding: '40px', color: '#64748b' }}>
                                    📭 No hay historial disponible para este vehículo
                                </div>
                            )}

                            {!selectedHistoryVehicle && (
                                <div style={{ textAlign: 'center', padding: '40px', color: '#64748b' }}>
                                    👆 Selecciona un vehículo para ver su historial de rutas
                                </div>
                            )}
                        </div>
                    )}

                    {activeTab === 'reports' && (
                        <ReportsPanel />
                    )}

                    {(activeTab === 'supervisors' || activeTab === 'vehicles' || activeTab === 'geofences') && (
                        <form onSubmit={handleAdd} className="sup-form">
                            {message.text && <div className={`message ${message.type}`}>{message.text}</div>}

                            {activeTab === 'supervisors' && (
                                <div className="grid-form">
                                    <div className="form-group"><label>Nombre</label><input name="name" value={supForm.name} onChange={handleSupChange} /></div>
                                    <div className="form-group"><label>Cédula</label><input name="cedula" value={supForm.cedula} onChange={handleSupChange} /></div>
                                    <div className="form-group"><label>Contraseña</label><input name="password" type="password" value={supForm.password} onChange={handleSupChange} /></div>
                                    <div className="form-group"><label>Teléfono</label><input name="phone" value={supForm.phone} onChange={handleSupChange} /></div>
                                    <div className="form-group">
                                        <label>Género</label>
                                        <select name="gender" value={supForm.gender} onChange={handleSupChange}>
                                            <option value="Masculino">Masculino</option>
                                            <option value="Femenino">Femenino</option>
                                        </select>
                                    </div>
                                    <div className="form-group"><label>Email</label><input name="email" type="email" value={supForm.email} onChange={handleSupChange} /></div>
                                    <div className="form-group full-width">
                                        <label>Departamentos Asignados</label>
                                        <div className="cities-checkbox-container">
                                            {DEPARTMENT_NAMES.map(dept => (
                                                <label key={dept} className="city-checkbox-label">
                                                    <input
                                                        type="checkbox"
                                                        name="assignedCities"
                                                        value={dept}
                                                        checked={supForm.assignedCities.includes(dept)}
                                                        onChange={handleSupChange}
                                                    />
                                                    {dept}
                                                </label>
                                            ))}
                                        </div>
                                    </div>
                                    <div className="form-actions full-width"><button type="submit" className="submit-btn">Registrar</button></div>
                                </div>
                            )}

                            {activeTab === 'vehicles' && (
                                <div className="grid-form">
                                    <div className="form-group"><label>Nombre del Vehículo</label><input name="name" value={vehicleForm.name} onChange={handleVehicleChange} placeholder="Ej: Camión 001" /></div>
                                    <div className="form-group">
                                        <label>Departamento</label>
                                        <select value={selectedDepartment} onChange={handleDepartmentChange}>
                                            <option value="">-- Seleccionar Departamento --</option>
                                            {DEPARTMENT_NAMES.map(dept => (
                                                <option key={dept} value={dept}>{dept}</option>
                                            ))}
                                        </select>
                                    </div>
                                    <div className="form-group">
                                        <label>Municipio</label>
                                        <select name="city" value={vehicleForm.city} onChange={handleMunicipioSelect} disabled={!selectedDepartment}>
                                            <option value="">-- Seleccionar Municipio --</option>
                                            {selectedDepartment && getMunicipios(selectedDepartment).map(mun => (
                                                <option key={mun.name} value={mun.name}>{mun.name}</option>
                                            ))}
                                        </select>
                                    </div>
                                    <div className="form-group"><label>Latitud</label><input name="lat" value={vehicleForm.lat} onChange={handleVehicleChange} placeholder="Clic en el mapa para seleccionar" /></div>
                                    <div className="form-group"><label>Longitud</label><input name="lng" value={vehicleForm.lng} onChange={handleVehicleChange} placeholder="Clic en el mapa para seleccionar" /></div>
                                    <div className="form-group full-width" style={{ color: 'var(--info-color)', fontSize: '0.9rem' }}>
                                        💡 Tip: Selecciona departamento → municipio para ir allí, luego haz clic en el mapa para ajustar la ubicación exacta.
                                    </div>
                                    <div className="form-actions full-width"><button type="submit" className="submit-btn">Agregar Vehículo</button></div>
                                </div>
                            )}

                            {activeTab === 'geofences' && (
                                <div className="grid-form">
                                    <div className="form-group"><label>Nombre Zona</label><input name="name" value={geoForm.name} onChange={handleGeoChange} /></div>
                                    <div className="form-group"><label>Radio (metros)</label><input name="radius" type="number" value={geoForm.radius} onChange={handleGeoChange} /></div>
                                    <div className="form-group"><label>Latitud</label><input name="lat" value={geoForm.lat} onChange={handleGeoChange} placeholder="Clic en el mapa" /></div>
                                    <div className="form-group"><label>Longitud</label><input name="lng" value={geoForm.lng} onChange={handleGeoChange} placeholder="Clic en el mapa" /></div>
                                    <div className="form-group full-width"><label>Descripción</label><input name="description" value={geoForm.description} onChange={handleGeoChange} /></div>
                                    <div className="form-group full-width" style={{ color: 'var(--info-color)', fontSize: '0.9rem' }}>
                                        💡 Tip: Haz clic en el mapa para centrar la geocerca.
                                    </div>
                                    <div className="form-actions full-width"><button type="submit" className="submit-btn">Crear Geocerca</button></div>
                                </div>
                            )}
                        </form>
                    )}

                    {/* --- LISTS (Supervisors / Vehicles) --- */}
                    {(activeTab === 'supervisors' || activeTab === 'vehicles') && (
                        <table className="data-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    {activeTab === 'supervisors' ? (
                                        <>
                                            <th>Cédula</th>
                                            <th>Contraseña</th>
                                            <th>Teléfono</th>
                                            <th>Departamentos</th>
                                        </>
                                    ) : (
                                        <>
                                            <th>Ubicación</th>
                                            <th>Departamento</th>
                                            <th>Municipio</th>
                                        </>
                                    )}
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {items.map(item => (
                                    <tr key={item.id} className={editingId === item.id ? 'editing' : ''}>
                                        {editingId === item.id ? (
                                            // --- EDIT MODE ---
                                            <>
                                                <td>
                                                    <input
                                                        className="edit-input"
                                                        name="name"
                                                        value={activeTab === 'supervisors' ? editingSupervisor.name : editForm.name}
                                                        onChange={activeTab === 'supervisors' ? handleEditSupChange : handleEditChange}
                                                    />
                                                </td>
                                                {activeTab === 'supervisors' ? (
                                                    <>
                                                        <td><input className="edit-input" name="cedula" value={editingSupervisor.cedula} onChange={handleEditSupChange} /></td>
                                                        <td><input className="edit-input" type="password" name="password" value={editingSupervisor.password} onChange={handleEditSupChange} placeholder="(Sin cambios)" /></td>
                                                        <td><input className="edit-input" name="phone" value={editingSupervisor.phone} onChange={handleEditSupChange} /></td>
                                                        <td>
                                                            <div className="custom-multiselect">
                                                                <div
                                                                    className="multiselect-trigger"
                                                                    onClick={() => setIsCityDropdownOpen(!isCityDropdownOpen)}
                                                                >
                                                                    {editingSupervisor.assignedCities.length > 0
                                                                        ? `${editingSupervisor.assignedCities.length} departamentos`
                                                                        : 'Seleccionar departamentos'}
                                                                    <span className="arrow">▼</span>
                                                                </div>
                                                                {isCityDropdownOpen && (
                                                                    <div className="multiselect-dropdown">
                                                                        {DEPARTMENT_NAMES.map(dept => (
                                                                            <label key={dept} className="multiselect-option">
                                                                                <input
                                                                                    type="checkbox"
                                                                                    name="assignedCities"
                                                                                    value={dept}
                                                                                    checked={editingSupervisor.assignedCities.includes(dept)}
                                                                                    onChange={handleEditSupChange}
                                                                                />
                                                                                <span>{dept}</span>
                                                                            </label>
                                                                        ))}
                                                                    </div>
                                                                )}
                                                            </div>
                                                        </td>
                                                    </>
                                                ) : (
                                                    <>
                                                        <td>
                                                            <div className="edit-coords">
                                                                <input className="edit-input-coord" name="lat" value={editForm.lat} onChange={handleEditChange} placeholder="Lat" />
                                                                <input className="edit-input-coord" name="lng" value={editForm.lng} onChange={handleEditChange} placeholder="Lng" />
                                                                <div style={{ fontSize: '0.8rem', color: 'var(--primary-color)', marginTop: '4px' }}>Clic en mapa para cambiar</div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <select name="department" value={editForm.department || ''} onChange={handleEditChange} className="edit-input">
                                                                <option value="">-- Depto --</option>
                                                                {DEPARTMENT_NAMES.map(dept => (
                                                                    <option key={dept} value={dept}>{dept}</option>
                                                                ))}
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="city" value={editForm.city || ''} onChange={handleEditChange} className="edit-input" disabled={!editForm.department}>
                                                                <option value="">-- Municipio --</option>
                                                                {editForm.department && getMunicipios(editForm.department).map(mun => (
                                                                    <option key={mun.name} value={mun.name}>{mun.name}</option>
                                                                ))}
                                                            </select>
                                                        </td>
                                                    </>
                                                )}
                                                <td>
                                                    <div className="action-buttons">
                                                        <button onClick={() => handleUpdate(item.id)} className="save-btn-sm">💾 Guardar</button>
                                                        <button onClick={cancelEditing} className="cancel-btn-sm">❌ Cancelar</button>
                                                    </div>
                                                </td>
                                            </>
                                        ) : (
                                            // --- VIEW MODE ---
                                            <>
                                                <td>{item.name}</td>
                                                {activeTab === 'supervisors' ? (
                                                    <>
                                                        <td>{item.cedula}</td>
                                                        <td>******</td>
                                                        <td>{item.phone}</td>
                                                        <td>{safeParseCities(item.assignedCities).join(', ')}</td>
                                                    </>
                                                ) : (
                                                    <>
                                                        <td>{item.lat}, {item.lng}</td>
                                                        <td>{item.department || '-'}</td>
                                                        <td>{item.city || '-'}</td>
                                                    </>
                                                )}
                                                <td>
                                                    <div className="action-buttons">
                                                        <button onClick={() => startEditing(item)} className="edit-btn-sm">✏️ Editar</button>
                                                        <button onClick={() => handleDelete(item.id, activeTab)} className="delete-btn-sm">🗑️ Eliminar</button>
                                                        {activeTab === 'vehicles' && (
                                                            <button onClick={() => handleFollowVehicle(item)} className="edit-btn-sm">📍 Ver</button>
                                                        )}
                                                    </div>
                                                </td>
                                            </>
                                        )}
                                    </tr>
                                ))}
                            </tbody>
                        </table >
                    )}

                    {
                        activeTab === 'geofences' && (
                            <div className="geofences-list">
                                <h3>Zonas Activas</h3>
                                <ul>
                                    {geofences.map(geo => (
                                        <li key={geo.id} className="geo-item">
                                            <span><strong>{geo.name}</strong> ({geo.radius}m)</span>
                                            <button onClick={() => handleDelete(geo.id, 'geofences')} className="delete-btn-sm">Eliminar</button>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        )
                    }

                    {/* Map Section */}
                    <div className="map-section-admin">
                        <h3>🗺️ Mapa en Tiempo Real {activeTab === 'history' ? '(Modo Historial)' : ''}</h3>
                        <div className="map-container-admin" style={{ position: 'relative' }}>
                            <MapComponent
                                socket={socket}
                                focusedMarker={focusedVehicle}
                                historyPath={activeTab === 'history' ? historyPath : []}
                                geofences={activeTab === 'geofences' ? geofences : []}
                                items={activeTab === 'vehicles' || activeTab === 'supervisors' ? items : []}
                                onMapClick={handleMapClick}
                                playbackPosition={activeTab === 'history' ? playbackPosition : null}
                                draftMarker={activeTab === 'vehicles' && vehicleForm.lat && vehicleForm.lng ? { lat: parseFloat(vehicleForm.lat), lng: parseFloat(vehicleForm.lng) } : null}
                                calculatedRoute={calculatedRoute}
                            />
                            <RouteCalculator
                                vehicles={allVehicles}
                                onRouteCalculated={handleRouteCalculated}
                                onClearRoute={handleClearRoute}
                            />
                        </div>
                    </div>
                </div>
            </main>
        </div>
    );
};


export default AdminPanel;
