const MessagingModel = require('../models/MessagingModel');
const BusinessModel = require('../models/BusinessModel');
const businessModel = new BusinessModel();

// ==================== WhatsApp Chat ====================
exports.whatsapp = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('messaging/whatsapp', {
            page_name: 'Chat WhatsApp',
            business: business || {},
            userData: req.session.user
        });
    } catch (error) {
        console.error('Error en whatsapp:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};

exports.listAllMessages = async (req, res) => {
    try {
        const messages = await MessagingModel.getAllMessages();
        res.json({ data: messages });
    } catch (error) {
        console.error('Error listando mensajes:', error);
        res.json({ data: [] });
    }
};

// ==================== Mensajes Enviados ====================
exports.sentMessages = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('messaging/sent-messages', {
            page_name: 'Mensajes Enviados',
            business: business || {},
            userData: req.session.user
        });
    } catch (error) {
        console.error('Error en mensajes enviados:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};

exports.listSentMessages = async (req, res) => {
    try {
        const messages = await MessagingModel.getSentMessages();
        res.json({ data: messages });
    } catch (error) {
        console.error('Error listando mensajes enviados:', error);
        res.json({ data: [] });
    }
};

// ==================== Mensajes Recibidos ====================
exports.receivedMessages = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('messaging/received-messages', {
            page_name: 'Mensajes Recibidos',
            business: business || {},
            userData: req.session.user
        });
    } catch (error) {
        console.error('Error en mensajes recibidos:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};

exports.listReceivedMessages = async (req, res) => {
    try {
        const messages = await MessagingModel.getReceivedMessages();
        res.json({ data: messages });
    } catch (error) {
        console.error('Error listando mensajes recibidos:', error);
        res.json({ data: [] });
    }
};

// ==================== Enviar Mensaje ====================
exports.sendMessage = async (req, res) => {
    try {
        const data = {
            ...req.body,
            direction: 'sent',
            status: 'sent' // Simulado
        };
        await MessagingModel.createMessage(data);
        res.json({ success: true, message: 'Mensaje enviado correctamente' });
    } catch (error) {
        console.error('Error enviando mensaje:', error);
        res.json({ success: false, message: error.message });
    }
};

// ==================== Templates ====================
exports.listTemplates = async (req, res) => {
    try {
        const templates = await MessagingModel.getAllTemplates();
        res.json({ data: templates });
    } catch (error) {
        console.error('Error listando plantillas:', error);
        res.json({ data: [] });
    }
};

exports.createTemplate = async (req, res) => {
    try {
        await MessagingModel.createTemplate(req.body);
        res.json({ success: true, message: 'Plantilla creada correctamente' });
    } catch (error) {
        console.error('Error creando plantilla:', error);
        res.json({ success: false, message: error.message });
    }
};

