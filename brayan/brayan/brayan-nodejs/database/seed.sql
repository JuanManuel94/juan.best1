-- Script SQL para crear usuario administrador por defecto
-- Usuario: admin
-- Contraseña: Admin123!

-- Crear tabla profiles si no existe
CREATE TABLE IF NOT EXISTS profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile VARCHAR(100) NOT NULL,
    permissions TEXT,
    state TINYINT DEFAULT 1
);

-- Crear tabla document_type si no existe
CREATE TABLE IF NOT EXISTS document_type (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document VARCHAR(50) NOT NULL
);

-- Insertar tipos de documento básicos
INSERT IGNORE INTO document_type (id, document) VALUES 
(1, 'Sin documento'),
(2, 'DNI'),
(3, 'RUC'),
(4, 'Carnet de Extranjería'),
(5, 'Pasaporte');

-- Insertar perfil administrador
INSERT INTO profiles (id, profile, permissions, state) 
VALUES (1, 'Administrador', '{}', 1)
ON DUPLICATE KEY UPDATE profile = 'Administrador';

-- Insertar perfil técnico
INSERT INTO profiles (id, profile, permissions, state) 
VALUES (2, 'Técnico', '{}', 1)
ON DUPLICATE KEY UPDATE profile = 'Técnico';

-- Insertar perfil cobrador
INSERT INTO profiles (id, profile, permissions, state) 
VALUES (3, 'Cobrador', '{}', 1)
ON DUPLICATE KEY UPDATE profile = 'Cobrador';

-- Crear tabla users si no existe
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    names VARCHAR(100) NOT NULL,
    surnames VARCHAR(100) NOT NULL,
    documentid INT DEFAULT 1,
    document VARCHAR(20),
    mobile VARCHAR(20),
    email VARCHAR(100),
    profileid INT NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    image VARCHAR(255) DEFAULT 'default.png',
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    state TINYINT DEFAULT 1,
    token VARCHAR(255),
    FOREIGN KEY (profileid) REFERENCES profiles(id),
    FOREIGN KEY (documentid) REFERENCES document_type(id)
);

-- Insertar usuario administrador por defecto
-- La contraseña 'Admin123!' está encriptada con AES-256-CBC usando la clave 'SISTWISP'
-- Hash generado: bUE2dHhrcFNZUzVxQmJjQVRBamJ5dz09
INSERT INTO users (id, names, surnames, documentid, document, mobile, email, profileid, username, password, image, registration_date, state, token)
VALUES (
    1, 
    'Administrador', 
    'Sistema', 
    1, 
    '00000000', 
    '999999999', 
    'admin@sistema.local', 
    1, 
    'admin', 
    'bUE2dHhrcFNZUzVxQmJjQVRBamJ5dz09', 
    'default.png', 
    NOW(), 
    1, 
    ''
)
ON DUPLICATE KEY UPDATE id = id;

-- Crear tabla business si no existe
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
);

-- Insertar datos de empresa por defecto
INSERT INTO business (id, business_name, ruc, address, mobile, email, logo_login, logo_system, favicon, background, igv)
VALUES (1, 'Mi Empresa WISP', '00000000000', 'Dirección de la empresa', '999999999', 'info@empresa.com', 'superwisp_white.png', 'superwisp_white.png', 'favicon.png', 'bg-1.jpeg', 18.00)
ON DUPLICATE KEY UPDATE id = id;

-- Crear tabla zones si no existe
CREATE TABLE IF NOT EXISTS zones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    zone VARCHAR(100) NOT NULL,
    state TINYINT DEFAULT 1
);

-- Insertar zona por defecto
INSERT INTO zones (id, zone, state) VALUES (1, 'Zona Centro', 1)
ON DUPLICATE KEY UPDATE id = id;

-- Crear tabla clients si no existe
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
    nap_cliente_id INT,
    FOREIGN KEY (documentid) REFERENCES document_type(id),
    FOREIGN KEY (zoneid) REFERENCES zones(id)
);

-- Crear tabla permissions si no existe
CREATE TABLE IF NOT EXISTS permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profileid INT NOT NULL,
    moduleid INT NOT NULL,
    r TINYINT DEFAULT 1,
    w TINYINT DEFAULT 1,
    u TINYINT DEFAULT 1,
    d TINYINT DEFAULT 1,
    FOREIGN KEY (profileid) REFERENCES profiles(id)
);

-- Crear permisos completos para el administrador
INSERT IGNORE INTO permissions (profileid, moduleid, r, w, u, d) VALUES
(1, 1, 1, 1, 1, 1),  -- Dashboard
(1, 2, 1, 1, 1, 1),  -- Clients
(1, 3, 1, 1, 1, 1),  -- Users
(1, 4, 1, 1, 1, 1),  -- Tickets
(1, 5, 1, 1, 1, 1),  -- Incidents
(1, 6, 1, 1, 1, 1),  -- Bills
(1, 7, 1, 1, 1, 1),  -- Products
(1, 8, 1, 1, 1, 1),  -- Categories
(1, 9, 1, 1, 1, 1),  -- Suppliers
(1, 10, 1, 1, 1, 1), -- Payments
(1, 11, 1, 1, 1, 1), -- Services
(1, 12, 1, 1, 1, 1), -- Business
(1, 13, 1, 1, 1, 1), -- Installations
(1, 14, 1, 1, 1, 1), -- Currencies
(1, 15, 1, 1, 1, 1), -- Runway
(1, 16, 1, 1, 1, 1), -- Vouchers
(1, 17, 1, 1, 1, 1), -- Units
(1, 18, 1, 1, 1, 1), -- Email
(1, 19, 1, 1, 1, 1), -- Red
(1, 20, 1, 1, 1, 1); -- WhatsApp

-- Crear tablas adicionales necesarias
CREATE TABLE IF NOT EXISTS bills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clientid INT NOT NULL,
    amount DECIMAL(10,2),
    remaining_amount DECIMAL(10,2),
    issue_date DATE,
    due_date DATE,
    type TINYINT DEFAULT 1,
    state TINYINT DEFAULT 1,
    FOREIGN KEY (clientid) REFERENCES clients(id)
);

CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clientid INT NOT NULL,
    billid INT,
    amount DECIMAL(10,2),
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    payment_method VARCHAR(50),
    state TINYINT DEFAULT 1,
    FOREIGN KEY (clientid) REFERENCES clients(id)
);

CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clientid INT NOT NULL,
    subject VARCHAR(255),
    description TEXT,
    priority TINYINT DEFAULT 1,
    assigned_to INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    closed_at DATETIME,
    state TINYINT DEFAULT 1,
    FOREIGN KEY (clientid) REFERENCES clients(id)
);

-- Mensaje de confirmación
SELECT 'Base de datos inicializada correctamente. Usuario admin creado.' as mensaje;
