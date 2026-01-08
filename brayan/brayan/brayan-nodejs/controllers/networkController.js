const NetworkModel = require('../models/NetworkModel');
const BusinessModel = require('../models/BusinessModel');
const businessModel = new BusinessModel();

// ==================== SmartOLT ====================
exports.smartolt = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('network/smartolt', {
            page_name: 'SmartOLT',
            business: business || {},
            userData: req.session.user
        });
    } catch (error) {
        console.error('Error en smartolt:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};


exports.listOLTs = async (req, res) => {
    try {
        const olts = await NetworkModel.getAllOLTs();
        res.json({ data: olts });
    } catch (error) {
        console.error('Error listando OLTs:', error);
        res.json({ data: [] });
    }
};

exports.listONUs = async (req, res) => {
    try {
        const onus = await NetworkModel.getAllONUs();
        res.json({ data: onus });
    } catch (error) {
        console.error('Error listando ONUs:', error);
        res.json({ data: [] });
    }
};

exports.createOLT = async (req, res) => {
    try {
        await NetworkModel.createOLT(req.body);
        res.json({ success: true, message: 'OLT creada correctamente' });
    } catch (error) {
        console.error('Error creando OLT:', error);
        res.json({ success: false, message: error.message });
    }
};

exports.updateOLT = async (req, res) => {
    try {
        await NetworkModel.updateOLT(req.body.id, req.body);
        res.json({ success: true, message: 'OLT actualizada correctamente' });
    } catch (error) {
        console.error('Error actualizando OLT:', error);
        res.json({ success: false, message: error.message });
    }
};

exports.deleteOLT = async (req, res) => {
    try {
        await NetworkModel.deleteOLT(req.body.id);
        res.json({ success: true, message: 'OLT eliminada correctamente' });
    } catch (error) {
        console.error('Error eliminando OLT:', error);
        res.json({ success: false, message: error.message });
    }
};

exports.selectOLT = async (req, res) => {
    try {
        const olt = await NetworkModel.getOLTById(req.params.id);
        res.json(olt || {});
    } catch (error) {
        console.error('Error seleccionando OLT:', error);
        res.json({});
    }
};

// ==================== Redes IPv4 ====================
exports.ipv4 = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        const routers = await NetworkModel.getAllRouters();
        res.render('network/ipv4', {
            page_name: 'Redes IPv4',
            business: business || {},
            userData: req.session.user,
            routers: routers
        });
    } catch (error) {
        console.error('Error en ipv4:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};

exports.listNetworks = async (req, res) => {
    try {
        const networks = await NetworkModel.getAllNetworks();
        res.json({ data: networks });
    } catch (error) {
        console.error('Error listando redes:', error);
        res.json({ data: [] });
    }
};

exports.createNetwork = async (req, res) => {
    try {
        await NetworkModel.createNetwork(req.body);
        res.json({ success: true, message: 'Red creada correctamente' });
    } catch (error) {
        console.error('Error creando red:', error);
        res.json({ success: false, message: error.message });
    }
};

exports.updateNetwork = async (req, res) => {
    try {
        await NetworkModel.updateNetwork(req.body.id, req.body);
        res.json({ success: true, message: 'Red actualizada correctamente' });
    } catch (error) {
        console.error('Error actualizando red:', error);
        res.json({ success: false, message: error.message });
    }
};

exports.deleteNetwork = async (req, res) => {
    try {
        await NetworkModel.deleteNetwork(req.body.id);
        res.json({ success: true, message: 'Red eliminada correctamente' });
    } catch (error) {
        console.error('Error eliminando red:', error);
        res.json({ success: false, message: error.message });
    }
};

exports.selectNetwork = async (req, res) => {
    try {
        const network = await NetworkModel.getNetworkById(req.params.id);
        res.json(network || {});
    } catch (error) {
        console.error('Error seleccionando red:', error);
        res.json({});
    }
};

// ==================== Monitoreo ====================
exports.monitoring = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('network/monitoring', {
            page_name: 'Monitoreo',
            business: business || {},
            userData: req.session.user
        });
    } catch (error) {
        console.error('Error en monitoreo:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};

exports.listHosts = async (req, res) => {
    try {
        const hosts = await NetworkModel.getAllHosts();
        res.json({ data: hosts });
    } catch (error) {
        console.error('Error listando hosts:', error);
        res.json({ data: [] });
    }
};

exports.createHost = async (req, res) => {
    try {
        await NetworkModel.createHost(req.body);
        res.json({ success: true, message: 'Host creado correctamente' });
    } catch (error) {
        console.error('Error creando host:', error);
        res.json({ success: false, message: error.message });
    }
};

exports.updateHost = async (req, res) => {
    try {
        await NetworkModel.updateHost(req.body.id, req.body);
        res.json({ success: true, message: 'Host actualizado correctamente' });
    } catch (error) {
        console.error('Error actualizando host:', error);
        res.json({ success: false, message: error.message });
    }
};

exports.deleteHost = async (req, res) => {
    try {
        await NetworkModel.deleteHost(req.body.id);
        res.json({ success: true, message: 'Host eliminado correctamente' });
    } catch (error) {
        console.error('Error eliminando host:', error);
        res.json({ success: false, message: error.message });
    }
};

exports.selectHost = async (req, res) => {
    try {
        const host = await NetworkModel.getHostById(req.params.id);
        res.json(host || {});
    } catch (error) {
        console.error('Error seleccionando host:', error);
        res.json({});
    }
};

// ==================== Tráfico ====================
exports.traffic = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('network/traffic', {
            page_name: 'Monitor de Tráfico',
            business: business || {},
            userData: req.session.user
        });
    } catch (error) {
        console.error('Error en tráfico:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};

// ==================== IPs Visitadas ====================
exports.visitedIps = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('network/visited-ips', {
            page_name: 'IPs Visitadas',
            business: business || {},
            userData: req.session.user
        });
    } catch (error) {
        console.error('Error en IPs visitadas:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};

// ==================== Blacklist ====================
exports.blacklist = async (req, res) => {
    try {
        const business = await businessModel.showBusiness();
        res.render('network/blacklist', {
            page_name: 'Monitor BlackList',
            business: business || {},
            userData: req.session.user
        });
    } catch (error) {
        console.error('Error en blacklist:', error);
        res.status(500).render('errors/500', { error: error.message });
    }
};
