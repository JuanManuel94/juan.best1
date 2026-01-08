const { Database } = require('../config/database');

class DashboardModel extends Database {
    constructor() {
        super();
    }

    async countClients() {
        const sql = `SELECT COUNT(id) AS total FROM clients WHERE state = 1`;
        return await this.select(sql);
    }

    async countActiveClients() {
        const sql = `SELECT COUNT(id) AS total FROM clients WHERE contract_status = 1 AND state = 1`;
        return await this.select(sql);
    }

    async countSuspendedClients() {
        const sql = `SELECT COUNT(id) AS total FROM clients WHERE contract_status = 2 AND state = 1`;
        return await this.select(sql);
    }

    async countPendingBills() {
        const sql = `SELECT COUNT(id) AS total FROM bills WHERE state = 2`;
        return await this.select(sql);
    }

    async countPaidBills() {
        const sql = `SELECT COUNT(id) AS total FROM bills WHERE state = 1`;
        return await this.select(sql);
    }

    async countOpenTickets() {
        const sql = `SELECT COUNT(id) AS total FROM tickets WHERE state = 1`;
        return await this.select(sql);
    }

    async getTotalIncome() {
        const sql = `SELECT COALESCE(SUM(amount), 0) AS total FROM payments WHERE MONTH(payment_date) = MONTH(NOW()) AND YEAR(payment_date) = YEAR(NOW())`;
        return await this.select(sql);
    }

    async getMonthlyData() {
        const sql = `SELECT 
            MONTH(payment_date) as month,
            COALESCE(SUM(amount), 0) as total
            FROM payments 
            WHERE YEAR(payment_date) = YEAR(NOW())
            GROUP BY MONTH(payment_date)
            ORDER BY month`;
        return await this.selectAll(sql);
    }

    async getRecentPayments() {
        const sql = `SELECT p.*, c.names, c.surnames 
            FROM payments p
            JOIN clients c ON p.clientid = c.id
            ORDER BY p.id DESC LIMIT 10`;
        return await this.selectAll(sql);
    }

    async getRecentTickets() {
        const sql = `SELECT t.*, c.names, c.surnames 
            FROM tickets t
            JOIN clients c ON t.clientid = c.id
            ORDER BY t.id DESC LIMIT 10`;
        return await this.selectAll(sql);
    }
}

module.exports = DashboardModel;
