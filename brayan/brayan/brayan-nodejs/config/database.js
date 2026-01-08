const mysql = require('mysql2');
const config = require('./config');

// Pool de conexiones MySQL
const pool = mysql.createPool({
    host: config.DB_HOST,
    user: config.DB_USER,
    password: config.DB_PASSWORD,
    database: config.DB_NAME,
    port: config.DB_PORT || 3306,
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

// Convertir pool a promesas
const promisePool = pool.promise();

class Database {
    constructor(tableName = "") {
        this.tableName = tableName;
        this.connection = null;
    }

    setTableName(name) {
        this.tableName = name;
        return this;
    }

    async getConnection() {
        return await promisePool.getConnection();
    }

    async createQueryRunner() {
        this.connection = await promisePool.getConnection();
        await this.connection.beginTransaction();
    }

    async commit() {
        if (this.connection) {
            await this.connection.commit();
            this.connection.release();
            this.connection = null;
        }
    }

    async rollback() {
        if (this.connection) {
            await this.connection.rollback();
            this.connection.release();
            this.connection = null;
        }
    }

    async insert(query, values = []) {
        try {
            const conn = this.connection || promisePool;
            const [result] = await conn.query(query, values);
            return result.insertId;
        } catch (error) {
            console.error('Insert error:', error);
            return 0;
        }
    }

    async insertMassive(columns = [], collection = []) {
        const columnStr = columns.join(", ");
        let query = `INSERT INTO ${this.tableName}(${columnStr}) VALUES ?`;

        // Preparar valores para inserción masiva: array de arrays
        const values = collection.map(row => columns.map(col => row[col]));

        try {
            const conn = this.connection || promisePool;
            const [result] = await conn.query(query, [values]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error('InsertMassive error:', error);
            return false;
        }
    }

    async insertObject(columns = [], row = {}) {
        const columnStr = columns.join(", ");
        const placeholders = columns.map(() => '?').join(',');
        const query = `INSERT INTO ${this.tableName}(${columnStr}) VALUES (${placeholders})`;
        const values = columns.map(col => row[col]);

        return await this.insert(query, values);
    }

    async select(query, values = []) {
        try {
            const conn = this.connection || promisePool;
            const [rows] = await conn.query(query, values);
            return rows[0] || null;
        } catch (error) {
            console.error('Select error:', error);
            return null;
        }
    }

    async selectAll(query, values = []) {
        try {
            const conn = this.connection || promisePool;
            const [rows] = await conn.query(query, values);
            return rows;
        } catch (error) {
            console.error('SelectAll error:', error);
            return [];
        }
    }

    async update(query, values = []) {
        try {
            const conn = this.connection || promisePool;
            const [result] = await conn.query(query, values);
            return result.affectedRows > 0;
        } catch (error) {
            console.error('Update error:', error);
            return false;
        }
    }

    async delete(query, values = []) {
        try {
            const conn = this.connection || promisePool;
            const [result] = await conn.query(query, values);
            return result.affectedRows > 0;
        } catch (error) {
            console.error('Delete error:', error);
            return false;
        }
    }

    async runSimpleQuery(query) {
        try {
            const conn = this.connection || promisePool;
            const [rows] = await conn.query(query);
            return rows;
        } catch (error) {
            console.error('RunSimpleQuery error:', error);
            return null;
        }
    }
}

module.exports = { Database, pool: promisePool };
