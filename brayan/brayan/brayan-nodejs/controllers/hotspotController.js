const BusinessModel = require('../models/BusinessModel');
const businessModel = new BusinessModel();
const { pool: db } = require('../config/database');

// ==================== Hotspot Model (inline) ====================
const HotspotModel = {
    async getAllVouchers() {
        const [rows] = await db.query('SELECT v.*, r.name as router_name FROM hotspot_vouchers v LEFT JOIN routers r ON v.routerid = r.id ORDER BY v.id DESC');
        return rows;
    },
    async getVoucherById(id) {
        const [rows] = await db.query('SELECT * FROM hotspot_vouchers WHERE id = ?', [id]);
        return rows[0];
    },
    async createVoucher(data) {
        const [result] = await db.query(
            'INSERT INTO hotspot_vouchers (code, profile, time_limit, data_limit, price, routerid, status, expires_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [data.code, data.profile, data.time_limit, data.data_limit, data.price, data.routerid, 'available', data.expires_at]
        );
        return result;
    },
    async generateVoucherCode() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';
        for (let i = 0; i < 8; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return code;
    },
    async createBulkVouchers(data, count) {
        const connection = await db.getConnection();
        try {
            await connection.beginTransaction();
            const codes = [];
            for (let i = 0; i < count; i++) {
                const code = await this.generateVoucherCode();
                await connection.query(
                    'INSERT INTO hotspot_vouchers (code, profile, time_limit, data_limit, price, routerid, status, expires_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                    [code, data.profile, data.time_limit, data.data_limit, data.price, data.routerid, 'available', data.expires_at]
                );
                codes.push(code);
            }
            await connection.commit();
            return codes;
        } catch (error) {
            await connection.rollback();
            throw error;
        } finally {
            connection.release();
        }
    },
    async deleteVoucher(id) {
        const [result] = await db.query('DELETE FROM hotspot_vouchers WHERE id = ?', [id]);
        return result;
    },
    async getAllTemplates() {
        const [rows] = await db.query('SELECT * FROM hotspot_templates WHERE state = 1 ORDER BY id DESC');
        return rows;
    },
    async getTemplateById(id) {
        const [rows] = await db.query('SELECT * FROM hotspot_templates WHERE id = ?', [id]);
        return rows[0];
    },
    async createTemplate(data) {
        const [result] = await db.query(
            'INSERT INTO hotspot_templates (name, description, html_content, css_content, is_default) VALUES (?, ?, ?, ?, ?)',
            [data.name, data.description, data.html_content, data.css_content, data.is_default || 0]
        );
        return result;
    },
    async updateTemplate(id, data) {
        const [result] = await db.query(
            'UPDATE hotspot_templates SET name = ?, description = ?, html_content = ?, css_content = ?, is_default = ? WHERE id = ?',
            [data.name, data.description, data.html_content, data.css_content, data.is_default || 0, id]
        );
        return result;
    },
    async deleteTemplate(id) {
        const [result] = await db.query('UPDATE hotspot_templates SET state = 0 WHERE id = ?', [id]);
        return result;
    }
};

// ==================== Vouchers ====================
exports.vouchers = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        const [routers] = await db.query('SELECT * FROM routers WHERE state = 1');
        res.render('hotspot/vouchers', {
            page_name: 'Fichas Hotspot',
            business: business || {},
            userData: req.session.user,
            routers: routers
        });
    } catch (error) {
        console.error('Error en vouchers:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};

exports.listVouchers = async (req, res) => {
    try {
        const vouchers = await HotspotModel.getAllVouchers();
        res.json({ data: vouchers });
    } catch (error) {
        console.error('Error listando vouchers:', error);
        res.json({ data: [] });
    }
};

exports.createVoucher = async (req, res) => {
    try {
        const count = parseInt(req.body.count) || 1;
        if (count > 1) {
            const codes = await HotspotModel.createBulkVouchers(req.body, count);
            res.json({ success: true, message: `${count} fichas creadas correctamente`, codes: codes });
        } else {
            const code = req.body.code || await HotspotModel.generateVoucherCode();
            await HotspotModel.createVoucher({ ...req.body, code: code });
            res.json({ success: true, message: 'Ficha creada correctamente', code: code });
        }
    } catch (error) {
        console.error('Error creando voucher:', error);
        res.json({ success: false, message: error.message });
    }
};

exports.deleteVoucher = async (req, res) => {
    try {
        await HotspotModel.deleteVoucher(req.body.id);
        res.json({ success: true, message: 'Ficha eliminada correctamente' });
    } catch (error) {
        console.error('Error eliminando voucher:', error);
        res.json({ success: false, message: error.message });
    }
};

// ==================== Hotspot Routers ====================
exports.hotspotRouters = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('hotspot/routers', {
            page_name: 'Routers Hotspot',
            business: business || {},
            userData: req.session.user
        });
    } catch (error) {
        console.error('Error en routers hotspot:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};

// ==================== Templates ====================
exports.templates = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('hotspot/templates', {
            page_name: 'Plantillas Hotspot',
            business: business || {},
            userData: req.session.user
        });
    } catch (error) {
        console.error('Error en plantillas hotspot:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};

exports.listTemplates = async (req, res) => {
    try {
        const templates = await HotspotModel.getAllTemplates();
        res.json({ data: templates });
    } catch (error) {
        console.error('Error listando plantillas:', error);
        res.json({ data: [] });
    }
};

exports.selectTemplate = async (req, res) => {
    try {
        const template = await HotspotModel.getTemplateById(req.params.id);
        res.json(template || {});
    } catch (error) {
        console.error('Error seleccionando plantilla:', error);
        res.json({});
    }
};

exports.createTemplate = async (req, res) => {
    try {
        await HotspotModel.createTemplate(req.body);
        res.json({ success: true, message: 'Plantilla creada correctamente' });
    } catch (error) {
        console.error('Error creando plantilla:', error);
        res.json({ success: false, message: error.message });
    }
};

exports.updateTemplate = async (req, res) => {
    try {
        await HotspotModel.updateTemplate(req.body.id, req.body);
        res.json({ success: true, message: 'Plantilla actualizada correctamente' });
    } catch (error) {
        console.error('Error actualizando plantilla:', error);
        res.json({ success: false, message: error.message });
    }
};

exports.deleteTemplate = async (req, res) => {
    try {
        await HotspotModel.deleteTemplate(req.body.id);
        res.json({ success: true, message: 'Plantilla eliminada correctamente' });
    } catch (error) {
        console.error('Error eliminando plantilla:', error);
        res.json({ success: false, message: error.message });
    }
};

