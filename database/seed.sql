INSERT INTO users (name, email, role, status, password_hash) VALUES
('Administrador TJA', 'admin@tjaech.gob.mx', 'ADMIN', 'ACTIVE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO user_roles (user_id, role) VALUES
(1, 'ADMIN');

INSERT INTO courses (name, edition, start_date, end_date, modality, area) VALUES
('Curso de Actualizacion Administrativa', '2024-A', '2024-05-20', '2024-05-24', 'Presencial', 'Capacitacion');

INSERT INTO participants (full_name, email, type) VALUES
('Maria Fernanda Ruiz Gomez', 'mruiz@example.com', 'INTERNAL');

INSERT INTO certificates (participant_id, course_id, doc_type, status, token, created_at, updated_at) VALUES
(1, 1, 'Constancia', 'VERIFIED', 'DEMO12345', NOW(), NOW());
