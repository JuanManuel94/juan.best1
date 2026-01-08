const { pool: db } = require('../config/database');

class SettingsModel {
    // ==================== Settings ====================
    static async getAllSettings() {
        const [rows] = await db.query('SELECT * FROM settings ORDER BY category, setting_key');
        return rows;
    }

    static async getSettingsByCategory(category) {
        const [rows] = await db.query('SELECT * FROM settings WHERE category = ?', [category]);
        return rows;
    }

    static async getSetting(key) {
        const [rows] = await db.query('SELECT * FROM settings WHERE setting_key = ?', [key]);
        return rows[0];
    }

    static async getSettingValue(key, defaultValue = null) {
        const setting = await this.getSetting(key);
        if (!setting) return defaultValue;

        switch (setting.setting_type) {
            case 'number':
                return parseFloat(setting.setting_value);
            case 'boolean':
                return setting.setting_value === 'true';
            case 'json':
                try {
                    return JSON.parse(setting.setting_value);
                } catch {
                    return defaultValue;
                }
            default:
                return setting.setting_value;
        }
    }

    static async updateSetting(key, value) {
        const [result] = await db.query(
            'UPDATE settings SET setting_value = ? WHERE setting_key = ?',
            [String(value), key]
        );
        return result;
    }

    static async createSetting(data) {
        const [result] = await db.query(
            'INSERT INTO settings (setting_key, setting_value, setting_type, category, description) VALUES (?, ?, ?, ?, ?)',
            [data.setting_key, data.setting_value, data.setting_type || 'string', data.category, data.description]
        );
        return result;
    }

    static async updateMultipleSettings(settings) {
        const connection = await db.getConnection();
        try {
            await connection.beginTransaction();
            for (const [key, value] of Object.entries(settings)) {
                await connection.query(
                    'UPDATE settings SET setting_value = ? WHERE setting_key = ?',
                    [String(value), key]
                );
            }
            await connection.commit();
            return { success: true };
        } catch (error) {
            await connection.rollback();
            throw error;
        } finally {
            connection.release();
        }
    }

    // ==================== Business/Company ====================
    static async getBusinessData() {
        const [rows] = await db.query('SELECT * FROM business WHERE id = 1');
        return rows[0];
    }

    static async updateBusinessData(data) {
        const [result] = await db.query(
            `UPDATE business SET 
                business_name = ?, ruc = ?, address = ?, mobile = ?, email = ?, 
                server_host = ?, port = ?, igv = ?
            WHERE id = 1`,
            [data.business_name, data.ruc, data.address, data.mobile, data.email,
            data.server_host, data.port, data.igv]
        );
        return result;
    }

    // ==================== API/Webhooks ====================
    static async getAPISettings() {
        const [rows] = await db.query("SELECT * FROM settings WHERE category = 'integrations'");
        return rows;
    }
}

module.exports = SettingsModel;
