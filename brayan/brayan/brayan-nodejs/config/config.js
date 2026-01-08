/* RUTA DEL SISTEMA */
const BASE_URL = "http://localhost:3000";

/* ZONA HORARIA */
process.env.TZ = 'America/Lima';

/* MESES */
const MONTHS = {
    "01": "*ENERO*",
    "02": "*FEBRERO*",
    "03": "*MARZO*",
    "04": "*ABRIL*",
    "05": "*MAYO*",
    "06": "*JUNIO*",
    "07": "*JULIO*",
    "08": "*AGOSTO*",
    "09": "*SEPTIEMBRE*",
    "10": "*OCTUBRE*",
    "11": "*NOVIEMBRE*",
    "12": "*DICIEMBRE*"
};

/* CONSTANTE DE CONEXION POSTGRESQL */
const DB_HOST = "localhost";
const DB_NAME = "wisp_system";
const DB_USER = "root";
const DB_PASSWORD = "";
const DB_PORT = 3306;
const DB_CHARSET = "utf8";

/* BACKUP */
const TABLES_NAME = "Tables_in_wisp_system";

/* DESARROLLADOR */
const DEVELOPER = "JUNIOR ESPINOZA";
const DEVELOPER_WEBSITE = "www.sntelecom.net.pe";
const DEVELOPER_EMAIL = "walterrengifo08@gmail.com";
const DEVELOPER_MOBILE = "+51 999 220 735";

/* SISTEMA */
const NAME_SYSTEM = "INTERNET SISTEMA";

/* CONSTANTES DE ENCRIPTACION */
const METHOD = "aes-256-cbc";
const SECRET_KEY = 'SISTWISP';
const SECRET_IV = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';

/* CONSTANTES DE MODULOS */
const MODULES = {
    DASHBOARD: 1,
    CLIENTS: 2,
    USERS: 3,
    TICKETS: 4,
    INCIDENTS: 5,
    BILLS: 6,
    PRODUCTS: 7,
    CATEGORIES: 8,
    SUPPLIERS: 9,
    PAYMENTS: 10,
    SERVICES: 11,
    BUSINESS: 12,
    INSTALLATIONS: 13,
    CURRENCYS: 14,
    RUNWAY: 15,
    VOUCHERS: 16,
    UNITS: 17,
    EMAIL: 18,
    RED: 19,
    WHATSAPP: 20
};

/* DELIMITADORES */
const SPD = ".";
const SPM = ",";

/* USUARIOS */
const USER_TYPES = {
    ADMINISTRATOR: 1,
    TECHNICAL: 2,
    CHARGES: 3
};

module.exports = {
    BASE_URL,
    MONTHS,
    DB_HOST,
    DB_NAME,
    DB_USER,
    DB_PASSWORD,
    DB_PORT,
    DB_CHARSET,
    TABLES_NAME,
    DEVELOPER,
    DEVELOPER_WEBSITE,
    DEVELOPER_EMAIL,
    DEVELOPER_MOBILE,
    NAME_SYSTEM,
    METHOD,
    SECRET_KEY,
    SECRET_IV,
    MODULES,
    SPD,
    SPM,
    USER_TYPES
};
