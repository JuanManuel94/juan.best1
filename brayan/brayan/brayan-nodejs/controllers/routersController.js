const BusinessModel = require('../models/BusinessModel');
const PermissionsModel = require('../models/PermissionsModel');
const config = require('../config/config');

class RoutersController {
    constructor() {
        this.businessModel = new BusinessModel();
        this.permissionsModel = new PermissionsModel();
    }

    async routers(req, res) {
        try {
            // Check permissions (placeholder)
            // const permits = await this.permissionsModel.modulePermissions(req.session.userData.profileid);

            const business = await this.businessModel.showBusiness();

            res.render('routers/routers', {
                page_name: 'Gestión de Routers',
                page_functions_js: 'routers.js',
                business: business || {}
            });

        } catch (error) {
            console.error('Routers page error:', error);
            res.render('errors/500', {
                page_name: 'Error',
                business: req.session.businessData || {}
            });
        }
    }

    async list(req, res) {
        try {
            // Mock data for Routers
            const data = [
                {
                    id: 1,
                    name: 'MikroTik CCR1009',
                    secondary_name: 'PPPoe Server + Radius',
                    ip: '192.168.10.1',
                    model: 'CCR1009-7G-1C-1S+',
                    version: '6.48.6 (stable)',
                    clients: 154,
                    status: 1 // Conectado
                },
                {
                    id: 2,
                    name: 'Nodo Norte - RB750',
                    secondary_name: 'Balanceo PCC',
                    ip: '10.10.10.1',
                    model: 'RB750Gr3',
                    version: '7.1.1 (stable)',
                    clients: 42,
                    status: 1 // Conectado
                },
                {
                    id: 3,
                    name: 'MikroTik Cloud Core',
                    secondary_name: 'Core Principal',
                    ip: '172.16.0.1',
                    model: 'CCR1036-8G-2S+',
                    version: '6.49.2 (long-term)',
                    clients: 320,
                    status: 2 // Desconectado / Error
                }
            ];

            return res.json({ data: data });
        } catch (error) {
            console.error('List routers error:', error);
            return res.json({ data: [] });
        }
    }
}

module.exports = new RoutersController();
