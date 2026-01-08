const UsersModel = require('../models/UsersModel');
const BusinessModel = require('../models/BusinessModel');
const PermissionsModel = require('../models/PermissionsModel');
const helpers = require('../helpers/helpers');
const config = require('../config/config');

class UsersController {
    constructor() {
        this.model = new UsersModel();
        this.businessModel = new BusinessModel();
        this.permissionsModel = new PermissionsModel();
    }

    async users(req, res) {
        try {
            const permits = await this.permissionsModel.modulePermissions(req.session.userData.profileid);
            req.session.permits = permits;
            req.session.permits_module = permits[config.MODULES.USERS] || {};

            const business = await this.businessModel.showBusiness();
            const documents = await this.model.listDocuments();
            const profiles = await this.model.listProfiles();

            res.render('users/users', {
                page_name: 'Usuarios del sistema',
                page_functions_js: 'users.js',
                business: business || {},
                documents: documents || [],
                profiles: profiles || []
            });

        } catch (error) {
            console.error('Users error:', error);
            res.render('errors/500', {
                page_name: 'Error',
                business: req.session.businessData || {}
            });
        }
    }

    async list(req, res) {
        try {
            const data = await this.model.listRecords(req.session.idUser);
            return res.json({ data: data });
        } catch (error) {
            console.error('List users error:', error);
            return res.json({ data: [] });
        }
    }

    async create(req, res) {
        try {
            const data = {
                names: helpers.strClean(req.body.names),
                surnames: helpers.strClean(req.body.surnames),
                documentid: parseInt(req.body.documentid) || 1,
                document: helpers.strClean(req.body.document),
                mobile: helpers.strClean(req.body.mobile),
                email: helpers.strClean(req.body.email?.toLowerCase()),
                profileid: parseInt(req.body.profileid),
                username: helpers.strClean(req.body.username?.toLowerCase()),
                password: helpers.encrypt(req.body.password),
                state: parseInt(req.body.state) || 1
            };

            if (!data.names || !data.surnames || !data.username || !req.body.password) {
                return res.json({
                    status: 'warning',
                    msg: 'Los campos son obligatorios.'
                });
            }

            const result = await this.model.create(data);

            if (result === 'exists') {
                return res.json({
                    status: 'warning',
                    msg: 'Ya existe un usuario con este documento.'
                });
            }

            return res.json({
                status: result === 'success' ? 'success' : 'error',
                msg: result === 'success' ? 'Usuario creado correctamente.' : 'Error al crear usuario.'
            });

        } catch (error) {
            console.error('Create user error:', error);
            return res.json({
                status: 'error',
                msg: 'Error en el servidor.'
            });
        }
    }

    async modify(req, res) {
        try {
            const data = {
                id: parseInt(req.body.id),
                names: helpers.strClean(req.body.names),
                surnames: helpers.strClean(req.body.surnames),
                documentid: parseInt(req.body.documentid) || 1,
                document: helpers.strClean(req.body.document),
                mobile: helpers.strClean(req.body.mobile),
                email: helpers.strClean(req.body.email?.toLowerCase()),
                profileid: parseInt(req.body.profileid),
                username: helpers.strClean(req.body.username?.toLowerCase()),
                password: req.body.password ? helpers.encrypt(req.body.password) : null,
                state: parseInt(req.body.state) || 1
            };

            if (!data.id || !data.names || !data.surnames || !data.username) {
                return res.json({
                    status: 'warning',
                    msg: 'Los campos son obligatorios.'
                });
            }

            const result = await this.model.modify(data);

            if (result === 'exists') {
                return res.json({
                    status: 'warning',
                    msg: 'Ya existe un usuario con este documento.'
                });
            }

            return res.json({
                status: result === 'success' ? 'success' : 'error',
                msg: result === 'success' ? 'Usuario modificado correctamente.' : 'Error al modificar usuario.'
            });

        } catch (error) {
            console.error('Modify user error:', error);
            return res.json({
                status: 'error',
                msg: 'Error en el servidor.'
            });
        }
    }

    async selectRecord(req, res) {
        try {
            const id = parseInt(req.params.id);
            const data = await this.model.selectRecord(id);
            return res.json(data || {});
        } catch (error) {
            console.error('Select user error:', error);
            return res.json({});
        }
    }

    async remove(req, res) {
        try {
            const id = parseInt(req.body.id);
            const result = await this.model.remove(id);

            return res.json({
                status: result === 'success' ? 'success' : 'error',
                msg: result === 'success' ? 'Usuario eliminado correctamente.' : 'Error al eliminar usuario.'
            });

        } catch (error) {
            console.error('Remove user error:', error);
            return res.json({
                status: 'error',
                msg: 'Error en el servidor.'
            });
        }
    }
}

module.exports = new UsersController();
