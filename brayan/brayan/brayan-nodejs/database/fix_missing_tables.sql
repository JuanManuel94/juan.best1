-- =====================================================
-- SCRIPT DE REPARACIÓN: Tablas faltantes
-- Ejecutar este script para crear las tablas que no se crearon
-- =====================================================

-- Tabla ROUTERS (para gestión de routers MikroTik)
CREATE TABLE IF NOT EXISTS routers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    api_port INT DEFAULT 8728,
    api_user VARCHAR(100),
    api_password VARCHAR(255),
    identity VARCHAR(100),
    version VARCHAR(50),
    board VARCHAR(100),
    status ENUM('online','offline','unknown') DEFAULT 'unknown',
    last_check DATETIME,
    state TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insertar router por defecto
INSERT IGNORE INTO routers (id, name, ip_address, api_port, api_user, status, state) VALUES
(1, 'Router Principal', '192.168.1.1', 8728, 'admin', 'unknown', 1);

-- Tabla PRODUCT_CATEGORIES (para categorías de productos de almacén)
CREATE TABLE IF NOT EXISTS product_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    parent_id INT,
    state TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES product_categories(id) ON DELETE SET NULL
);

-- Insertar categorías por defecto
INSERT IGNORE INTO product_categories (id, name, description, state) VALUES
(1, 'Fibra Óptica', 'Materiales de fibra óptica', 1),
(2, 'Equipos de Red', 'Routers, switches y accesorios', 1),
(3, 'Cables', 'Cables UTP, coaxial y otros', 1),
(4, 'Herramientas', 'Herramientas para instalación', 1);

-- Tabla SUPPLIERS (proveedores de productos)
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    trade_name VARCHAR(150),
    ruc VARCHAR(20),
    contact_name VARCHAR(100),
    phone VARCHAR(20),
    mobile VARCHAR(20),
    email VARCHAR(100),
    website VARCHAR(255),
    address TEXT,
    city VARCHAR(100),
    notes TEXT,
    payment_terms VARCHAR(100),
    state TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla PRODUCTS (productos de almacén)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    sku VARCHAR(50),
    barcode VARCHAR(50),
    description TEXT,
    categoryid INT,
    supplierid INT,
    unit VARCHAR(50) DEFAULT 'unidad',
    purchase_price DECIMAL(10,2) DEFAULT 0,
    sale_price DECIMAL(10,2) DEFAULT 0,
    stock INT DEFAULT 0,
    min_stock INT DEFAULT 0,
    location VARCHAR(100),
    image VARCHAR(255),
    state TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoryid) REFERENCES product_categories(id),
    FOREIGN KEY (supplierid) REFERENCES suppliers(id)
);

-- Tabla TASKS (para el módulo de tareas)
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    task_type ENUM('installation','support','maintenance','collection','other') DEFAULT 'other',
    assigned_to INT,
    clientid INT,
    ticketid INT,
    priority ENUM('low','medium','high','urgent') DEFAULT 'medium',
    status ENUM('pending','in_progress','completed','cancelled') DEFAULT 'pending',
    due_date DATE,
    due_time TIME,
    reminder_sent TINYINT DEFAULT 0,
    completed_at DATETIME,
    completion_notes TEXT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla SETTINGS (para configuración del sistema)
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('string','number','boolean','json') DEFAULT 'string',
    category VARCHAR(50),
    description VARCHAR(255),
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar configuraciones por defecto
INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, category, description) VALUES
('currency', 'PEN', 'string', 'general', 'Moneda del sistema'),
('currency_symbol', 'S/', 'string', 'general', 'Símbolo de moneda'),
('timezone', 'America/Lima', 'string', 'general', 'Zona horaria'),
('date_format', 'DD/MM/YYYY', 'string', 'general', 'Formato de fecha'),
('payment_reminder_days', '3', 'number', 'billing', 'Días antes del vencimiento para recordatorio'),
('late_payment_fee', '0', 'number', 'billing', 'Mora por pago tardío'),
('cut_service_days', '5', 'number', 'billing', 'Días de mora para corte'),
('google_maps_api_key', '', 'string', 'integrations', 'API Key de Google Maps'),
('smartolt_api_url', '', 'string', 'integrations', 'URL API SmartOLT'),
('smartolt_api_key', '', 'string', 'integrations', 'API Key SmartOLT'),
('whatsapp_enabled', 'false', 'boolean', 'notifications', 'Habilitar WhatsApp'),
('email_enabled', 'false', 'boolean', 'notifications', 'Habilitar correos'),
('sms_enabled', 'false', 'boolean', 'notifications', 'Habilitar SMS');

-- Tabla HOTSPOT_VOUCHERS (para fichas hotspot)
CREATE TABLE IF NOT EXISTS hotspot_vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    profile VARCHAR(100),
    time_limit INT,
    data_limit BIGINT,
    price DECIMAL(10,2),
    routerid INT,
    status ENUM('available','used','expired') DEFAULT 'available',
    used_by VARCHAR(100),
    used_at DATETIME,
    expires_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS hotspot_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    html_content TEXT,
    css_content TEXT,
    preview_image VARCHAR(255),
    is_default TINYINT DEFAULT 0,
    state TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para mensajes de WhatsApp
CREATE TABLE IF NOT EXISTS whatsapp_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    direction ENUM('sent','received') NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    clientid INT,
    message_text TEXT,
    media_url VARCHAR(255),
    message_type ENUM('text','image','document','audio','video') DEFAULT 'text',
    status ENUM('pending','sent','delivered','read','failed') DEFAULT 'pending',
    whatsapp_id VARCHAR(100),
    error_message TEXT,
    sent_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS whatsapp_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category ENUM('billing','reminder','notification','promo','support') DEFAULT 'notification',
    message_template TEXT NOT NULL,
    variables TEXT,
    state TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

SELECT 'Tablas faltantes creadas correctamente.' as mensaje;
