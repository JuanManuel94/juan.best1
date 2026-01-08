-- =====================================================
-- MIGRACIÓN: Nuevas tablas para módulos del sistema
-- Ejecutar después de seed.sql
-- =====================================================

-- ==================== REDES ====================

-- Redes IPv4
CREATE TABLE IF NOT EXISTS ipv4_networks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    network VARCHAR(18) NOT NULL,
    gateway VARCHAR(15),
    netmask VARCHAR(15),
    dns_primary VARCHAR(15),
    dns_secondary VARCHAR(15),
    description VARCHAR(255),
    routerid INT,
    pool_name VARCHAR(100),
    total_ips INT DEFAULT 0,
    used_ips INT DEFAULT 0,
    state TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Routers (si no existe, o mejoramos)
CREATE TABLE IF NOT EXISTS routers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    ip_address VARCHAR(15) NOT NULL,
    api_port INT DEFAULT 8728,
    api_user VARCHAR(100),
    api_password VARCHAR(255),
    identity VARCHAR(100),
    version VARCHAR(50),
    board VARCHAR(100),
    uptime VARCHAR(100),
    last_connection DATETIME,
    status ENUM('online','offline','unknown') DEFAULT 'unknown',
    state TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- SmartOLT Devices
CREATE TABLE IF NOT EXISTS smartolt_devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    olt_name VARCHAR(100) NOT NULL,
    ip_address VARCHAR(15),
    api_url VARCHAR(255),
    api_key VARCHAR(255),
    serial_number VARCHAR(100),
    model VARCHAR(100),
    firmware VARCHAR(100),
    total_ports INT DEFAULT 0,
    status ENUM('online','offline','unknown') DEFAULT 'unknown',
    last_sync DATETIME,
    state TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ONU/ONT Clients (para SmartOLT)
CREATE TABLE IF NOT EXISTS onu_clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clientid INT,
    oltid INT,
    onu_serial VARCHAR(50),
    onu_type VARCHAR(50),
    port INT,
    slot INT,
    pon INT,
    rx_power DECIMAL(5,2),
    tx_power DECIMAL(5,2),
    status ENUM('online','offline','los','unknown') DEFAULT 'unknown',
    last_seen DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (clientid) REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (oltid) REFERENCES smartolt_devices(id) ON DELETE SET NULL
);

-- Monitoreo de Hosts
CREATE TABLE IF NOT EXISTS monitoring_hosts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    ip_address VARCHAR(15) NOT NULL,
    host_type ENUM('router','switch','olt','ap','server','other') DEFAULT 'other',
    check_interval INT DEFAULT 60,
    last_check DATETIME,
    last_up DATETIME,
    last_down DATETIME,
    status ENUM('up','down','unknown') DEFAULT 'unknown',
    uptime_percent DECIMAL(5,2) DEFAULT 100.00,
    notifications_enabled TINYINT DEFAULT 1,
    state TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Historial de Monitoreo
CREATE TABLE IF NOT EXISTS monitoring_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hostid INT NOT NULL,
    status ENUM('up','down') NOT NULL,
    response_time INT,
    checked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hostid) REFERENCES monitoring_hosts(id) ON DELETE CASCADE
);

-- ==================== CLIENTES ====================

-- Instalaciones
CREATE TABLE IF NOT EXISTS installations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clientid INT NOT NULL,
    scheduled_date DATE NOT NULL,
    scheduled_time TIME,
    technicianid INT,
    address TEXT,
    latitude VARCHAR(50),
    longitude VARCHAR(50),
    serviceid INT,
    equipment TEXT,
    notes TEXT,
    status ENUM('pending','scheduled','in_progress','completed','cancelled') DEFAULT 'pending',
    notification_sent TINYINT DEFAULT 0,
    completed_at DATETIME,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (clientid) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (technicianid) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Contratos
