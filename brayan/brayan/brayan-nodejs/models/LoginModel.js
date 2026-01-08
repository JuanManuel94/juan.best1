const { Database } = require('../config/database');
const helpers = require('../helpers/helpers');

class LoginModel extends Database {
    constructor() {
        super();
    }

    async validation(username, password) {
        const sql = `SELECT id, state FROM users WHERE username = ? AND password = ? AND state != 0`;
        return await this.select(sql, [username, password]);
    }

    async loginSession(id) {
        const sql = `SELECT u.id, u.names, u.surnames, u.documentid, u.document, u.mobile, u.email, 
                     u.profileid, p.profile, u.username, u.password, u.image, u.state
                     FROM users u
                     JOIN profiles p ON u.profileid = p.id
                     WHERE u.id = ?`;
        return await this.select(sql, [id]);
    }

    async validationEmail(email) {
        const sql = `SELECT id, names, surnames FROM users WHERE email = ? AND state = 1`;
        return await this.select(sql, [email]);
    }

    async updateToken(id, token) {
        const sql = `UPDATE users SET token = ? WHERE id = ?`;
        const result = await this.update(sql, [token, id]);
        return result ? 'success' : 'error';
    }

    async userInformation(email, token) {
        const sql = `SELECT id, names, surnames FROM users WHERE email = ? AND token = ? AND state = 1`;
        return await this.select(sql, [email, token]);
    }

    async updatePassword(id, password) {
        const sql = `UPDATE users SET password = ?, token = ? WHERE id = ?`;
        const result = await this.update(sql, [password, '', id]);
        return result ? 'success' : 'error';
    }
}

module.exports = LoginModel;
