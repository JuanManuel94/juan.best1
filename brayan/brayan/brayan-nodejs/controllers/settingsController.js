const SettingsModel = require('../models/SettingsModel');
const BusinessModel = require('../models/BusinessModel');
const businessModel = new BusinessModel();

// ==================== Settings Index ====================
exports.index = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('settings/index', {
            page_name: 'Ajustes',
            business: business || {},
            userData: req.session.user
        });
    } catch (error) {
        console.error('Error en ajustes:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};

// ==================== General Settings ====================
exports.general = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        const settings = await SettingsModel.getSettingsByCategory('general');
        res.render('settings/general', {
            page_name: 'Configuración General',
            business: business || {},
            userData: req.session.user,
            settings: settings
        });
    } catch (error) {
        console.error('Error en configuración general:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};

exports.updateGeneral = async (req, res) => {
    try {
        await SettingsModel.updateMultipleSettings(req.body);
        res.json({ success: true, message: 'Configuración actualizada correctamente' });
    } catch (error) {
        console.error('Error actualizando configuración:', error);
        res.json({ success: false, message: error.message });
    }
};

// ==================== Company/Business ====================
exports.company = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('settings/company', {
            page_name: 'Datos de Empresa',
            business: business || {},
            userData: req.session.user
        });
    } catch (error) {
        console.error('Error en datos de empresa:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};

exports.updateCompany = async (req, res) => {
    try {
        await SettingsModel.updateBusinessData(req.body);
        res.json({ success: true, message: 'Datos de empresa actualizados correctamente' });
    } catch (error) {
        console.error('Error actualizando empresa:', error);
        res.json({ success: false, message: error.message });
    }
};

// ==================== Billing Configuration ====================
exports.billingConfig = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        const settings = await SettingsModel.getSettingsByCategory('billing');
        res.render('settings/billing-config', {
            page_name: 'Configuración de Facturación',
            business: business || {},
            userData: req.session.user,
            settings: settings
        });
    } catch (error) {
        console.error('Error en configuración de facturación:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};

exports.updateBillingConfig = async (req, res) => {
    try {
        await SettingsModel.updateMultipleSettings(req.body);
        res.json({ success: true, message: 'Configuración de facturación actualizada' });
    } catch (error) {
        console.error('Error actualizando facturación:', error);
        res.json({ success: false, message: error.message });
    }
};

// ==================== API / Webhooks ====================
exports.apiWebhooks = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        const settings = await SettingsModel.getAPISettings();
        res.render('settings/api-webhooks', {
            page_name: 'API / Webhooks',
            business: business || {},
            userData: req.session.user,
            settings: settings
        });
    } catch (error) {
        console.error('Error en API/Webhooks:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};

exports.updateAPISettings = async (req, res) => {
    try {
        await SettingsModel.updateMultipleSettings(req.body);
        res.json({ success: true, message: 'Configuración de API actualizada' });
    } catch (error) {
        console.error('Error actualizando API:', error);
        res.json({ success: false, message: error.message });
    }
};

// ==================== Get All Settings ====================
exports.getAllSettings = async (req, res) => {
    try {
        const settings = await SettingsModel.getAllSettings();
        res.json({ data: settings });
    } catch (error) {
        console.error('Error obteniendo configuración:', error);
        res.json({ data: [] });
    }
};
