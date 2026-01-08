const db = require('../config/database');

class FinanceModel {

    // ==================== TRANSACCIONES ====================

    static async getAllTransactions(filters = {}) {
        let query = `
            SELECT t.*, 
                   c.names as client_names, c.surnames as client_surnames,
                   u.names as user_names, u.surnames as user_surnames
            FROM transactions t
            LEFT JOIN clients c ON t.clientid = c.id
            LEFT JOIN users u ON t.userid = u.id
            WHERE 1=1
        `;
        const params = [];

        if (filters.type) {
            query += ' AND t.type = ?';
            params.push(filters.type);
        }
        if (filters.startDate) {
            query += ' AND t.transaction_date >= ?';
            params.push(filters.startDate);
        }
        if (filters.endDate) {
            query += ' AND t.transaction_date <= ?';
            params.push(filters.endDate);
        }
        if (filters.category) {
            query += ' AND t.category = ?';
            params.push(filters.category);
        }

        query += ' ORDER BY t.transaction_date DESC, t.id DESC';

        const [rows] = await db.execute(query, params);
        return rows;
    }

    static async getTransactionById(id) {
        const [rows] = await db.execute(
            `SELECT t.*, c.names as client_names, c.surnames as client_surnames
             FROM transactions t
             LEFT JOIN clients c ON t.clientid = c.id
             WHERE t.id = ?`,
            [id]
        );
        return rows[0];
    }

    static async createTransaction(data) {
        const [result] = await db.execute(
            `INSERT INTO transactions 
             (type, category, subcategory, amount, description, reference, 
              payment_method, clientid, billid, paymentid, userid, transaction_date)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
            [
                data.type, data.category, data.subcategory, data.amount,
                data.description, data.reference, data.payment_method,
                data.clientid || null, data.billid || null, data.paymentid || null,
                data.userid, data.transaction_date
            ]
        );
        return result;
    }

    // ==================== PAGOS ====================

    static async registerPayment(data) {
        const connection = await db.getConnection();
        try {
            await connection.beginTransaction();

            // Insertar pago
            const [paymentResult] = await connection.execute(
                `INSERT INTO payments (clientid, billid, amount, payment_date, payment_method, state)
                 VALUES (?, ?, ?, NOW(), ?, 1)`,
                [data.clientid, data.billid, data.amount, data.payment_method]
            );

            // Actualizar factura si existe
            if (data.billid) {
                await connection.execute(
                    `UPDATE bills SET remaining_amount = remaining_amount - ?,
                     state = CASE WHEN remaining_amount - ? <= 0 THEN 2 ELSE state END
                     WHERE id = ?`,
                    [data.amount, data.amount, data.billid]
                );
            }

            // Registrar transacción
            await connection.execute(
                `INSERT INTO transactions 
                 (type, category, amount, description, payment_method, clientid, billid, paymentid, userid, transaction_date)
                 VALUES ('income', 'Pago de servicio', ?, ?, ?, ?, ?, ?, ?, CURDATE())`,
                [data.amount, data.description || 'Pago de cliente', data.payment_method,
                data.clientid, data.billid, paymentResult.insertId, data.userid]
            );

            await connection.commit();
            return paymentResult;
        } catch (error) {
            await connection.rollback();
            throw error;
        } finally {
            connection.release();
        }
    }

    static async getClientPendingBills(clientid) {
        const [rows] = await db.execute(
            `SELECT * FROM bills WHERE clientid = ? AND state = 1 ORDER BY due_date ASC`,
            [clientid]
        );
        return rows;
    }

    // ==================== OTROS INGRESOS/EGRESOS ====================

    static async getAllOtherMovements(filters = {}) {
        let query = `
            SELECT om.*, u.names as user_names, u.surnames as user_surnames
            FROM other_movements om
            LEFT JOIN users u ON om.created_by = u.id
            WHERE 1=1
        `;
        const params = [];

        if (filters.type) {
            query += ' AND om.type = ?';
            params.push(filters.type);
        }
        if (filters.startDate) {
            query += ' AND om.movement_date >= ?';
            params.push(filters.startDate);
        }
        if (filters.endDate) {
            query += ' AND om.movement_date <= ?';
            params.push(filters.endDate);
        }

        query += ' ORDER BY om.movement_date DESC, om.id DESC';

        const [rows] = await db.execute(query, params);
        return rows;
    }

    static async createOtherMovement(data) {
        const [result] = await db.execute(
            `INSERT INTO other_movements 
             (type, category, amount, description, receipt_number, supplierid, movement_date, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
            [data.type, data.category, data.amount, data.description,
            data.receipt_number, data.supplierid || null, data.movement_date, data.created_by]
        );
        return result;
    }

    // ==================== REPORTES ====================

    static async getPaymentReport(startDate, endDate) {
        const [rows] = await db.execute(
            `SELECT 
                DATE(payment_date) as date,
                COUNT(*) as total_payments,
                SUM(amount) as total_amount,
                payment_method
             FROM payments
             WHERE payment_date BETWEEN ? AND ?
             GROUP BY DATE(payment_date), payment_method
             ORDER BY date DESC`,
            [startDate, endDate]
        );
        return rows;
    }

    static async getDailySummary(date) {
        const [income] = await db.execute(
            `SELECT COALESCE(SUM(amount), 0) as total FROM transactions 
             WHERE type = 'income' AND transaction_date = ?`,
            [date]
        );
        const [expense] = await db.execute(
            `SELECT COALESCE(SUM(amount), 0) as total FROM transactions 
             WHERE type = 'expense' AND transaction_date = ?`,
            [date]
        );
        return {
            income: income[0].total,
            expense: expense[0].total,
            balance: income[0].total - expense[0].total
        };
    }
}

module.exports = FinanceModel;