CREATE TABLE IF NOT EXISTS contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clientid INT NOT NULL,
    contract_number VARCHAR(50),
    serviceid INT,
    start_date DATE NOT NULL,
    end_date DATE,
    monthly_amount DECIMAL(10,2),
    installation_cost DECIMAL(10,2) DEFAULT 0,
    deposit DECIMAL(10,2) DEFAULT 0,
    terms TEXT,
    document_path VARCHAR(255),
    status ENUM('active','suspended','cancelled','expired') DEFAULT 'active',
    signed_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (clientid) REFERENCES clients(id) ON DELETE CASCADE
);

-- Anuncios
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    type ENUM('info','warning','promo','maintenance') DEFAULT 'info',
    target ENUM('all','zones','specific') DEFAULT 'all',
    target_zones TEXT,
    start_date DATE,
    end_date DATE,
    show_on_portal TINYINT DEFAULT 1,
    send_notification TINYINT DEFAULT 0,
    created_by INT,
    state TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Correos enviados
CREATE TABLE IF NOT EXISTS emails_sent (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clientid INT,
    recipient_email VARCHAR(255) NOT NULL,
    subject VARCHAR(255),
    body TEXT,
    template_used VARCHAR(100),
    status ENUM('sent','failed','pending') DEFAULT 'pending',
    sent_at DATETIME,
    error_message TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (clientid) REFERENCES clients(id) ON DELETE SET NULL
);

-- ==================== FINANZAS ====================

-- Transacciones (mejorada)
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('income','expense') NOT NULL,
    category VARCHAR(100),
    subcategory VARCHAR(100),
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    reference VARCHAR(100),
    payment_method ENUM('cash','transfer','card','check','other') DEFAULT 'cash',
    clientid INT,
    billid INT,
    paymentid INT,
    userid INT,
    transaction_date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (clientid) REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (userid) REFERENCES users(id) ON DELETE SET NULL
);

-- Otros Ingresos y Egresos
CREATE TABLE IF NOT EXISTS other_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('income','expense') NOT NULL,
    category VARCHAR(100),
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    receipt_number VARCHAR(50),
    supplierid INT,
    attachment_path VARCHAR(255),
    movement_date DATE NOT NULL,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ==================== ALMACÉN ====================

-- Categorías de productos
CREATE TABLE IF NOT EXISTS product_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    parent_id INT,
    state TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Proveedores
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    trade_name VARCHAR(200),
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

-- Productos (mejorada si no tiene categoryid)
-- Nota: Usando procedimiento para compatibilidad con MySQL < 8.0

-- Agregar columna categoryid si no existe
SET @exist_categoryid = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'categoryid');
SET @sql = IF(@exist_categoryid = 0, 'ALTER TABLE products ADD COLUMN categoryid INT', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna supplierid si no existe
SET @exist_supplierid = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'supplierid');
SET @sql = IF(@exist_supplierid = 0, 'ALTER TABLE products ADD COLUMN supplierid INT', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna min_stock si no existe
SET @exist_min_stock = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'min_stock');
SET @sql = IF(@exist_min_stock = 0, 'ALTER TABLE products ADD COLUMN min_stock INT DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna location si no existe
SET @exist_location = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'location');
SET @sql = IF(@exist_location = 0, 'ALTER TABLE products ADD COLUMN location VARCHAR(100)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ==================== TAREAS ====================

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
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (clientid) REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ==================== FICHAS HOTSPOT ====================

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

-- ==================== MENSAJERÍA ====================

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
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (clientid) REFERENCES clients(id) ON DELETE SET NULL
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

-- ==================== CONFIGURACIÓN ====================

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

-- Insertar categorías de productos por defecto
INSERT IGNORE INTO product_categories (id, name, description) VALUES
(1, 'Equipos de Red', 'Routers, switches, ONUs, etc.'),
(2, 'Cables y Conectores', 'Cables UTP, fibra óptica, conectores'),
(3, 'Herramientas', 'Herramientas de instalación y mantenimiento'),
(4, 'Accesorios', 'Cajas, postes, herrajes');

SELECT 'Migración completada exitosamente.' as mensaje;
