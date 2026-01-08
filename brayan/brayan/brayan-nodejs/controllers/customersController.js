const CustomersModel = require('../models/CustomersModel');
const BusinessModel = require('../models/BusinessModel');
const PermissionsModel = require('../models/PermissionsModel');
const helpers = require('../helpers/helpers');
const config = require('../config/config');

class CustomersController {
    constructor() {
        this.model = new CustomersModel();
        this.businessModel = new BusinessModel();
        this.permissionsModel = new PermissionsModel();
    }

    async customers(req, res) {
        try {
            const permits = await this.permissionsModel.modulePermissions(req.session.userData.profileid);
            req.session.permits = permits;
            req.session.permits_module = permits[config.MODULES.CLIENTS] || {};

            const business = await this.businessModel.showBusiness();
            const zones = await this.model.listZones();
            const documents = await this.model.listDocuments();

            res.render('customers/customers', {
                page_name: 'Clientes',
                page_functions_js: 'customers.js',
                business: business || {},
                zones: zones || [],
                documents: documents || []
            });

        } catch (error) {
            console.error('Customers error:', error);
            res.render('errors/500', {
                page_name: 'Error',
                business: req.session.businessData || {}
            });
        }
    }

    async list(req, res) {
        try {
            const data = await this.model.listRecords();
            return res.json({ data: data });
        } catch (error) {
            console.error('List customers error:', error);
            return res.json({ data: [] });
        }
    }

    async create(req, res) {
        try {
            const data = {
                names: helpers.strClean(req.body.names),
                surnames: helpers.strClean(req.body.surnames),
                documentid: parseInt(req.body.documentid) || 1,
                document: helpers.strClean(req.body.document),
                mobile: helpers.strClean(req.body.mobile),
                email: helpers.strClean(req.body.email?.toLowerCase()),
                address: helpers.strClean(req.body.address),
                zoneid: parseInt(req.body.zoneid) || null,
                latitude: req.body.latitude || null,
                longitude: req.body.longitude || null,
                contract_date: req.body.contract_date,
                contract_status: parseInt(req.body.contract_status) || 1
            };

            if (!data.names || !data.surnames || !data.document) {
                return res.json({
                    status: 'warning',
                    msg: 'Los campos nombres, apellidos y documento son obligatorios.'
                });
            }

            const result = await this.model.create(data);

            if (result === 'exists') {
                return res.json({
                    status: 'warning',
                    msg: 'Ya existe un cliente con este documento.'
                });
            }

            return res.json({
                status: result === 'success' ? 'success' : 'error',
                msg: result === 'success' ? 'Cliente creado correctamente.' : 'Error al crear cliente.'
            });

        } catch (error) {
            console.error('Create customer error:', error);
            return res.json({
                status: 'error',
                msg: 'Error en el servidor.'
            });
        }
    }

    async modify(req, res) {
        try {
            const data = {
                id: parseInt(req.body.id),
                names: helpers.strClean(req.body.names),
                surnames: helpers.strClean(req.body.surnames),
                documentid: parseInt(req.body.documentid) || 1,
                document: helpers.strClean(req.body.document),
                mobile: helpers.strClean(req.body.mobile),
                email: helpers.strClean(req.body.email?.toLowerCase()),
                address: helpers.strClean(req.body.address),
                zoneid: parseInt(req.body.zoneid) || null,
                latitude: req.body.latitude || null,
                longitude: req.body.longitude || null,
                contract_date: req.body.contract_date,
                contract_status: parseInt(req.body.contract_status) || 1
            };

            if (!data.id || !data.names || !data.surnames || !data.document) {
                return res.json({
                    status: 'warning',
                    msg: 'Los campos son obligatorios.'
                });
            }

            const result = await this.model.modify(data);

            if (result === 'exists') {
                return res.json({
                    status: 'warning',
                    msg: 'Ya existe un cliente con este documento.'
                });
            }

            return res.json({
                status: result === 'success' ? 'success' : 'error',
                msg: result === 'success' ? 'Cliente modificado correctamente.' : 'Error al modificar cliente.'
            });

        } catch (error) {
            console.error('Modify customer error:', error);
            return res.json({
                status: 'error',
                msg: 'Error en el servidor.'
            });
        }
    }

