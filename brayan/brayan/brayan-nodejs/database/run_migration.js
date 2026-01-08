/**
 * Script para ejecutar migraciones SQL
 * Ejecutar con: node database/run_migration.js
 */

const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');
const config = require('../config/config');

async function runMigration() {
    console.log('Conectando a la base de datos...');
    
    const connection = await mysql.createConnection({
        host: config.DB_HOST,
        user: config.DB_USER,
        password: config.DB_PASSWORD,
        database: config.DB_NAME,
        port: config.DB_PORT || 3306,
        multipleStatements: true  // Permite ejecutar múltiples statements
    });

    try {
        const sqlFile = path.join(__dirname, 'fix_missing_tables.sql');
        const sql = fs.readFileSync(sqlFile, 'utf8');
        
        console.log('Ejecutando script SQL...');
        await connection.query(sql);
        
        console.log('✅ Migración ejecutada correctamente!');
        console.log('Tablas creadas:');
        console.log('  - routers');
        console.log('  - product_categories');
        console.log('  - suppliers');
        console.log('  - products');
        console.log('  - tasks');
        console.log('  - settings');
        console.log('  - hotspot_vouchers');
        console.log('  - hotspot_templates');
        console.log('  - whatsapp_messages');
        console.log('  - whatsapp_templates');
        
    } catch (error) {
        console.error('❌ Error ejecutando migración:', error.message);
        if (error.sqlMessage) {
            console.error('SQL Error:', error.sqlMessage);
        }
    } finally {
        await connection.end();
    }
}

runMigration();
