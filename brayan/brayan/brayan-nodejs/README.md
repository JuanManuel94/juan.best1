# Sistema WISP - Node.js

Sistema de gestión para proveedores de Internet (WISP) convertido de PHP a Node.js.

## Credenciales de Acceso

- **Usuario:** `admin`
- **Contraseña:** `Admin123!`

## Requisitos

- Node.js v18 o superior
- MySQL 5.7 o superior

## Instalación

1. **Configurar la base de datos:**

   ```bash
   # Importar el script SQL
   mysql -u root -p < database/seed.sql
   ```

2. **Configurar conexión a base de datos:**

   Editar `config/config.js` con tus credenciales:
   ```javascript
   const DB_HOST = "localhost";
   const DB_NAME = "nombre_base_datos";
   const DB_USER = "tu_usuario";
   const DB_PASSWORD = "tu_contraseña";
   ```

3. **Instalar dependencias (ya instaladas):**

   ```bash
   npm install
   ```

4. **Iniciar el servidor:**

   ```bash
   npm start
   ```

5. **Acceder al sistema:**

   Abrir en el navegador: `http://localhost:3000`

## Estructura del Proyecto

```
brayan-nodejs/
├── app.js                 # Punto de entrada
├── config/
│   ├── config.js          # Configuración
│   └── database.js        # Conexión MySQL
├── controllers/           # Controladores
├── models/                # Modelos
├── views/                 # Vistas EJS
├── helpers/               # Funciones utilitarias
├── middleware/            # Middleware (auth)
├── routes/                # Rutas
├── public/                # Assets (CSS, JS, imágenes)
├── uploads/               # Archivos subidos
└── database/
    └── seed.sql           # Script de base de datos
```

## Funcionalidades Convertidas

- ✅ Autenticación y sesiones
- ✅ Dashboard con estadísticas
- ✅ Gestión de clientes
- ✅ Gestión de usuarios
- ✅ Sistema de rutas equivalente al PHP
- ✅ Encriptación AES-256 compatible
- ✅ Estilos e interfaz idénticos al original

## Notas

- Los Assets (CSS, JS, imágenes) se mantienen sin cambios
- La encriptación es compatible con las contraseñas existentes
- Las rutas siguen el mismo patrón: `/controller/method/params`
