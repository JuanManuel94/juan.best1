const sqlite3 = require('sqlite3').verbose();
const path = require('path');

const dbPath = path.resolve(__dirname, 'database.sqlite');
const db = new sqlite3.Database(dbPath, (err) => {
    if (err) {
        console.error('Error opening database:', err.message);
        process.exit(1);
    }
    console.log('Connected to the SQLite database.');
});

const alterTable = () => {
    db.serialize(() => {
        // Add city column
        db.run("ALTER TABLE vehicles ADD COLUMN city TEXT", (err) => {
            if (err) {
                if (err.message.includes('duplicate column name')) {
                    console.log('Column "city" already exists.');
                } else {
                    console.error('Error adding "city" column:', err.message);
                }
            } else {
                console.log('Column "city" added successfully.');
            }
        });

        // Add department column
        db.run("ALTER TABLE vehicles ADD COLUMN department TEXT", (err) => {
            if (err) {
                if (err.message.includes('duplicate column name')) {
                    console.log('Column "department" already exists.');
                } else {
                    console.error('Error adding "department" column:', err.message);
                }
            } else {
                console.log('Column "department" added successfully.');
            }
        });
    });

    db.close((err) => {
        if (err) {
            console.error('Error closing database:', err.message);
        } else {
            console.log('Database connection closed.');
        }
    });
};

alterTable();
