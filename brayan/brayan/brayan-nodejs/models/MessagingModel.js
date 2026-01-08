const { pool: db } = require('../config/database');

class MessagingModel {
    // ==================== WhatsApp Messages ====================
    static async getAllMessages(direction = null) {
        let query = `
            SELECT m.*, c.names, c.surnames 
            FROM whatsapp_messages m 
            LEFT JOIN clients c ON m.clientid = c.id 
        `;
        if (direction) {
            query += ` WHERE m.direction = '${direction}'`;
        }
        query += ' ORDER BY m.created_at DESC';
        const [rows] = await db.query(query);
        return rows;
    }

    static async getSentMessages() {
        return this.getAllMessages('sent');
    }

    static async getReceivedMessages() {
        return this.getAllMessages('received');
    }

    static async getMessageById(id) {
        const [rows] = await db.query('SELECT * FROM whatsapp_messages WHERE id = ?', [id]);
        return rows[0];
    }

    static async createMessage(data) {
        const [result] = await db.query(
            'INSERT INTO whatsapp_messages (direction, phone_number, clientid, message_text, message_type, status) VALUES (?, ?, ?, ?, ?, ?)',
            [data.direction, data.phone_number, data.clientid, data.message_text, data.message_type || 'text', data.status || 'pending']
        );
        return result;
    }

    static async updateMessageStatus(id, status) {
        const [result] = await db.query(
            'UPDATE whatsapp_messages SET status = ?, sent_at = NOW() WHERE id = ?',
            [status, id]
        );
        return result;
    }

    // ==================== WhatsApp Templates ====================
    static async getAllTemplates() {
        const [rows] = await db.query('SELECT * FROM whatsapp_templates WHERE state = 1 ORDER BY id DESC');
        return rows;
    }

    static async getTemplateById(id) {
        const [rows] = await db.query('SELECT * FROM whatsapp_templates WHERE id = ?', [id]);
        return rows[0];
    }

    static async createTemplate(data) {
        const [result] = await db.query(
            'INSERT INTO whatsapp_templates (name, category, message_template, variables) VALUES (?, ?, ?, ?)',
            [data.name, data.category, data.message_template, data.variables]
        );
        return result;
    }

    static async updateTemplate(id, data) {
        const [result] = await db.query(
            'UPDATE whatsapp_templates SET name = ?, category = ?, message_template = ?, variables = ? WHERE id = ?',
            [data.name, data.category, data.message_template, data.variables, id]
        );
        return result;
    }

    static async deleteTemplate(id) {
        const [result] = await db.query('UPDATE whatsapp_templates SET state = 0 WHERE id = ?', [id]);
        return result;
    }

    // ==================== Emails ====================
    static async getAllEmails() {
        const [rows] = await db.query(`
            SELECT e.*, c.names, c.surnames 
            FROM emails_sent e 
            LEFT JOIN clients c ON e.clientid = c.id 
            ORDER BY e.created_at DESC
        `);
        return rows;
    }

    static async getEmailById(id) {
        const [rows] = await db.query('SELECT * FROM emails_sent WHERE id = ?', [id]);
        return rows[0];
    }

    static async createEmail(data) {
        const [result] = await db.query(
            'INSERT INTO emails_sent (clientid, recipient_email, subject, body, template_used, status) VALUES (?, ?, ?, ?, ?, ?)',
            [data.clientid, data.recipient_email, data.subject, data.body, data.template_used, data.status || 'pending']
        );
        return result;
    }
}

module.exports = MessagingModel;
