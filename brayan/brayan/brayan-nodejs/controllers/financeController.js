const FinanceModel = require('../models/FinanceModel');
const CustomersModel = require('../models/CustomersModel');
const BusinessModel = require('../models/BusinessModel');
const businessModel = new BusinessModel();

// Función helper para obtener datos comunes de las vistas
const getViewData = (req, pageName, pageTitle, business = {}) => ({
    page_name: pageName,
    page_title: pageTitle,
    userData: req.session.userData,
    business: business
});

// ==================== TRANSACCIONES ====================

exports.transactions = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('finance/transactions', {
            ...getViewData(req, 'Transacciones', 'Transacciones', business || {}),
            page_functions_js: 'finance/transactions.js'
        });
    } catch (error) {
        console.error('Error loading transactions:', error);
        res.status(500).render('errors/500');
    }
};

exports.listTransactions = async (req, res) => {
    try {
        const filters = {
            type: req.query.type || null,
            startDate: req.query.startDate || null,
            endDate: req.query.endDate || null,
            category: req.query.category || null
        };
        const transactions = await FinanceModel.getAllTransactions(filters);
        res.json({ data: transactions });
    } catch (error) {
        console.error('Error listing transactions:', error);
        res.status(500).json({ error: 'Error al cargar transacciones' });
    }
};

exports.createTransaction = async (req, res) => {
    try {
        const data = {
            ...req.body,
            userid: req.session.userData.id
        };
        await FinanceModel.createTransaction(data);
        res.json({ success: true, message: 'Transacción registrada correctamente' });
    } catch (error) {
        console.error('Error creating transaction:', error);
        res.status(500).json({ success: false, message: 'Error al registrar transacción' });
    }
};

// ==================== REGISTRAR PAGO ====================

exports.registerPaymentPage = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('finance/register-payment', {
            ...getViewData(req, 'Registrar Pago', 'Registrar Pago', business || {}),
            page_functions_js: 'finance/register-payment.js'
        });
    } catch (error) {
        console.error('Error loading register payment page:', error);
        res.status(500).render('errors/500');
    }
};

exports.registerPayment = async (req, res) => {
    try {
        const data = {
            ...req.body,
            userid: req.session.userData.id
        };
        await FinanceModel.registerPayment(data);
        res.json({ success: true, message: 'Pago registrado correctamente' });
    } catch (error) {
        console.error('Error registering payment:', error);
        res.status(500).json({ success: false, message: 'Error al registrar pago' });
    }
};

exports.getClientBills = async (req, res) => {
    try {
        const { clientid } = req.params;
        const bills = await FinanceModel.getClientPendingBills(clientid);
        res.json({ success: true, bills });
    } catch (error) {
        console.error('Error getting client bills:', error);
        res.status(500).json({ success: false, message: 'Error al obtener facturas' });
    }
};

// ==================== OTROS INGRESOS Y EGRESOS ====================

exports.otherMovements = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('finance/other-movements', {
            ...getViewData(req, 'Otros Ingresos & Egresos', 'Otros Ingresos & Egresos', business || {}),
            page_functions_js: 'finance/other-movements.js'
        });
    } catch (error) {
        console.error('Error loading other movements:', error);
        res.status(500).render('errors/500');
    }
};

exports.listOtherMovements = async (req, res) => {
    try {
        const filters = {
            type: req.query.type || null,
            startDate: req.query.startDate || null,
            endDate: req.query.endDate || null
        };
        const movements = await FinanceModel.getAllOtherMovements(filters);
        res.json({ data: movements });
    } catch (error) {
        console.error('Error listing other movements:', error);
        res.status(500).json({ error: 'Error al cargar movimientos' });
    }
};

exports.createOtherMovement = async (req, res) => {
    try {
        const data = {
            ...req.body,
            created_by: req.session.userData.id
        };
        await FinanceModel.createOtherMovement(data);
        res.json({ success: true, message: 'Movimiento registrado correctamente' });
    } catch (error) {
        console.error('Error creating other movement:', error);
        res.status(500).json({ success: false, message: 'Error al registrar movimiento' });
    }
};

// ==================== REPORTES DE PAGO ====================

exports.paymentReports = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('finance/payment-reports', {
            ...getViewData(req, 'Reportes de Pago', 'Reportes de Pago', business || {}),
            page_functions_js: 'finance/payment-reports.js'
        });
    } catch (error) {
        console.error('Error loading payment reports:', error);
        res.status(500).render('errors/500');
    }
};

exports.getPaymentReportData = async (req, res) => {
    try {
        const { startDate, endDate } = req.query;
        const report = await FinanceModel.getPaymentReport(startDate, endDate);
        const summary = await FinanceModel.getDailySummary(new Date().toISOString().split('T')[0]);
        res.json({ success: true, report, summary });
    } catch (error) {
        console.error('Error getting payment report:', error);
        res.status(500).json({ success: false, message: 'Error al generar reporte' });
    }
};

// ==================== PAGOS MASIVOS ====================

exports.massPayments = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('finance/mass-payments', {
            ...getViewData(req, 'Pagos Masivos', 'Pagos Masivos', business || {}),
            page_functions_js: 'finance/mass-payments.js'
        });
    } catch (error) {
        console.error('Error loading mass payments:', error);
        res.status(500).render('errors/500');
    }
};

// ==================== FACTURACIÓN ELECTRÓNICA ====================

exports.electronicBilling = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('finance/electronic-billing', {
            ...getViewData(req, 'Facturación Electrónica', 'Facturación Electrónica', business || {}),
            page_functions_js: 'finance/electronic-billing.js'
        });
    } catch (error) {
        console.error('Error loading electronic billing:', error);
        res.status(500).render('errors/500');
    }
};


