const express = require('express');
const session = require('express-session');
const cookieParser = require('cookie-parser');
const path = require('path');
const config = require('./config/config');
const { loadSessionData } = require('./middleware/auth');
const helpers = require('./helpers/helpers');

const app = express();
const PORT = process.env.PORT || 3000;

// Configuración del motor de vistas
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(cookieParser());

// Archivos estáticos
app.use('/public', express.static(path.join(__dirname, 'public')));
app.use('/Assets', express.static(path.join(__dirname, 'public')));
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));

// Configuración de sesiones
app.use(session({
    secret: config.SECRET_KEY,
    resave: false,
    saveUninitialized: false,
    cookie: {
        maxAge: 24 * 60 * 60 * 1000 // 24 horas
    }
}));

// Cargar datos de sesión en vistas
app.use(loadSessionData);

// Variables globales para las vistas
app.use((req, res, next) => {
    res.locals.base_url = config.BASE_URL;
    res.locals.base_style = () => config.BASE_URL + '/public';
    res.locals.DEVELOPER = config.DEVELOPER;
    res.locals.NAME_SYSTEM = config.NAME_SYSTEM;
    res.locals.helpers = helpers;
    next();
});

// Importar rutas
const routes = require('./routes');
app.use('/', routes);

// Ruta por defecto
app.get('/', (req, res) => {
    res.redirect('/login');
});

// Manejo de errores 404
app.use((req, res) => {
    res.status(404).render('errors/404', {
        page_name: 'Error 404',
        business: req.session.businessData || {}
    });
});

// Manejo de errores generales
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).render('errors/500', {
        page_name: 'Error 500',
        business: req.session.businessData || {}
    });
});

// Iniciar servidor
app.listen(PORT, () => {
    console.log(`🚀 Servidor corriendo en ${config.BASE_URL}`);
    console.log(`📦 Puerto: ${PORT}`);
});

module.exports = app;
