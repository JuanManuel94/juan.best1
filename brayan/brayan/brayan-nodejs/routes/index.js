const express = require('express');
const router = express.Router();

// Importar controladores
const loginController = require('../controllers/loginController');
const dashboardController = require('../controllers/dashboardController');
const customersController = require('../controllers/customersController');
const usersController = require('../controllers/usersController');
const financeController = require('../controllers/financeController');
const tasksController = require('../controllers/tasksController');
const warehouseController = require('../controllers/warehouseController');
const networkController = require('../controllers/networkController');
const messagingController = require('../controllers/messagingController');
const settingsController = require('../controllers/settingsController');
const hotspotController = require('../controllers/hotspotController');

// Middleware de autenticación
const { isAuthenticated, isNotAuthenticated } = require('../middleware/auth');

// ================= RUTAS DE LOGIN =================
router.get('/', (req, res) => res.redirect('/login'));
router.get('/login', isNotAuthenticated, (req, res) => loginController.login(req, res));
router.get('/login/login', isNotAuthenticated, (req, res) => loginController.login(req, res));
router.post('/login/validation', (req, res) => loginController.validation(req, res));
router.post('/login/reset', (req, res) => loginController.reset(req, res));
router.get('/login/restore/:emailEncrypted/:token', (req, res) => loginController.restore(req, res));
router.post('/login/update_password', (req, res) => loginController.updatePassword(req, res));
router.get('/logout', (req, res) => loginController.logout(req, res));

// ================= RUTAS DE DASHBOARD =================
router.get('/dashboard', isAuthenticated, (req, res) => dashboardController.dashboard(req, res));
router.get('/dashboard/dashboard', isAuthenticated, (req, res) => dashboardController.dashboard(req, res));

// ================= RUTAS DE CLIENTES =================
router.get('/customers', isAuthenticated, (req, res) => customersController.customers(req, res));
router.get('/customers/customers', isAuthenticated, (req, res) => customersController.customers(req, res));
router.get('/customers/list', isAuthenticated, (req, res) => customersController.list(req, res));
router.post('/customers/create', isAuthenticated, (req, res) => customersController.create(req, res));
router.post('/customers/modify', isAuthenticated, (req, res) => customersController.modify(req, res));
router.get('/customers/select/:id', isAuthenticated, (req, res) => customersController.selectRecord(req, res));
router.post('/customers/remove', isAuthenticated, (req, res) => customersController.remove(req, res));

// Nuevas rutas de clientes
router.get('/customers/map', isAuthenticated, (req, res) => customersController.map(req, res));
router.get('/customers/installations', isAuthenticated, (req, res) => customersController.installations(req, res));
router.get('/customers/installations/list', isAuthenticated, (req, res) => customersController.listInstallations(req, res));
router.get('/customers/contracts', isAuthenticated, (req, res) => customersController.contracts(req, res));
router.get('/customers/contracts/list', isAuthenticated, (req, res) => customersController.listContracts(req, res));
router.get('/customers/announcements', isAuthenticated, (req, res) => customersController.announcements(req, res));
router.get('/customers/announcements/list', isAuthenticated, (req, res) => customersController.listAnnouncements(req, res));
router.get('/customers/emails', isAuthenticated, (req, res) => customersController.emails(req, res));
router.get('/customers/emails/list', isAuthenticated, (req, res) => customersController.listEmails(req, res));

// ================= RUTAS DE USUARIOS =================
router.get('/users', isAuthenticated, (req, res) => usersController.users(req, res));
router.get('/users/users', isAuthenticated, (req, res) => usersController.users(req, res));
router.get('/users/list', isAuthenticated, (req, res) => usersController.list(req, res));
router.post('/users/create', isAuthenticated, (req, res) => usersController.create(req, res));
router.post('/users/modify', isAuthenticated, (req, res) => usersController.modify(req, res));
router.get('/users/select/:id', isAuthenticated, (req, res) => usersController.selectRecord(req, res));
router.post('/users/remove', isAuthenticated, (req, res) => usersController.remove(req, res));

// ================= RUTAS DE FACTURAS =================
const billsController = require('../controllers/billsController');
router.get('/bills', isAuthenticated, (req, res) => billsController.bills(req, res));
router.get('/bills/list', isAuthenticated, (req, res) => billsController.list(req, res));

