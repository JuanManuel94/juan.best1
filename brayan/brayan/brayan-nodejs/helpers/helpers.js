const crypto = require('crypto');
const nodemailer = require('nodemailer');
const config = require('../config/config');

// Encriptación AES-256-CBC compatible con PHP openssl_encrypt
function encrypt(text) {
    if (!text) return '';
    try {
        // Crear key y iv exactamente como PHP
        const key = crypto.createHash('sha256').update(config.SECRET_KEY).digest();
        const iv = crypto.createHash('sha256').update(config.SECRET_IV).digest().slice(0, 16);

        const cipher = crypto.createCipheriv('aes-256-cbc', key, iv);
        let encrypted = cipher.update(text, 'utf8', 'base64');
        encrypted += cipher.final('base64');

        // PHP hace base64_encode del resultado, así que hacemos lo mismo
        return Buffer.from(encrypted, 'utf8').toString('base64');
    } catch (error) {
        console.error('Encrypt error:', error);
        return '';
    }
}

function decrypt(text) {
    if (!text) return '';
    try {
        const key = crypto.createHash('sha256').update(config.SECRET_KEY).digest();
        const iv = crypto.createHash('sha256').update(config.SECRET_IV).digest().slice(0, 16);

        // Decodificar el doble base64
        const decoded = Buffer.from(text, 'base64').toString('utf8');

        const decipher = crypto.createDecipheriv('aes-256-cbc', key, iv);
        let decrypted = decipher.update(decoded, 'base64', 'utf8');
        decrypted += decipher.final('utf8');
        return decrypted;
    } catch (error) {
        console.error('Decrypt error:', error);
        return '';
    }
}

// Formateo de dinero
function formatMoney(amount) {
    if (!amount) return '0.00';
    return parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, config.SPM);
}

// Limpiar cadenas
function strClean(str) {
    if (!str) return '';
    let string = str.replace(/\s+/g, ' ').trim();
    string = string.replace(/<script>/gi, '');
    string = string.replace(/<\/script>/gi, '');
    string = string.replace(/<script src>/gi, '');
    string = string.replace(/<script type=>/gi, '');
    string = string.replace(/SELECT \* FROM/gi, '');
    string = string.replace(/DELETE FROM/gi, '');
    string = string.replace(/INSERT INTO/gi, '');
    string = string.replace(/SELECT COUNT\(\*\) FROM/gi, '');
    string = string.replace(/DROP TABLE/gi, '');
    string = string.replace(/OR '1'='1/gi, '');
    string = string.replace(/OR "1"="1"/gi, '');
    string = string.replace(/is NULL; --/gi, '');
    string = string.replace(/LIKE '/gi, '');
    string = string.replace(/LIKE "/gi, '');
    string = string.replace(/OR 'a'='a/gi, '');
    string = string.replace(/OR "a"="a/gi, '');
    string = string.replace(/\^/g, '');
    string = string.replace(/\[/g, '');
    string = string.replace(/\]/g, '');
    string = string.replace(/==/g, '');
    string = string.replace(/"/g, '');
    string = string.replace(/''/g, '');
    return string;
}

// Generar token
function generateToken() {
    return crypto.randomBytes(40).toString('hex');
}

// Generar contraseña
function generatePassword(length = 10) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
    let password = '';
    for (let i = 0; i < length; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return password;
}

// Tiempo transcurrido
function timeElapsed(date) {
    const timestamp = new Date(date).getTime();
    const now = Date.now();
    const diff = Math.floor((now - timestamp) / 1000);

    const intervals = [
        { label: 'año', seconds: 31536000 },
        { label: 'mes', seconds: 2592000 },
        { label: 'dia', seconds: 86400 },
        { label: 'hora', seconds: 3600 },
        { label: 'minuto', seconds: 60 },
        { label: 'segundo', seconds: 1 }
    ];

    for (const interval of intervals) {
        const count = Math.floor(diff / interval.seconds);
        if (count >= 1) {
            return `Hace ${count} ${interval.label}${count > 1 ? 's' : ''}`;
        }
    }
    return 'Hace un momento';
}

// Meses
function months() {
    return ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
}

function months2() {
    return {
        "1": "Enero", "2": "Febrero", "3": "Marzo", "4": "Abril",
        "5": "Mayo", "6": "Junio", "7": "Julio", "8": "Agosto",
        "9": "Septiembre", "10": "Octubre", "11": "Noviembre", "12": "Diciembre"
    };
}

// Nombre del día
function dayName(date) {
    const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    return days[new Date(date).getDay()];
}

// Fecha en letras
function dateLetters(date) {
    const d = new Date(date);
    const day = dayName(date);
    const numberDay = d.getDate();
    const year = d.getFullYear();
    const month = months()[d.getMonth()];
    return `${day}, ${numberDay} de ${month} del ${year}`;
}

// Limpiar acentos
function clearCadena(cadena) {
    if (!cadena) return '';
    return cadena.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
}

// Tamaño de archivo formateado
function filesizeFormatted(bytes) {
    const units = ['B', 'KB', 'MB', 'GB', 'TB'];
    let i = 0;
    while (bytes >= 1024 && i < units.length - 1) {
        bytes /= 1024;
        i++;
    }
    return `${bytes.toFixed(2)} ${units[i]}`;
}

// Enviar email
async function sendMail(information, template) {
    try {
        const transporter = nodemailer.createTransport({
            host: information.host,
            port: information.port,
            secure: information.port === 465,
            auth: {
                user: information.sender,
                pass: information.password
            }
        });

        const mailOptions = {
            from: `"${information.name_sender}" <${information.sender}>`,
            to: information.addressee,
            subject: information.affair,
            html: template
        };

        await transporter.sendMail(mailOptions);
        return true;
    } catch (error) {
        console.error('Error sending email:', error);
        return false;
    }
}

// Base URL
function baseUrl() {
    return config.BASE_URL;
}

// Base Style (Assets path)
function baseStyle() {
    return config.BASE_URL + '/public';
}

// Países (lista simplificada)
const countries = require('./countries');

function countrySelector(defaultCountry = "") {
    let output = "";
    for (const country of countries) {
        const selected = country.code === defaultCountry.toUpperCase() ? 'selected' : '';
        output += `<option value="${country.code}" ${selected}>${country.name} (+${country.code})</option>`;
    }
    return output;
}

module.exports = {
    encrypt,
    decrypt,
    formatMoney,
    strClean,
    generateToken,
    generatePassword,
    timeElapsed,
    months,
    months2,
    dayName,
    dateLetters,
    clearCadena,
    filesizeFormatted,
    sendMail,
    baseUrl,
    baseStyle,
    countrySelector,
    countries
};
