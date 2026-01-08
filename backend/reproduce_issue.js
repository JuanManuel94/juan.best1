const sqlite3 = require('sqlite3').verbose();
const db = new sqlite3.Database('./database.sqlite');

const runTest = async () => {
    console.log('Starting test...');

    // 1. Add first vehicle
    const v1 = {
        id: 'test_v1_' + Date.now(),
        name: 'Test Vehicle 1',
        lat: 10.9685,
        lng: -74.7813,
        city: 'Barranquilla',
        department: 'Atlántico',
        type: 'vehicle',
        lastUpdate: new Date().toISOString()
    };

    // 2. Add second vehicle (slightly different coords)
    const v2 = {
        id: 'test_v2_' + Date.now(),
        name: 'Test Vehicle 2',
        lat: 10.9700,
        lng: -74.7800,
        city: 'Barranquilla',
        department: 'Atlántico',
        type: 'vehicle',
        lastUpdate: new Date().toISOString()
    };

    const insert = (v) => {
        return new Promise((resolve, reject) => {
            db.run(`INSERT INTO vehicles (id, name, lat, lng, city, department, type, lastUpdate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
                [v.id, v.name, v.lat, v.lng, v.city, v.department, v.type, v.lastUpdate],
                function (err) {
                    if (err) reject(err);
                    else resolve();
                }
            );
        });
    };

    try {
        await insert(v1);
        console.log('Inserted Vehicle 1');
        await insert(v2);
        console.log('Inserted Vehicle 2');

        // 3. Query all vehicles
        db.all("SELECT * FROM vehicles WHERE name LIKE 'Test Vehicle%'", [], (err, rows) => {
            if (err) {
                console.error(err);
                return;
            }
            console.log(`Found ${rows.length} test vehicles:`);
            rows.forEach(r => console.log(`- ${r.name} (${r.lat}, ${r.lng})`));

            // Cleanup
            db.run("DELETE FROM vehicles WHERE name LIKE 'Test Vehicle%'", [], (err) => {
                if (err) console.error('Error cleaning up:', err);
                else console.log('Cleaned up test data.');
            });
        });

    } catch (err) {
        console.error('Error during test:', err);
    }
};

runTest();