// ================= RUTAS DE SERVICIOS =================
const servicesController = require('../controllers/servicesController');
router.get('/services/internet', isAuthenticated, (req, res) => servicesController.internet(req, res));
router.get('/services/internet/list', isAuthenticated, (req, res) => servicesController.listInternet(req, res));
router.get('/services/voice', isAuthenticated, (req, res) => servicesController.voice(req, res));
router.get('/services/custom', isAuthenticated, (req, res) => servicesController.custom(req, res));

// ================= RUTAS DE GESTIÓN DE RED =================
const routersController = require('../controllers/routersController');
router.get('/routers', isAuthenticated, (req, res) => routersController.routers(req, res));
router.get('/routers/list', isAuthenticated, (req, res) => routersController.list(req, res));

// SmartOLT
router.get('/network/smartolt', isAuthenticated, networkController.smartolt);
router.get('/network/smartolt/olts/list', isAuthenticated, networkController.listOLTs);
router.get('/network/smartolt/onus/list', isAuthenticated, networkController.listONUs);
router.post('/network/smartolt/olts/create', isAuthenticated, networkController.createOLT);
router.post('/network/smartolt/olts/update', isAuthenticated, networkController.updateOLT);
router.post('/network/smartolt/olts/delete', isAuthenticated, networkController.deleteOLT);
router.get('/network/smartolt/olts/select/:id', isAuthenticated, networkController.selectOLT);

// IPv4
router.get('/network/ipv4', isAuthenticated, networkController.ipv4);
router.get('/network/ipv4/list', isAuthenticated, networkController.listNetworks);
router.post('/network/ipv4/create', isAuthenticated, networkController.createNetwork);
router.post('/network/ipv4/update', isAuthenticated, networkController.updateNetwork);
router.post('/network/ipv4/delete', isAuthenticated, networkController.deleteNetwork);
router.get('/network/ipv4/select/:id', isAuthenticated, networkController.selectNetwork);

// Monitoreo
router.get('/network/monitoring', isAuthenticated, networkController.monitoring);
router.get('/network/monitoring/list', isAuthenticated, networkController.listHosts);
router.post('/network/monitoring/create', isAuthenticated, networkController.createHost);
router.post('/network/monitoring/update', isAuthenticated, networkController.updateHost);
router.post('/network/monitoring/delete', isAuthenticated, networkController.deleteHost);
router.get('/network/monitoring/select/:id', isAuthenticated, networkController.selectHost);

// Tráfico, IPs Visitadas, Blacklist
router.get('/network/traffic', isAuthenticated, networkController.traffic);
router.get('/network/visited-ips', isAuthenticated, networkController.visitedIps);
router.get('/network/blacklist', isAuthenticated, networkController.blacklist);

// ================= RUTAS DE TICKETS =================
const ticketsController = require('../controllers/ticketsController');
router.get('/tickets', isAuthenticated, (req, res) => ticketsController.tickets(req, res));
router.get('/tickets/list', isAuthenticated, (req, res) => ticketsController.listTickets(req, res));
router.get('/tickets/answered', isAuthenticated, (req, res) => ticketsController.answered(req, res));
router.get('/tickets/closed', isAuthenticated, (req, res) => ticketsController.closed(req, res));

// ================= RUTAS DE FINANZAS =================
router.get('/finance/transactions', isAuthenticated, financeController.transactions);
router.get('/finance/transactions/list', isAuthenticated, financeController.listTransactions);
router.post('/finance/transactions/create', isAuthenticated, financeController.createTransaction);
router.get('/finance/payment', isAuthenticated, financeController.registerPaymentPage);
router.post('/finance/payment/register', isAuthenticated, financeController.registerPayment);
router.get('/finance/payment/bills/:clientid', isAuthenticated, financeController.getClientBills);
router.get('/finance/other-movements', isAuthenticated, financeController.otherMovements);
router.get('/finance/other-movements/list', isAuthenticated, financeController.listOtherMovements);
router.post('/finance/other-movements/create', isAuthenticated, financeController.createOtherMovement);
router.get('/finance/reports', isAuthenticated, financeController.paymentReports);
router.get('/finance/reports/data', isAuthenticated, financeController.getPaymentReportData);
router.get('/finance/mass-payments', isAuthenticated, financeController.massPayments);
router.get('/finance/electronic-billing', isAuthenticated, financeController.electronicBilling);

