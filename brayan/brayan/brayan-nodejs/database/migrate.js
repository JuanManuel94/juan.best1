const { pool } = require('../config/database');
const fs = require('fs');
const path = require('path');

async function runMigration() {
    console.log('🚀 Iniciando migración v2...');

    try {
        const migrationPath = path.join(__dirname, 'migration_v2.sql');
        const sql = fs.readFileSync(migrationPath, 'utf8');

        // Limpiar el SQL (quitar comentarios y líneas vacías)
        const statements = sql
            .split(';')
            .map(s => s.trim())
            .filter(s => s.length > 0 && !s.startsWith('--'));

        for (let statement of statements) {
            try {
                // Si es un ALTER TABLE, ignorar errores si la columna ya existe
                if (statement.toUpperCase().includes('ALTER TABLE')) {
                    try {
                        await pool.query(statement);
                    } catch (e) {
                        console.log(`⚠️ Advertencia en ALTER: ${e.sqlMessage || e.message}`);
                    }
                } else {
                    await pool.query(statement);
                }
            } catch (err) {
                console.error(`❌ Error ejecutando sentencia: \n${statement.substring(0, 50)}...\nError: ${err.message}`);
            }
        }

        console.log('\n✅ Migración completada correctamente.');
    } catch (error) {
        console.error('❌ Error fatal en migración:', error);
    } finally {
        process.exit(0);
    }
}

runMigration();
