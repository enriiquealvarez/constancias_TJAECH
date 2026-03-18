# Sistema de Verificacion de Constancias - TJA Chiapas

Proyecto PHP 8.1+ con MVC ligero para verificacion publica y administracion interna de constancias.

## Requisitos
- PHP 8.1+
- MySQL 8+
- Apache con mod_rewrite habilitado

## Instalacion
1) Crear base de datos y cargar SQL:

```sql
CREATE DATABASE constancias_tja CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE constancias_tja;
SOURCE database/schema.sql;
SOURCE database/seed.sql;
```

2) Configurar credenciales en `config/database.php`.

3) Ajustar `config/app.php` si el proyecto vive en un subdirectorio:
- `base_url`: URL base (ejemplo: `http://localhost/constanciasTJAECH/`)
- `base_path`: ruta base (ejemplo: `/constanciasTJAECH`)

4) En Apache, apunte el DocumentRoot a `/public` (recomendado).

## Credenciales iniciales
- Usuario: `admin@tjaech.gob.mx`
- Contrasena: `password`

Si desea cambiarla, genere un nuevo hash con `password_hash` y actualice `database/seed.sql` o cambie el registro en la DB.

## Roles
- `ADMIN`: acceso total, incluye gestion de usuarios
- `COURSES`: gestion de cursos
- `PARTICIPANTS`: gestion de participantes
- `CERTIFICATES`: gestion de constancias
- `READONLY`: solo lectura de cursos, participantes y constancias

## Nota de actualizacion de BD
Si ya habias creado la tabla `users`, agrega la columna de rol:

```sql
ALTER TABLE users ADD COLUMN role ENUM('ADMIN','COURSES','PARTICIPANTS','CERTIFICATES','READONLY') NOT NULL DEFAULT 'ADMIN';
```

Y agrega el estatus:

```sql
ALTER TABLE users ADD COLUMN status ENUM('ACTIVE','DISABLED') NOT NULL DEFAULT 'ACTIVE';
```

Y crea la tabla de roles multiple:

```sql
CREATE TABLE user_roles (
    user_id INT NOT NULL,
    role ENUM('ADMIN','COURSES','PARTICIPANTS','CERTIFICATES','READONLY') NOT NULL,
    PRIMARY KEY (user_id, role),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

Si ya tenias usuarios, asigna roles iniciales:

```sql
INSERT IGNORE INTO user_roles (user_id, role)
SELECT id, role FROM users;
```

Si ya existe el campo `role` sin READONLY, ajusta el enum:

```sql
ALTER TABLE users MODIFY role ENUM('ADMIN','COURSES','PARTICIPANTS','CERTIFICATES','READONLY') NOT NULL DEFAULT 'ADMIN';
ALTER TABLE user_roles MODIFY role ENUM('ADMIN','COURSES','PARTICIPANTS','CERTIFICATES','READONLY') NOT NULL;
```

## Recuperacion de contrasena
Crear la tabla de tokens:

```sql
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

Configura el correo en `config/mail.php`:
- host, puerto, usuario y contrasena SMTP
- from_name y from_email
- app_url (URL publica para cargar logo)

## Rutas principales
- Publico: `/c/{token}`
- Verificacion manual: `/verificar`
- Admin: `/admin`
- Usuarios: `/admin/users`
- Auditoria: `/admin/audit`

Ejemplo publico: `/c/DEMO12345`

## Logos e imagenes
Coloque los archivos institucionales en:
- `public/assets/img/logo-tja.png`
- `public/assets/img/logo-humanismo.png`

Si no tiene imagenes aun, use placeholders con esos nombres.

## Seguridad
- Sesiones seguras
- CSRF token
- Consultas preparadas
- Sanitizacion de salida con `htmlspecialchars`

## Estructura
- `public/` front controller y assets
- `app/` MVC
- `config/` configuracion
- `database/` schema y seeds

## Notas
- Todas las operaciones del panel usan AJAX con `fetch` y SweetAlert2.
- El export CSV esta disponible en `Admin > Constancias`.
- El export CSV de auditoria esta disponible en `Admin > Auditoria`.
