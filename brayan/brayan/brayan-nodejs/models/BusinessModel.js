const { Database } = require('../config/database');

class BusinessModel extends Database {
    constructor() {
        super();
    }

    async showBusiness() {
        const sql = `SELECT * FROM business WHERE id = 1`;
        return await this.select(sql);
    }

    async updateBusiness(data) {
        const sql = `UPDATE business SET 
            business_name = ?, ruc = ?, address = ?, mobile = ?, email = ?, 
            password = ?, server_host = ?, port = ?, logo_login = ?, 
            logo_system = ?, favicon = ?, logo_email = ?, background = ?, 
            igv = ?, token_document = ?
            WHERE id = 1`;

        const values = [
            data.business_name, data.ruc, data.address, data.mobile, data.email,
            data.password, data.server_host, data.port, data.logo_login,
            data.logo_system, data.favicon, data.logo_email, data.background,
            data.igv, data.token_document
        ];

        const result = await this.update(sql, values);
        return result ? 'success' : 'error';
    }
}

module.exports = BusinessModel;
