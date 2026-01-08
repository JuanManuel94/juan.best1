const { Database } = require('../config/database');

class PermissionsModel extends Database {
    constructor() {
        super();
    }

    async modulePermissions(profileId) {
        const sql = `SELECT * FROM permissions WHERE profileid = ?`;
        const rows = await this.selectAll(sql, [profileId]);

        const permissions = {};
        for (const row of rows) {
            permissions[row.moduleid] = row;
        }
        return permissions;
    }
}

module.exports = PermissionsModel;
