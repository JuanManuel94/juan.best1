/**
 * Script de inicialización para MySQL
 * Crea las tablas y el usuario administrador
 * 
 * Ejecutar con: node database/init.js
 */

const { Database, pool } = require('../config/database');
const helpers = require('../helpers/helpers');

async function initDatabase() {
    const db = new Database();

    console.log('🔧 Inicializando base de datos MySQL...\n');

    try {
        // Crear tabla profiles
        await db.runSimpleQuery(`
            CREATE TABLE IF NOT EXISTS profiles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                profile VARCHAR(100) NOT NULL,
                permissions TEXT,
                state TINYINT DEFAULT 1
            )
        `);
        console.log('✅ Tabla profiles verificada');

        // Crear tabla document_type
        await db.runSimpleQuery(`
            CREATE TABLE IF NOT EXISTS document_type (
                id INT AUTO_INCREMENT PRIMARY KEY,
                document VARCHAR(50) NOT NULL
            )
        `);
        console.log('✅ Tabla document_type verificada');

        // Insertar tipos de documento
        const documents = [
            [1, 'Sin documento'],
            [2, 'DNI'],
            [3, 'RUC'],
            [4, 'Carnet de Extranjería'],
            [5, 'Pasaporte']
        ];

        for (const doc of documents) {
            await db.runSimpleQuery(`INSERT IGNORE INTO document_type (id, document) VALUES (${doc[0]}, '${doc[1]}')`);
        }
        console.log('✅ Tipos de documento insertados');

        // Insertar perfiles
        await db.runSimpleQuery(`INSERT IGNORE INTO profiles (id, profile, permissions, state) VALUES (1, 'Administrador', '{}', 1)`);
        await db.runSimpleQuery(`INSERT IGNORE INTO profiles (id, profile, permissions, state) VALUES (2, 'Técnico', '{}', 1)`);
        await db.runSimpleQuery(`INSERT IGNORE INTO profiles (id, profile, permissions, state) VALUES (3, 'Cobrador', '{}', 1)`);
        console.log('✅ Perfiles insertados');

        // Crear tabla users
        await db.runSimpleQuery(`
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                names VARCHAR(100) NOT NULL,
                surnames VARCHAR(100) NOT NULL,
                documentid INT DEFAULT 1,
                document VARCHAR(20),
                mobile VARCHAR(20),
                email VARCHAR(100),
                profileid INT NOT NULL,
                username VARCHAR(50) NOT NULL,
                password VARCHAR(255) NOT NULL,
                image VARCHAR(255) DEFAULT 'default.png',
                registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                state TINYINT DEFAULT 1,
                token VARCHAR(255)
            )
        `);
        console.log('✅ Tabla users verificada');

        // Encriptar contraseña
        const password = 'admin';
        const encryptedPassword = helpers.encrypt(password);
        console.log(`\n🔐 Contraseña: ${password}`);
        console.log(`🔑 Hash generado: ${encryptedPassword}`);

        // Verificar si el usuario admin ya existe
        const existingUser = await db.select(`SELECT id FROM users WHERE username = 'admin'`);

        if (existingUser) {
            // Actualizar contraseña
            await db.runSimpleQuery(`UPDATE users SET password = '${encryptedPassword}' WHERE username = 'admin'`);
            console.log('\n✅ Usuario admin actualizado con nueva contraseña');
        } else {
            // Insertar usuario admin
            await db.runSimpleQuery(`
                INSERT INTO users (names, surnames, documentid, document, mobile, email, profileid, username, password, image, state) 
                VALUES ('Administrador', 'Sistema', 1, '00000000', '999999999', 'admin@sistema.local', 1, 'admin', '${encryptedPassword}', 'default.png', 1)
            `);
            console.log('\n✅ Usuario admin creado');
        }

        // Crear tabla business
        await db.runSimpleQuery(`
            CREATE TABLE IF NOT EXISTS business (
                id INT AUTO_INCREMENT PRIMARY KEY,
                business_name VARCHAR(200),
                ruc VARCHAR(20),
                address TEXT,
                mobile VARCHAR(20),
                email VARCHAR(100),
                password VARCHAR(255),
                server_host VARCHAR(100),
                port INT,
                logo_login VARCHAR(255) DEFAULT 'superwisp_white.png',
                logo_system VARCHAR(255) DEFAULT 'superwisp_white.png',
                favicon VARCHAR(255) DEFAULT 'favicon.png',
                logo_email VARCHAR(255),
                background VARCHAR(255) DEFAULT 'bg-1.jpeg',
                igv DECIMAL(5,2) DEFAULT 18.00,
                token_document VARCHAR(255)
            )
        `);
        console.log('✅ Tabla business verificada');

        // Insertar empresa por defecto
        const existingBusiness = await db.select(`SELECT id FROM business WHERE id = 1`);
        if (!existingBusiness) {
            await db.runSimpleQuery(`
                INSERT INTO business (id, business_name, ruc, address, mobile, email) 
                VALUES (1, 'Mi Empresa WISP', '00000000000', 'Dirección de la empresa', '999999999', 'info@empresa.com')
            `);
            console.log('✅ Empresa por defecto creada');
        }

        // Crear tabla zones
        await db.runSimpleQuery(`
            CREATE TABLE IF NOT EXISTS zones (
                id INT AUTO_INCREMENT PRIMARY KEY,
                zone VARCHAR(100) NOT NULL,
                state TINYINT DEFAULT 1
            )
        `);
        await db.runSimpleQuery(`INSERT IGNORE INTO zones (id, zone, state) VALUES (1, 'Zona Centro', 1)`);
        console.log('✅ Tabla zones verificada');

        // Crear tabla clients
        await db.runSimpleQuery(`
            CREATE TABLE IF NOT EXISTS clients (
                id INT AUTO_INCREMENT PRIMARY KEY,
                names VARCHAR(100) NOT NULL,
                surnames VARCHAR(100) NOT NULL,
                documentid INT DEFAULT 1,
                document VARCHAR(20),
                mobile VARCHAR(20),
                email VARCHAR(100),
                address TEXT,
                zoneid INT,
                latitude VARCHAR(50),
                longitude VARCHAR(50),
                contract_date DATE,
                contract_status TINYINT DEFAULT 1,
                registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                state TINYINT DEFAULT 1,
                ap_cliente_id INT,
                nap_cliente_id INT
            )
        `);
        console.log('✅ Tabla clients verificada');

        // Crear tabla permissions
        await db.runSimpleQuery(`
            CREATE TABLE IF NOT EXISTS permissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                profileid INT NOT NULL,
                moduleid INT NOT NULL,
                r TINYINT DEFAULT 1,
                w TINYINT DEFAULT 1,
                u TINYINT DEFAULT 1,
                d TINYINT DEFAULT 1,
                UNIQUE(profileid, moduleid)
            )
        `);

        // Permisos para administrador
        for (let i = 1; i <= 20; i++) {
            await db.runSimpleQuery(`INSERT IGNORE INTO permissions (profileid, moduleid, r, w, u, d) VALUES (1, ${i}, 1, 1, 1, 1)`);
        }
        console.log('✅ Permisos del administrador configurados');

        // Crear tablas adicionales
        await db.runSimpleQuery(`
            CREATE TABLE IF NOT EXISTS bills (
                id INT AUTO_INCREMENT PRIMARY KEY,
                clientid INT NOT NULL,
                amount DECIMAL(10,2),
                remaining_amount DECIMAL(10,2),
                issue_date DATE,
                due_date DATE,
                type TINYINT DEFAULT 1,
                state TINYINT DEFAULT 1
            )
        `);

        await db.runSimpleQuery(`
            CREATE TABLE IF NOT EXISTS payments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                clientid INT NOT NULL,
                billid INT,
                amount DECIMAL(10,2),
                payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                payment_method VARCHAR(50),
                state TINYINT DEFAULT 1
            )
        `);

        await db.runSimpleQuery(`
            CREATE TABLE IF NOT EXISTS tickets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                clientid INT NOT NULL,
                subject VARCHAR(255),
                description TEXT,
                priority TINYINT DEFAULT 1,
                assigned_to INT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                closed_at DATETIME,
                state TINYINT DEFAULT 1
            )
        `);
        console.log('✅ Tablas adicionales creadas');

        console.log('\n========================================');
        console.log('🎉 ¡Base de datos MySQL inicializada!');
        console.log('========================================');
        console.log('\n📌 Credenciales de acceso:');
        console.log('   Usuario: admin');
        console.log('   Contraseña: admin');
        console.log('\n🚀 Ahora puede iniciar el servidor con: npm start');

    } catch (error) {
        console.error('❌ Error:', error.message);
        console.error(error);
    } finally {
        await pool.end();
        process.exit(0);
    }
}

initDatabase();
