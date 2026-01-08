const sqlite3 = require('sqlite3').verbose();
const db = new sqlite3.Database('./database.sqlite');

db.all("SELECT * FROM supervisors", [], (err, rows) => {
    if (err) console.error(err);
    console.log('Supervisors in DB:', rows);
});

db.all("SELECT * FROM vehicles", [], (err, rows) => {
    if (err) console.error(err);
    console.log('Vehicles in DB:', rows);
    db.close();
});
