const { pool: db } = require('../config/database');

class NetworkModel {
    // ==================== SmartOLT ====================
    static async getAllOLTs() {
        const [rows] = await db.query('SELECT * FROM smartolt_devices WHERE state = 1 ORDER BY id DESC');
        return rows;
    }

    static async getOLTById(id) {
        const [rows] = await db.query('SELECT * FROM smartolt_devices WHERE id = ?', [id]);
        return rows[0];
    }

    static async createOLT(data) {
        const [result] = await db.query(
            'INSERT INTO smartolt_devices (olt_name, ip_address, api_url, api_key, serial_number, model, firmware, total_ports, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [data.olt_name, data.ip_address, data.api_url, data.api_key, data.serial_number, data.model, data.firmware, data.total_ports || 0, data.status || 'unknown']
        );
        return result;
    }

    static async updateOLT(id, data) {
        const [result] = await db.query(
            'UPDATE smartolt_devices SET olt_name = ?, ip_address = ?, api_url = ?, api_key = ?, serial_number = ?, model = ?, firmware = ?, total_ports = ?, status = ? WHERE id = ?',
            [data.olt_name, data.ip_address, data.api_url, data.api_key, data.serial_number, data.model, data.firmware, data.total_ports, data.status, id]
        );
        return result;
    }

    static async deleteOLT(id) {
        const [result] = await db.query('UPDATE smartolt_devices SET state = 0 WHERE id = ?', [id]);
        return result;
    }

    // ==================== ONUs ====================
    static async getAllONUs() {
        const [rows] = await db.query(`
            SELECT o.*, c.names, c.surnames, s.olt_name 
            FROM onu_clients o 
            LEFT JOIN clients c ON o.clientid = c.id 
            LEFT JOIN smartolt_devices s ON o.oltid = s.id 
            ORDER BY o.id DESC
        `);
        return rows;
    }

    static async getONUById(id) {
        const [rows] = await db.query('SELECT * FROM onu_clients WHERE id = ?', [id]);
        return rows[0];
    }

    static async createONU(data) {
        const [result] = await db.query(
            'INSERT INTO onu_clients (clientid, oltid, onu_serial, onu_type, port, slot, pon, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [data.clientid, data.oltid, data.onu_serial, data.onu_type, data.port, data.slot, data.pon, data.status || 'unknown']
        );
        return result;
    }

    // ==================== Redes IPv4 ====================
    static async getAllNetworks() {
        const [rows] = await db.query(`
            SELECT n.*, r.name as router_name 
            FROM ipv4_networks n 
            LEFT JOIN routers r ON n.routerid = r.id 
            WHERE n.state = 1 
            ORDER BY n.id DESC
        `);
        return rows;
    }

    static async getNetworkById(id) {
        const [rows] = await db.query('SELECT * FROM ipv4_networks WHERE id = ?', [id]);
        return rows[0];
    }

    static async createNetwork(data) {
        const [result] = await db.query(
            'INSERT INTO ipv4_networks (network, gateway, netmask, dns_primary, dns_secondary, description, routerid, pool_name, total_ips, used_ips) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [data.network, data.gateway, data.netmask, data.dns_primary, data.dns_secondary, data.description, data.routerid, data.pool_name, data.total_ips || 0, data.used_ips || 0]
        );
        return result;
    }

    static async updateNetwork(id, data) {
        const [result] = await db.query(
            'UPDATE ipv4_networks SET network = ?, gateway = ?, netmask = ?, dns_primary = ?, dns_secondary = ?, description = ?, routerid = ?, pool_name = ?, total_ips = ?, used_ips = ? WHERE id = ?',
            [data.network, data.gateway, data.netmask, data.dns_primary, data.dns_secondary, data.description, data.routerid, data.pool_name, data.total_ips, data.used_ips, id]
        );
        return result;
    }

    static async deleteNetwork(id) {
        const [result] = await db.query('UPDATE ipv4_networks SET state = 0 WHERE id = ?', [id]);
        return result;
    }

    // ==================== Monitoreo ====================
    static async getAllHosts() {
        const [rows] = await db.query('SELECT * FROM monitoring_hosts WHERE state = 1 ORDER BY status DESC, id DESC');
        return rows;
    }

    static async getHostById(id) {
        const [rows] = await db.query('SELECT * FROM monitoring_hosts WHERE id = ?', [id]);
        return rows[0];
    }

    static async createHost(data) {
        const [result] = await db.query(
            'INSERT INTO monitoring_hosts (name, ip_address, host_type, check_interval, notifications_enabled) VALUES (?, ?, ?, ?, ?)',
            [data.name, data.ip_address, data.host_type, data.check_interval || 60, data.notifications_enabled || 1]
        );
        return result;
    }

    static async updateHost(id, data) {
        const [result] = await db.query(
            'UPDATE monitoring_hosts SET name = ?, ip_address = ?, host_type = ?, check_interval = ?, notifications_enabled = ? WHERE id = ?',
            [data.name, data.ip_address, data.host_type, data.check_interval, data.notifications_enabled, id]
        );
        return result;
    }

    static async deleteHost(id) {
        const [result] = await db.query('UPDATE monitoring_hosts SET state = 0 WHERE id = ?', [id]);
        return result;
    }

    static async getMonitoringHistory(hostId, limit = 100) {
        const [rows] = await db.query(
            'SELECT * FROM monitoring_history WHERE hostid = ? ORDER BY checked_at DESC LIMIT ?',
            [hostId, limit]
        );
        return rows;
    }

    static async addMonitoringRecord(hostId, status, responseTime) {
        const [result] = await db.query(
            'INSERT INTO monitoring_history (hostid, status, response_time) VALUES (?, ?, ?)',
            [hostId, status, responseTime]
        );
        return result;
    }

    // ==================== Routers ====================
    static async getAllRouters() {
        const [rows] = await db.query('SELECT * FROM routers WHERE state = 1 ORDER BY id DESC');
        return rows;
    }

    static async getRouterById(id) {
        const [rows] = await db.query('SELECT * FROM routers WHERE id = ?', [id]);
        return rows[0];
    }

    static async createRouter(data) {
        const [result] = await db.query(
            'INSERT INTO routers (name, ip_address, api_port, api_user, api_password, identity, version, board, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [data.name, data.ip_address, data.api_port || 8728, data.api_user, data.api_password, data.identity, data.version, data.board, data.status || 'unknown']
        );
        return result;
    }

    static async updateRouter(id, data) {
        const [result] = await db.query(
            'UPDATE routers SET name = ?, ip_address = ?, api_port = ?, api_user = ?, api_password = ?, identity = ?, version = ?, board = ?, status = ? WHERE id = ?',
            [data.name, data.ip_address, data.api_port, data.api_user, data.api_password, data.identity, data.version, data.board, data.status, id]
        );
        return result;
    }

    static async deleteRouter(id) {
        const [result] = await db.query('UPDATE routers SET state = 0 WHERE id = ?', [id]);
        return result;
    }
}

module.exports = NetworkModel;
