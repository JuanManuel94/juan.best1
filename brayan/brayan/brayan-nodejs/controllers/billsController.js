const BusinessModel = require('../models/BusinessModel');
const PermissionsModel = require('../models/PermissionsModel');
const config = require('../config/config');

class BillsController {
    constructor() {
        this.businessModel = new BusinessModel();
        this.permissionsModel = new PermissionsModel();
    }

    async bills(req, res) {
        try {
            // Check permissions (using a placeholder ID for now or existing logic)
            // const permits = await this.permissionsModel.modulePermissions(req.session.userData.profileid);
            // req.session.permits = permits;

            const business = await this.businessModel.showBusiness();

            res.render('bills/bills', {
                page_name: 'Facturas',
                page_functions_js: 'bills.js',
                business: business || {}
            });

        } catch (error) {
            console.error('Bills page error:', error);
            res.render('errors/500', {
                page_name: 'Error',
                business: req.session.businessData || {}
            });
        }
    }

    async list(req, res) {
        try {
            // Mock data for now to verify table structure
            const data = [
                {
                    id: 1,
                    legal_number: 'F001-000001',
                    type: 'LIBRE',
                    client: 'Juan Perez',
                    emission_date: '2023-10-01',
                    due_date: '2023-10-05',
                    total: 100.00,
                    balance: 0.00,
                    payment_method: 'Efectivo',
                    status: 1 // Pagado
                },
                {
                    id: 2,
                    legal_number: 'F001-000002',
                    type: 'SERVICIOS',
                    client: 'Maria Lopez',
                    emission_date: '2023-10-02',
                    due_date: '2023-10-10',
                    total: 150.50,
                    balance: 150.50,
                    payment_method: 'Transferencia',
                    status: 2 // Pendiente
                }
            ];

            return res.json({ data: data });
        } catch (error) {
            console.error('List bills error:', error);
            return res.json({ data: [] });
        }
    }
}

module.exports = new BillsController();
