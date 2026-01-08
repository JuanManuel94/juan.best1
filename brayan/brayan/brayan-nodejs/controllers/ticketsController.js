const BusinessModel = require('../models/BusinessModel');
const PermissionsModel = require('../models/PermissionsModel');
const config = require('../config/config');

class TicketsController {
    constructor() {
        this.businessModel = new BusinessModel();
        this.permissionsModel = new PermissionsModel();
    }

    async tickets(req, res) {
        try {
            const business = await this.businessModel.showBusiness();
            res.render('tickets/tickets', {
                page_name: 'Tickets de Soporte',
                page_functions_js: 'tickets.js',
                business: business || {}
            });
        } catch (error) {
            console.error('Tickets page error:', error);
            res.render('errors/500', {
                page_name: 'Error',
                business: req.session.businessData || {}
            });
        }
    }

    async listTickets(req, res) {
        try {
            // Mock data for Tickets
            const data = [
                {
                    id: 105,
                    department: 'Soporte Técnico',
                    sender: 'Maria Gonzalez (Cliente)',
                    subject: 'Sin internet desde ayer',
                    technician: 'Carlos Admin',
                    date: '2023-12-18 10:30:00',
                    location: 'Zona Norte',
                    status: 1, // Open
                    last_response: '2023-12-18 11:00:00'
                },
                {
                    id: 104,
                    department: 'Facturación',
                    sender: 'Juan Perez (Cliente)',
                    subject: 'Error en factura',
                    technician: 'Ana Ventas',
                    date: '2023-12-17 15:45:00',
                    location: 'Zona Sur',
                    status: 2, // Closed/Answered
                    last_response: '2023-12-17 16:00:00'
                }
            ];

            return res.json({ data: data });
        } catch (error) {
            console.error('List tickets error:', error);
            return res.json({ data: [] });
        }
    }

    // ==================== TICKETS RESPONDIDOS ====================
    async answered(req, res) {
        try {
            const business = await this.businessModel.showBusiness();
            res.render('tickets/tickets', {
                page_name: 'Tickets Respondidos',
                page_functions_js: 'tickets.js',
                business: business || {},
                filter: 'answered'
            });
        } catch (error) {
            console.error('Answered tickets error:', error);
            res.status(500).render('errors/500', { error: error.message });
        }
    }

    // ==================== TICKETS CERRADOS ====================
    async closed(req, res) {
        try {
            const business = await this.businessModel.showBusiness();
            res.render('tickets/tickets', {
                page_name: 'Tickets Cerrados',
                page_functions_js: 'tickets.js',
                business: business || {},
                filter: 'closed'
            });
        } catch (error) {
            console.error('Closed tickets error:', error);
            res.status(500).render('errors/500', { error: error.message });
        }
    }
}

module.exports = new TicketsController();

