const { pool: db } = require('../config/database');

class TasksModel {

    static async getAllTasks(filters = {}) {
        let query = `
            SELECT t.*,
                   c.names as client_names, c.surnames as client_surnames,
                   u.names as assigned_names, u.surnames as assigned_surnames,
                   cr.names as creator_names, cr.surnames as creator_surnames
            FROM tasks t
            LEFT JOIN clients c ON t.clientid = c.id
            LEFT JOIN users u ON t.assigned_to = u.id
            LEFT JOIN users cr ON t.created_by = cr.id
            WHERE 1=1
        `;
        const params = [];

        if (filters.status) {
            query += ' AND t.status = ?';
            params.push(filters.status);
        }
        if (filters.priority) {
            query += ' AND t.priority = ?';
            params.push(filters.priority);
        }
        if (filters.assigned_to) {
            query += ' AND t.assigned_to = ?';
            params.push(filters.assigned_to);
        }
        if (filters.task_type) {
            query += ' AND t.task_type = ?';
            params.push(filters.task_type);
        }

        query += ' ORDER BY FIELD(t.priority, "urgent", "high", "medium", "low"), t.due_date ASC';

        const [rows] = await db.execute(query, params);
        return rows;
    }

    static async getTaskById(id) {
        const [rows] = await db.execute(
            `SELECT t.*, c.names as client_names, c.surnames as client_surnames
             FROM tasks t
             LEFT JOIN clients c ON t.clientid = c.id
             WHERE t.id = ?`,
            [id]
        );
        return rows[0];
    }

    static async createTask(data) {
        const [result] = await db.execute(
            `INSERT INTO tasks
             (title, description, task_type, assigned_to, clientid, ticketid,
              priority, status, due_date, due_time, address, latitude, longitude,
              duration_hours, duration_minutes, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
            [
                data.title, data.description, data.task_type,
                data.assigned_to || null, data.clientid || null, data.ticketid || null,
                data.priority || 'medium', data.status || 'pending',
                data.due_date, data.due_time || null,
                data.address || null, data.latitude || null, data.longitude || null,
                data.duration_hours || 0, data.duration_minutes || 0,
                data.created_by
            ]
        );
        return result;
    }

    static async updateTask(id, data) {
        const fields = [];
        const params = [];

        const allowedFields = ['title', 'description', 'task_type', 'assigned_to',
            'clientid', 'priority', 'status', 'due_date', 'due_time', 'completion_notes',
            'address', 'latitude', 'longitude', 'duration_hours', 'duration_minutes'];

        for (const field of allowedFields) {
            if (data[field] !== undefined) {
                fields.push(`${field} = ?`);
                params.push(data[field]);
            }
        }

        if (data.status === 'completed') {
            fields.push('completed_at = NOW()');
        }

        params.push(id);

        const [result] = await db.execute(
            `UPDATE tasks SET ${fields.join(', ')} WHERE id = ?`,
            params
        );
        return result;
    }

    static async deleteTask(id) {
        const [result] = await db.execute('DELETE FROM tasks WHERE id = ?', [id]);
        return result;
    }

    static async getTaskStats() {
        const [stats] = await db.execute(`
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN priority = 'urgent' AND status != 'completed' THEN 1 ELSE 0 END) as urgent
            FROM tasks
        `);
        return stats[0];
    }
}

module.exports = TasksModel;
