-- ============================================================================
-- Módulo de Certificaciones (CEMASC / OIMASC)
-- Ejecutar una sola vez sobre la base de datos de constancias (phpMyAdmin o CLI).
-- ============================================================================

-- 1) Nuevo rol CEMASC: solo tiene acceso al módulo de Certificaciones.
ALTER TABLE users
    MODIFY COLUMN role ENUM('ADMIN','COURSES','PARTICIPANTS','CERTIFICATES','READONLY','CEMASC') NOT NULL DEFAULT 'ADMIN';

ALTER TABLE user_roles
    MODIFY COLUMN role ENUM('ADMIN','COURSES','PARTICIPANTS','CERTIFICATES','READONLY','CEMASC') NOT NULL;

-- 2) Tabla de certificaciones. Independiente de constancias/cursos/participantes.
--    La vigencia (VIGENTE / NO VIGENTE) se calcula al consultar, comparando
--    valid_from / valid_until contra la fecha actual; no requiere tareas programadas.
CREATE TABLE IF NOT EXISTS certifications (
    id INT NOT NULL AUTO_INCREMENT,
    cert_number VARCHAR(30) NOT NULL,
    full_name VARCHAR(200) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    valid_from DATE NOT NULL,
    valid_until DATE NOT NULL,
    status ENUM('ACTIVE','REVOKED') NOT NULL DEFAULT 'ACTIVE',
    token VARCHAR(40) NOT NULL,
    created_by INT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_certifications_number (cert_number),
    UNIQUE KEY uq_certifications_token (token),
    KEY idx_certifications_valid_until (valid_until)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