    async selectRecord(req, res) {
        try {
            const id = parseInt(req.params.id);
            const data = await this.model.selectRecord(id);
            return res.json(data || {});
        } catch (error) {
            console.error('Select customer error:', error);
            return res.json({});
        }
    }

    async remove(req, res) {
        try {
            const id = parseInt(req.body.id);
            const result = await this.model.remove(id);

            return res.json({
                status: result === 'success' ? 'success' : 'error',
                msg: result === 'success' ? 'Cliente eliminado correctamente.' : 'Error al eliminar cliente.'
            });

        } catch (error) {
            console.error('Remove customer error:', error);
            return res.json({
                status: 'error',
                msg: 'Error en el servidor.'
            });
        }
    }

    // ==================== MAPA DE CLIENTES ====================
    async map(req, res) {
        try {
            const business = await this.businessModel.showBusiness();
            res.render('customers/map', {
                page_name: 'Mapa de Clientes',
                business: business || {},
                userData: req.session.user
            });
        } catch (error) {
            console.error('Map error:', error);
            res.status(500).render('errors/500', { error: error.message });
        }
    }

    // ==================== INSTALACIONES ====================
    async installations(req, res) {
        try {
            const business = await this.businessModel.showBusiness();
            res.render('customers/installations', {
                page_name: 'Instalaciones',
                business: business || {},
                userData: req.session.user
            });
        } catch (error) {
            console.error('Installations error:', error);
            res.status(500).render('errors/500', { error: error.message });
        }
    }

    async listInstallations(req, res) {
        try {
            const { pool: db } = require('../config/database');
            const [rows] = await db.query(`
                SELECT i.*, c.names, c.surnames, u.names as technician_name 
                FROM installations i 
                LEFT JOIN clients c ON i.clientid = c.id 
                LEFT JOIN users u ON i.technicianid = u.id 
                ORDER BY i.scheduled_date DESC
            `);
            res.json({ data: rows });
        } catch (error) {
            console.error('List installations error:', error);
            res.json({ data: [] });
        }
    }

    // ==================== CONTRATOS ====================
    async contracts(req, res) {
        try {
            const business = await this.businessModel.showBusiness();
            res.render('customers/contracts', {
                page_name: 'Contratos',
                business: business || {},
                userData: req.session.user
            });
        } catch (error) {
            console.error('Contracts error:', error);
            res.status(500).render('errors/500', { error: error.message });
        }
    }

    async listContracts(req, res) {
        try {
            const { pool: db } = require('../config/database');
            const [rows] = await db.query(`
                SELECT c.*, cl.names, cl.surnames 
                FROM contracts c 
                LEFT JOIN clients cl ON c.clientid = cl.id 
                ORDER BY c.created_at DESC
            `);
            res.json({ data: rows });
        } catch (error) {
            console.error('List contracts error:', error);
            res.json({ data: [] });
        }
    }

    // ==================== ANUNCIOS ====================
    async announcements(req, res) {
        try {
            const business = await this.businessModel.showBusiness();
            res.render('customers/announcements', {
                page_name: 'Anuncios',
                business: business || {},
                userData: req.session.user
            });
        } catch (error) {
            console.error('Announcements error:', error);
            res.status(500).render('errors/500', { error: error.message });
        }
    }

    async listAnnouncements(req, res) {
        try {
            const { pool: db } = require('../config/database');
            const [rows] = await db.query('SELECT * FROM announcements ORDER BY created_at DESC');
            res.json({ data: rows });
        } catch (error) {
            console.error('List announcements error:', error);
            res.json({ data: [] });
        }
    }

    // ==================== CORREOS ====================
    async emails(req, res) {
        try {
            const business = await this.businessModel.showBusiness();
            res.render('customers/emails', {
                page_name: 'Correos',
                business: business || {},
                userData: req.session.user
            });
        } catch (error) {
            console.error('Emails error:', error);
            res.status(500).render('errors/500', { error: error.message });
        }
    }

    async listEmails(req, res) {
        try {
            const { pool: db } = require('../config/database');
            const [rows] = await db.query('SELECT * FROM emails_sent ORDER BY created_at DESC');
            res.json({ data: rows });
        } catch (error) {
            console.error('List emails error:', error);
            res.json({ data: [] });
        }
    }
}

module.exports = new CustomersController();

