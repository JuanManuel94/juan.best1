<?php
/* RUTA DEL SISTEMA */
const BASE_URL = "https://juan.wisppro.net";
/* ZONA HORARIA*/
date_default_timezone_set('America/Lima');
const MONTHS = [
  "01" => "*ENERO*",
  "02" => "*FEBRERO*",
  "03" => "*MARZO*",
  "04" => "*ABRIL*",
  "05" => "*MAYO*",
  "06" => "*JUNIO*",
  "07" => "*JULIO*",
  "08" => "*AGOSTO*",
  "09" => "*SEPTIEMBRE*",
  "10" => "*OCTUBRE*",
  "11" => "*NOVIEMBRE*",
  "12" => "*DICIEMBRE*"
];
/* CONSTANTE DE CONEXION */
const DB_HOST = "localhost";
const DB_NAME = "u204188373_juanred"; //nombre base de datos
const DB_USER = "u204188373_juanred"; //usuario
const DB_PASSWORD = "iS0u7T>r722+"; //contraseña
const DB_CHARSET = "utf8";
/* BACKUP */
const TABLES_NAME = "Tables_in_u204188373_juffdfanred"; //backups - Tables_in_nombreBD
/* DESARROLLADOR*/
const DEVELOPER = "JUNIOR ESPINOZA";
const DEVELOPER_WEBSITE = "www.sntelecom.net.pe";
const DEVELOPER_EMAIL = "walterrengifo08@gmail.com";
const DEVELOPER_MOBILE = "+51 999 220 735";
/* SISTEMA */
const NAME_SYSTEM = "INTERNET SISTEMA";
/* CONSTANTES DE ENCRIPTACION */
const METHOD = "AES-256-CBC";
const SECRET_KEY = 'SISTWISP';
const SECRET_IV = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
/* CONSTANTES DE MODULOS */
const DASHBOARD = 1;
const CLIENTS = 2;
const USERS = 3;
const TICKETS = 4;
const INCIDENTS = 5;
const BILLS = 6;
const PRODUCTS = 7;
const CATEGORIES = 8;
const SUPPLIERS = 9;
const PAYMENTS = 10;
const SERVICES = 11;
const BUSINESS = 12;
const INSTALLATIONS = 13;
const CURRENCYS = 14;
const RUNWAY = 15;
const VOUCHERS = 16;
const UNITS = 17;
const EMAIL = 18;
const RED = 19;
const WHATSAPP = 20;
/* DELIMITADORES */
const SPD = ".";
const SPM = ",";
/* USUARIOS */
const ADMINISTRATOR = 1;
const TECHNICAL = 2;
const CHARGES = 3;

