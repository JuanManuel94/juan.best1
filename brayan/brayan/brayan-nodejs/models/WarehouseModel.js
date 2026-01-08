const { pool: db } = require('../config/database');

class WarehouseModel {

    // ==================== CATEGORÍAS ====================

    static async getAllCategories() {
        const [rows] = await db.execute(
            `SELECT * FROM product_categories WHERE state = 1 ORDER BY name ASC`
        );
        return rows;
    }

    static async getCategoryById(id) {
        const [rows] = await db.execute(
            'SELECT * FROM product_categories WHERE id = ?',
            [id]
        );
        return rows[0];
    }

    static async createCategory(data) {
        const [result] = await db.execute(
            `INSERT INTO product_categories (name, description, parent_id, state)
             VALUES (?, ?, ?, 1)`,
            [data.name, data.description || null, data.parent_id || null]
        );
        return result;
    }

    static async updateCategory(id, data) {
        const [result] = await db.execute(
            `UPDATE product_categories SET name = ?, description = ?, parent_id = ? WHERE id = ?`,
            [data.name, data.description || null, data.parent_id || null, id]
        );
        return result;
    }

    static async deleteCategory(id) {
        const [result] = await db.execute(
            'UPDATE product_categories SET state = 0 WHERE id = ?',
            [id]
        );
        return result;
    }

    // ==================== PROVEEDORES ====================

    static async getAllSuppliers() {
        const [rows] = await db.execute(
            `SELECT * FROM suppliers WHERE state = 1 ORDER BY name ASC`
        );
        return rows;
    }

    static async getSupplierById(id) {
        const [rows] = await db.execute(
            'SELECT * FROM suppliers WHERE id = ?',
            [id]
        );
        return rows[0];
    }

    static async createSupplier(data) {
        const [result] = await db.execute(
            `INSERT INTO suppliers
             (name, trade_name, ruc, contact_name, phone, mobile, email, website, address, city, notes, payment_terms, state)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)`,
            [
                data.name, data.trade_name, data.ruc, data.contact_name,
                data.phone, data.mobile, data.email, data.website,
                data.address, data.city, data.notes, data.payment_terms
            ]
        );
        return result;
    }

    static async updateSupplier(id, data) {
        const [result] = await db.execute(
            `UPDATE suppliers SET
             name = ?, trade_name = ?, ruc = ?, contact_name = ?,
             phone = ?, mobile = ?, email = ?, website = ?,
             address = ?, city = ?, notes = ?, payment_terms = ?
             WHERE id = ?`,
            [
                data.name, data.trade_name, data.ruc, data.contact_name,
                data.phone, data.mobile, data.email, data.website,
                data.address, data.city, data.notes, data.payment_terms, id
            ]
        );
        return result;
    }

    static async deleteSupplier(id) {
        const [result] = await db.execute(
            'UPDATE suppliers SET state = 0 WHERE id = ?',
            [id]
        );
        return result;
    }

    // ==================== PRODUCTOS (mejorado) ====================

    static async getAllProducts() {
        const [rows] = await db.execute(`
            SELECT p.*, c.name as category_name, s.name as supplier_name
            FROM products p
            LEFT JOIN product_categories c ON p.categoryid = c.id
            LEFT JOIN suppliers s ON p.supplierid = s.id
            WHERE p.state = 1
            ORDER BY p.name ASC
        `);
        return rows;
    }

    static async getProductById(id) {
        const [rows] = await db.execute(
            `SELECT p.*, c.name as category_name, s.name as supplier_name
             FROM products p
             LEFT JOIN product_categories c ON p.categoryid = c.id
             LEFT JOIN suppliers s ON p.supplierid = s.id
             WHERE p.id = ?`,
            [id]
        );
        return rows[0];
    }

    static async getLowStockProducts() {
        const [rows] = await db.execute(`
            SELECT * FROM products
            WHERE state = 1 AND stock <= min_stock
            ORDER BY stock ASC
        `);
        return rows;
    }
}

module.exports = WarehouseModel;
