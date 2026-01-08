const DashboardModel = require('../models/DashboardModel');
const BusinessModel = require('../models/BusinessModel');
const PermissionsModel = require('../models/PermissionsModel');
const config = require('../config/config');

class DashboardController {
    constructor() {
        this.model = new DashboardModel();
        this.businessModel = new BusinessModel();
        this.permissionsModel = new PermissionsModel();
    }

    async dashboard(req, res) {
        try {
            // Cargar permisos del módulo
            const permits = await this.permissionsModel.modulePermissions(req.session.userData.profileid);
            req.session.permits = permits;
            req.session.permits_module = permits[config.MODULES.DASHBOARD] || {};

            // Obtener estadísticas
            const [
                totalClients,
                activeClients,
                suspendedClients,
                pendingBills,
                paidBills,
                openTickets,
                totalIncome,
                monthlyData,
                recentPayments,
                recentTickets
            ] = await Promise.all([
                this.model.countClients(),
                this.model.countActiveClients(),
                this.model.countSuspendedClients(),
                this.model.countPendingBills(),
                this.model.countPaidBills(),
                this.model.countOpenTickets(),
                this.model.getTotalIncome(),
                this.model.getMonthlyData(),
                this.model.getRecentPayments(),
                this.model.getRecentTickets()
            ]);

            const business = await this.businessModel.showBusiness();
            req.session.businessData = business;

            res.render('dashboard/dashboard', {
                page_name: 'Dashboard',
                page_functions_js: 'dashboard.js',
                business: business || {},
                stats: {
                    totalClients: totalClients?.total || 0,
                    activeClients: activeClients?.total || 0,
                    suspendedClients: suspendedClients?.total || 0,
                    pendingBills: pendingBills?.total || 0,
                    paidBills: paidBills?.total || 0,
                    openTickets: openTickets?.total || 0,
                    totalIncome: totalIncome?.total || 0,
                },
                monthlyData: monthlyData || [],
                recentPayments: recentPayments || [],
                recentTickets: recentTickets || []
            });

        } catch (error) {
            console.error('Dashboard error:', error);
            res.render('errors/500', {
                page_name: 'Error',
                business: req.session.businessData || {}
            });
        }
    }
}

module.exports = new DashboardController();
