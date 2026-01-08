// Middleware de autenticación
function isAuthenticated(req, res, next) {
    if (req.session && req.session.login) {
        return next();
    }
    return res.redirect('/login');
}

// Middleware para verificar si NO está autenticado (para login)
function isNotAuthenticated(req, res, next) {
    if (req.session && req.session.login) {
        return res.redirect('/dashboard');
    }
    return next();
}

// Middleware para verificar permisos de módulo
function hasPermission(moduleId) {
    return (req, res, next) => {
        if (!req.session || !req.session.permits) {
            return res.status(403).json({ status: 'error', msg: 'No tiene permisos' });
        }

        const permits = req.session.permits;
        if (permits[moduleId]) {
            return next();
        }

        return res.status(403).json({ status: 'error', msg: 'No tiene permisos para este módulo' });
    };
}

// Cargar datos de sesión en las vistas
function loadSessionData(req, res, next) {
    res.locals.session = req.session || {};
    res.locals.userData = req.session.userData || {};
    res.locals.businessData = req.session.businessData || {};
    res.locals.permits = req.session.permits || {};
    res.locals.permits_module = req.session.permits_module || {};
    next();
}

module.exports = {
    isAuthenticated,
    isNotAuthenticated,
    hasPermission,
    loadSessionData
};
