const LoginModel = require('../models/LoginModel');
const BusinessModel = require('../models/BusinessModel');
const PermissionsModel = require('../models/PermissionsModel');
const helpers = require('../helpers/helpers');

class LoginController {
    constructor() {
        this.model = new LoginModel();
        this.businessModel = new BusinessModel();
        this.permissionsModel = new PermissionsModel();
    }

    // Mostrar página de login
    async login(req, res) {
        try {
            const business = await this.businessModel.showBusiness();
            req.session.businessData = business;

            res.render('login/login', {
                page_name: 'Login',
                page_functions_js: 'login.js',
                business: business || {},
                cookies: req.cookies
            });
        } catch (error) {
            console.error('Login error:', error);
            res.render('login/login', {
                page_name: 'Login',
                page_functions_js: 'login.js',
                business: {},
                cookies: {}
            });
        }
    }

    // Validar credenciales
    async validation(req, res) {
        try {
            const { username, password, remember } = req.body;

            if (!username || !password) {
                return res.json({
                    status: 'warning',
                    msg: 'El usuario y contraseña son campos obligatorios.'
                });
            }

            const cleanUsername = helpers.strClean(username.toLowerCase());
            const encryptedPassword = helpers.encrypt(password);

            const user = await this.model.validation(cleanUsername, encryptedPassword);

            if (!user) {
                return res.json({
                    status: 'warning',
                    msg: 'Usuario o contraseña es incorrecta.'
                });
            }

            if (user.state !== 1) {
                return res.json({
                    status: 'error',
                    msg: 'El usuario se encuentra desactivado, comuniquese con su administrador.'
                });
            }

            // Configurar cookies si remember está activo
            if (remember) {
                const oneYear = 365 * 24 * 60 * 60 * 1000;
                res.cookie('username', cleanUsername, { maxAge: oneYear });
                res.cookie('password', password, { maxAge: oneYear });
            } else {
                res.clearCookie('username');
                res.clearCookie('password');
            }

            // Configurar sesión
            req.session.idUser = user.id;
            req.session.login = true;

            // Cargar datos de usuario y permisos
            const userData = await this.model.loginSession(user.id);
            req.session.userData = userData;

            const business = await this.businessModel.showBusiness();
            req.session.businessData = business;

            const permits = await this.permissionsModel.modulePermissions(userData.profileid);
            req.session.permits = permits;

            return res.json({
                status: 'success',
                msg: 'ok'
            });

        } catch (error) {
            console.error('Validation error:', error);
            return res.json({
                status: 'error',
                msg: 'Error en el servidor.'
            });
        }
    }

    // Solicitar restablecimiento de contraseña
    async reset(req, res) {
        try {
            const { email } = req.body;

            if (!email) {
                return res.json({
                    status: 'error',
                    msg: 'Ingrese un correo electrónico valido.'
                });
            }

            const cleanEmail = helpers.strClean(email.toLowerCase());
            const user = await this.model.validationEmail(cleanEmail);

            if (!user) {
                return res.json({
                    status: 'not_exist',
                    msg: 'No existe ningún operador con este correo.'
                });
            }

            const token = helpers.generateToken();
            const business = await this.businessModel.showBusiness();
            const urlRecovery = `${helpers.baseUrl()}/login/restore/${helpers.encrypt(cleanEmail)}/${token}`;

            // Aquí iría el envío de email
            const updateToken = await this.model.updateToken(user.id, token);

            if (updateToken === 'success') {
                return res.json({
                    status: 'success',
                    msg: 'Se le envió un correo, revise su bandeja de entrada de su cuenta de correo.'
                });
            } else {
                return res.json({
                    status: 'error',
                    msg: 'No es posible realizar el proceso, intenta más tarde.'
                });
            }

        } catch (error) {
            console.error('Reset error:', error);
            return res.json({
                status: 'error',
                msg: 'Error en el servidor.'
            });
        }
    }

    // Página de restaurar contraseña
    async restore(req, res) {
        try {
            const { emailEncrypted, token } = req.params;

            if (!emailEncrypted || !token) {
                return res.redirect('/login');
            }

            const email = helpers.decrypt(emailEncrypted);
            const cleanEmail = helpers.strClean(email);
            const cleanToken = helpers.strClean(token);

            const user = await this.model.userInformation(cleanEmail, cleanToken);

            if (!user) {
                return res.redirect('/login');
            }

            const business = await this.businessModel.showBusiness();

            res.render('login/restore_password', {
                email: cleanEmail,
                token: cleanToken,
                id: helpers.encrypt(user.id.toString()),
                page_name: 'Restaurar contraseña',
                page_functions_js: 'restore.js',
                business: business || {}
            });

        } catch (error) {
            console.error('Restore error:', error);
            res.redirect('/login');
        }
    }

    // Actualizar contraseña
    async updatePassword(req, res) {
        try {
            const { id, email, token, password, passwordConfirm } = req.body;

            if (!id || !email || !token || !password || !passwordConfirm) {
                return res.json({
                    status: 'error',
                    msg: 'Los campos son obligatorios.'
                });
            }

            if (password !== passwordConfirm) {
                return res.json({
                    status: 'error',
                    msg: 'Las contraseñas no coinciden.'
                });
            }

            const decryptedId = parseInt(helpers.decrypt(id));
            const cleanEmail = helpers.strClean(email);
            const cleanToken = helpers.strClean(token);

            const user = await this.model.userInformation(cleanEmail, cleanToken);

            if (!user) {
                return res.json({
                    status: 'error',
                    msg: 'No se encontró información del usuario.'
                });
            }

            const encryptedPassword = helpers.encrypt(password);
            const result = await this.model.updatePassword(decryptedId, encryptedPassword);

            if (result === 'success') {
                return res.json({
                    status: 'success',
                    msg: 'Tu contraseña ha sido restablecida.'
                });
            } else {
                return res.json({
                    status: 'error',
                    msg: 'No es posible realizar el proceso, intente más tarde.'
                });
            }

        } catch (error) {
            console.error('UpdatePassword error:', error);
            return res.json({
                status: 'error',
                msg: 'Error en el servidor.'
            });
        }
    }

    // Cerrar sesión
    async logout(req, res) {
        req.session.destroy((err) => {
            if (err) {
                console.error('Logout error:', err);
            }
            res.redirect('/login');
        });
    }
}

module.exports = new LoginController();
