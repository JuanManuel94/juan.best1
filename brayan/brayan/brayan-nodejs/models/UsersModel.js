const { Database } = require('../config/database');
const helpers = require('../helpers/helpers');

class UsersModel extends Database {
    constructor() {
        super();
    }

    async listRecords(userId) {
        const where = userId !== 1 ? "AND u.id != 1" : "";
        const sql = `SELECT u.id, u.names, u.surnames, d.document AS name_doc, u.document, 
            u.mobile, u.email, u.profileid, p.profile, u.username, u.password, 
            u.image, u.registration_date, u.state
            FROM users u
            JOIN document_type d ON u.documentid = d.id
            JOIN profiles p ON u.profileid = p.id
            WHERE u.state != 0 ${where}
            ORDER BY u.id DESC`;
        return await this.selectAll(sql);
    }

    async selectRecord(id) {
        const sql = `SELECT * FROM users WHERE id = ?`;
        return await this.select(sql, [id]);
    }

    async create(data) {
        // Verificar si el documento ya existe
        const checkSql = `SELECT id FROM users WHERE document = ?`;
        const exists = await this.select(checkSql, [data.document]);

        if (exists) {
            return 'exists';
        }

        const sql = `INSERT INTO users (
            names, surnames, documentid, document, mobile, email, 
            profileid, username, password, image, registration_date, state
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)`;

        const values = [
            data.names, data.surnames, data.documentid, data.document,
            data.mobile, data.email, data.profileid, data.username,
            data.password, data.image || 'default.png', data.state || 1
        ];

        const result = await this.insert(sql, values);
        return result > 0 ? 'success' : 'error';
    }

    async modify(data) {
        // Verificar duplicado excluyendo el registro actual
        const checkSql = `SELECT id FROM users WHERE document = ? AND id != ?`;
        const exists = await this.select(checkSql, [data.document, data.id]);

        if (exists) {
            return 'exists';
        }

        let sql, values;

        if (data.password) {
            sql = `UPDATE users SET 
                names = ?, surnames = ?, documentid = ?, document = ?, 
                mobile = ?, email = ?, profileid = ?, username = ?, 
                password = ?, state = ?
                WHERE id = ?`;
            values = [
                data.names, data.surnames, data.documentid, data.document,
                data.mobile, data.email, data.profileid, data.username,
                data.password, data.state, data.id
            ];
        } else {
            sql = `UPDATE users SET 
                names = ?, surnames = ?, documentid = ?, document = ?, 
                mobile = ?, email = ?, profileid = ?, username = ?, state = ?
                WHERE id = ?`;
            values = [
                data.names, data.surnames, data.documentid, data.document,
                data.mobile, data.email, data.profileid, data.username,
                data.state, data.id
            ];
        }

        const result = await this.update(sql, values);
        return result ? 'success' : 'error';
    }

    async remove(id) {
        const sql = `DELETE FROM users WHERE id = ?`;
        const result = await this.delete(sql, [id]);
        return result ? 'success' : 'error';
    }

    async listDocuments() {
        const sql = `SELECT * FROM document_type WHERE id IN (2, 4, 5)`;
        return await this.selectAll(sql);
    }

    async listProfiles() {
        const sql = `SELECT * FROM profiles WHERE state = 1`;
        return await this.selectAll(sql);
    }

    async modifyData(id, names, surnames, mobile, email) {
        const sql = `UPDATE users SET names = ?, surnames = ?, mobile = ?, email = ? WHERE id = ?`;
        const result = await this.update(sql, [names, surnames, mobile, email, id]);
        return result ? 'success' : 'error';
    }

    async modifyPassword(id, password) {
        const sql = `UPDATE users SET password = ? WHERE id = ?`;
        const result = await this.update(sql, [password, id]);
        return result ? 'success' : 'error';
    }

    async changeProfile(id, image) {
        const sql = `UPDATE users SET image = ? WHERE id = ?`;
        const result = await this.update(sql, [image, id]);
        return result ? 'success' : 'error';
    }
}

module.exports = UsersModel;
