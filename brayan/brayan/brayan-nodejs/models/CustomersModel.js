const { Database } = require('../config/database');

class CustomersModel extends Database {
    constructor() {
        super();
    }

    async listRecords() {
        const sql = `SELECT c.*, d.document AS name_doc, z.zone,
            CASE c.contract_status 
                WHEN 1 THEN 'Activo' 
                WHEN 2 THEN 'Suspendido' 
                WHEN 3 THEN 'Cortado' 
                ELSE 'N/A' 
            END AS status_name
            FROM clients c
            LEFT JOIN document_type d ON c.documentid = d.id
            LEFT JOIN zones z ON c.zoneid = z.id
            WHERE c.state != 0
            ORDER BY c.id DESC`;
        return await this.selectAll(sql);
    }

    async selectRecord(id) {
        const sql = `SELECT * FROM clients WHERE id = ?`;
        return await this.select(sql, [id]);
    }

    async create(data) {
        // Verificar si el documento ya existe
        const checkSql = `SELECT id FROM clients WHERE document = ?`;
        const exists = await this.select(checkSql, [data.document]);

        if (exists) {
            return 'exists';
        }

        const sql = `INSERT INTO clients (
            names, surnames, documentid, document, mobile, email, address, 
            zoneid, latitude, longitude, contract_date, contract_status, 
            registration_date, state
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 1)`;

        const values = [
            data.names, data.surnames, data.documentid, data.document,
            data.mobile, data.email, data.address, data.zoneid,
            data.latitude, data.longitude, data.contract_date, data.contract_status || 1
        ];

        const result = await this.insert(sql, values);
        return result > 0 ? 'success' : 'error';
    }

    async modify(data) {
        // Verificar duplicado excluyendo el registro actual
        const checkSql = `SELECT id FROM clients WHERE document = ? AND id != ?`;
        const exists = await this.select(checkSql, [data.document, data.id]);

        if (exists) {
            return 'exists';
        }

        const sql = `UPDATE clients SET 
            names = ?, surnames = ?, documentid = ?, document = ?, 
            mobile = ?, email = ?, address = ?, zoneid = ?,
            latitude = ?, longitude = ?, contract_date = ?, contract_status = ?
            WHERE id = ?`;

        const values = [
            data.names, data.surnames, data.documentid, data.document,
            data.mobile, data.email, data.address, data.zoneid,
            data.latitude, data.longitude, data.contract_date, data.contract_status,
            data.id
        ];

        const result = await this.update(sql, values);
        return result ? 'success' : 'error';
    }

    async remove(id) {
        const sql = `UPDATE clients SET state = 0 WHERE id = ?`;
        const result = await this.update(sql, [id]);
        return result ? 'success' : 'error';
    }

    async listZones() {
        const sql = `SELECT * FROM zones WHERE state = 1`;
        return await this.selectAll(sql);
    }

    async listDocuments() {
        const sql = `SELECT * FROM document_type`;
        return await this.selectAll(sql);
    }
}

module.exports = CustomersModel;
