const BusinessModel = require('../models/BusinessModel');
const PermissionsModel = require('../models/PermissionsModel');
const config = require('../config/config');

class ServicesController {
    constructor() {
        this.businessModel = new BusinessModel();
        this.permissionsModel = new PermissionsModel();
    }

    async internet(req, res) {
        try {
            // Check permissions (placeholder)
            // const permits = await this.permissionsModel.modulePermissions(req.session.userData.profileid);

            const business = await this.businessModel.showBusiness();

            res.render('services/internet', {
                page_name: 'Planes de Internet',
                page_functions_js: 'internet.js',
                business: business || {}
            });

        } catch (error) {
            console.error('Services page error:', error);
            res.render('errors/500', {
                page_name: 'Error',
                business: req.session.businessData || {}
            });
        }
    }

    async listInternet(req, res) {
        try {
            // Mock data for Internet Plans
            const data = [
                {
                    id: 1,
                    name: 'Plan 3 Megas',
                    download_speed: 3000, // kbps
                    upload_speed: 1000,   // kbps
                    price: 950.00,
                    active_clients: 15,
                    suspended_clients: 2
                },
                {
                    id: 2,
                    name: 'Plan 5 Megas',
                    download_speed: 5000,
                    upload_speed: 1500,
                    price: 1200.00,
                    active_clients: 42,
                    suspended_clients: 0
                },
                {
                    id: 3,
                    name: 'Plan Fibra 50M',
                    download_speed: 50000,
                    upload_speed: 25000,
                    price: 2500.00,
                    active_clients: 8,
                    suspended_clients: 1
                }
            ];

            return res.json({ data: data });
        } catch (error) {
            console.error('List services error:', error);
            return res.json({ data: [] });
        }
    }

    // ==================== PLANES DE VOZ ====================
    async voice(req, res) {
        try {
            const business = await this.businessModel.showBusiness();
            res.render('services/voice', {
                page_name: 'Planes de Voz',
                business: business || {}
            });
        } catch (error) {
            console.error('Voice page error:', error);
            res.status(500).render('errors/500', { error: error.message });
        }
    }

    // ==================== SERVICIOS PERSONALIZADOS ====================
    async custom(req, res) {
        try {
            const business = await this.businessModel.showBusiness();
            res.render('services/custom', {
                page_name: 'Servicios Personalizados',
                business: business || {}
            });
        } catch (error) {
            console.error('Custom services page error:', error);
            res.status(500).render('errors/500', { error: error.message });
        }
    }
}

module.exports = new ServicesController();

