-- Script para agregar columnas adicionales a la tabla tasks
-- Ejecutar después de fix_missing_tables.sql

-- Agregar columna address si no existe
SET @exist_address = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tasks' AND COLUMN_NAME = 'address');
SET @sql = IF(@exist_address = 0, 'ALTER TABLE tasks ADD COLUMN address VARCHAR(255)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna latitude si no existe
SET @exist_lat = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tasks' AND COLUMN_NAME = 'latitude');
SET @sql = IF(@exist_lat = 0, 'ALTER TABLE tasks ADD COLUMN latitude VARCHAR(50)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna longitude si no existe
SET @exist_lng = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tasks' AND COLUMN_NAME = 'longitude');
SET @sql = IF(@exist_lng = 0, 'ALTER TABLE tasks ADD COLUMN longitude VARCHAR(50)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna duration_hours si no existe
SET @exist_dh = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tasks' AND COLUMN_NAME = 'duration_hours');
SET @sql = IF(@exist_dh = 0, 'ALTER TABLE tasks ADD COLUMN duration_hours INT DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna duration_minutes si no existe
SET @exist_dm = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tasks' AND COLUMN_NAME = 'duration_minutes');
SET @sql = IF(@exist_dm = 0, 'ALTER TABLE tasks ADD COLUMN duration_minutes INT DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'Columnas adicionales agregadas a tasks.' as mensaje;