// ================= RUTAS DE TAREAS =================
router.get('/tasks', isAuthenticated, tasksController.tasks);
router.get('/tasks/list', isAuthenticated, tasksController.list);
router.get('/tasks/select/:id', isAuthenticated, tasksController.selectRecord);
router.post('/tasks/create', isAuthenticated, tasksController.create);
router.post('/tasks/update', isAuthenticated, tasksController.update);
router.post('/tasks/status', isAuthenticated, tasksController.changeStatus);
router.post('/tasks/remove', isAuthenticated, tasksController.remove);

// ================= RUTAS DE ALMACÉN =================
router.get('/warehouse/products', isAuthenticated, warehouseController.products);
router.get('/warehouse/products/list', isAuthenticated, warehouseController.listProducts);
router.get('/warehouse/categories', isAuthenticated, warehouseController.categories);
router.get('/warehouse/categories/list', isAuthenticated, warehouseController.listCategories);
router.get('/warehouse/categories/select/:id', isAuthenticated, warehouseController.selectCategory);
router.post('/warehouse/categories/create', isAuthenticated, warehouseController.createCategory);
router.post('/warehouse/categories/update', isAuthenticated, warehouseController.updateCategory);
router.post('/warehouse/categories/delete', isAuthenticated, warehouseController.deleteCategory);
router.get('/warehouse/suppliers', isAuthenticated, warehouseController.suppliers);
router.get('/warehouse/suppliers/list', isAuthenticated, warehouseController.listSuppliers);
router.get('/warehouse/suppliers/select/:id', isAuthenticated, warehouseController.selectSupplier);
router.post('/warehouse/suppliers/create', isAuthenticated, warehouseController.createSupplier);
router.post('/warehouse/suppliers/update', isAuthenticated, warehouseController.updateSupplier);
router.post('/warehouse/suppliers/delete', isAuthenticated, warehouseController.deleteSupplier);

// ================= RUTAS DE HOTSPOT =================
router.get('/hotspot/vouchers', isAuthenticated, hotspotController.vouchers);
router.get('/hotspot/vouchers/list', isAuthenticated, hotspotController.listVouchers);
router.post('/hotspot/vouchers/create', isAuthenticated, hotspotController.createVoucher);
router.post('/hotspot/vouchers/delete', isAuthenticated, hotspotController.deleteVoucher);
router.get('/hotspot/routers', isAuthenticated, hotspotController.hotspotRouters);
router.get('/hotspot/templates', isAuthenticated, hotspotController.templates);
router.get('/hotspot/templates/list', isAuthenticated, hotspotController.listTemplates);
router.get('/hotspot/templates/select/:id', isAuthenticated, hotspotController.selectTemplate);
router.post('/hotspot/templates/create', isAuthenticated, hotspotController.createTemplate);
router.post('/hotspot/templates/update', isAuthenticated, hotspotController.updateTemplate);
router.post('/hotspot/templates/delete', isAuthenticated, hotspotController.deleteTemplate);

// ================= RUTAS DE MENSAJERÍA =================
router.get('/messaging/whatsapp', isAuthenticated, messagingController.whatsapp);
router.get('/messaging/whatsapp/list', isAuthenticated, messagingController.listAllMessages);
router.get('/messaging/sent', isAuthenticated, messagingController.sentMessages);
router.get('/messaging/sent/list', isAuthenticated, messagingController.listSentMessages);
router.get('/messaging/received', isAuthenticated, messagingController.receivedMessages);
router.get('/messaging/received/list', isAuthenticated, messagingController.listReceivedMessages);
router.post('/messaging/send', isAuthenticated, messagingController.sendMessage);

// ================= RUTAS DE AJUSTES =================
router.get('/settings', isAuthenticated, settingsController.index);
router.get('/settings/general', isAuthenticated, settingsController.general);
router.post('/settings/general/update', isAuthenticated, settingsController.updateGeneral);
router.get('/settings/company', isAuthenticated, settingsController.company);
router.post('/settings/company/update', isAuthenticated, settingsController.updateCompany);
router.get('/settings/billing', isAuthenticated, settingsController.billingConfig);
router.post('/settings/billing/update', isAuthenticated, settingsController.updateBillingConfig);
router.get('/settings/api', isAuthenticated, settingsController.apiWebhooks);
router.post('/settings/api/update', isAuthenticated, settingsController.updateAPISettings);

module.exports = router;
