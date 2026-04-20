-- =====================================================
--  USUARIS D'EXEMPLE PER A LA PLATAFORMA TRACKING D'Hores
-- =====================================================
-- TOTS ELS USUARIS TENEN COM A CONTRASENYA PER DEFECTE:  123456
-- Pots canviar-les més tard des del panell d'administració

USE tracking_hores;

-- 🔹 USUARI SUPER ADMINISTRADOR (ja existeix per defecte)
-- Correu: admin@empresa.cat
-- Contrasenya: password

-- -----------------------------------------------------
-- 🔹 USUARIS ADMINISTRADORS
-- -----------------------------------------------------
INSERT INTO users (dni, email, password, nom, cognoms, departament, rol) VALUES 
('11111111B', 'maria.lopez@empresa.cat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maria', 'López García', 'RRHH', 'administrador'),
('22222222C', 'juan.martinez@empresa.cat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan', 'Martínez Pérez', 'Tecnologia', 'administrador');

-- -----------------------------------------------------
-- 🔹 USUARIS EMPLEATS NORMALS
-- -----------------------------------------------------
INSERT INTO users (dni, email, password, nom, cognoms, departament, rol, horari_entrada, horari_sortida) VALUES 
('33333333D', 'ana.sanchez@empresa.cat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Sánchez Ruiz', 'Disseny', 'empleat', '08:00:00', '16:00:00'),
('44444444E', 'carlos.fernandez@empresa.cat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos', 'Fernández Díaz', 'Desenvolupament', 'empleat', '09:00:00', '17:00:00'),
('55555555F', 'laura.garcia@empresa.cat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Laura', 'García Moreno', 'Marketing', 'empleat', '08:30:00', '16:30:00'),
('66666666G', 'david.torres@empresa.cat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David', 'Torres Jiménez', 'Desenvolupament', 'empleat', '10:00:00', '18:00:00'),
('77777777H', 'marta.ramirez@empresa.cat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marta', 'Ramírez Alonso', 'Finances', 'empleat', '08:00:00', '15:00:00'),
('88888888I', 'sergio.molina@empresa.cat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sergio', 'Molina Ortega', 'Producció', 'empleat', '07:00:00', '15:00:00');

-- =====================================================
-- ✅ RESUM D'USUARIS CREADES:
-- =====================================================
--
-- | ROL           | CORREU                        | CONTRASENYA |
-- |---------------|-------------------------------|-------------|
-- | SuperAdmin    | admin@empresa.cat             | password    |
-- | Administrador | maria.lopez@empresa.cat       | 123456      |
-- | Administrador | juan.martinez@empresa.cat     | 123456      |
-- | Empleat       | ana.sanchez@empresa.cat       | 123456      |
-- | Empleat       | carlos.fernandez@empresa.cat  | 123456      |
-- | Empleat       | laura.garcia@empresa.cat      | 123456      |
-- | Empleat       | david.torres@empresa.cat      | 123456      |
-- | Empleat       | marta.ramirez@empresa.cat     | 123456      |
-- | Empleat       | sergio.molina@empresa.cat     | 123456      |
--
-- =====================================================
-- 📌 COM UTILITZAR AQUESTS USUARIS:
-- 1. Executa aquest fitxer SQL a la teva base de dades
-- 2. Ves a la pàgina de login: /login
-- 3. Introdueix qualsevol dels correus i la seva contrasenya
-- 4. Ja pots provar totes les funcionalitats segons el rol
-- =====================================================