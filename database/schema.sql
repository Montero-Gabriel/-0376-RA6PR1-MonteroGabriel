-- =====================================================
--  BASE DE DADES - APLICACIÓ TRACKING D'Hores
--  Optimitzada per 400 usuaris simultanis
-- =====================================================

CREATE DATABASE IF NOT EXISTS tracking_hores CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tracking_hores;

-- -----------------------------------------------------
-- Taula USUARIS - Empleats i Administradors
-- -----------------------------------------------------
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    dni VARCHAR(15) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(80) NOT NULL,
    cognoms VARCHAR(120) NOT NULL,
    departament VARCHAR(60) NULL,
    horari_entrada TIME DEFAULT '08:00:00',
    horari_sortida TIME DEFAULT '16:00:00',
    hores_diaries_requerides DECIMAL(3,1) DEFAULT 8.0,
    rol ENUM('empleat', 'administrador', 'superadmin') DEFAULT 'empleat',
    actiu BOOLEAN DEFAULT TRUE,
    data_creacio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_connexio TIMESTAMP NULL,
    
    INDEX idx_email (email),
    INDEX idx_actiu (actiu),
    INDEX idx_rol (rol)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Taula PROJECTES
-- -----------------------------------------------------
CREATE TABLE projectes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codi VARCHAR(20) UNIQUE NOT NULL,
    nom VARCHAR(150) NOT NULL,
    descripcio TEXT NULL,
    client VARCHAR(100) NULL,
    hores_pressupostades DECIMAL(7,1) NOT NULL,
    data_inici DATE NULL,
    data_final DATE NULL,
    estat ENUM('obert', 'en_curs', 'tancat') DEFAULT 'obert',
    actiu BOOLEAN DEFAULT TRUE,
    data_creacio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_estat (estat),
    INDEX idx_actiu (actiu)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Taula SESSIONS DE TREBALL (Marcatge Entrada/Sortida)
-- -----------------------------------------------------
CREATE TABLE sessions_treball (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    hora_entrada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    hora_sortida TIMESTAMP NULL,
    ubicacio_entrada VARCHAR(255) NULL,
    ubicacio_sortida VARCHAR(255) NULL,
    hores_totals DECIMAL(4,2) GENERATED ALWAYS AS (
        TIMESTAMPDIFF(SECOND, hora_entrada, hora_sortida)/3600
    ) STORED NULL,
    estat ENUM('activa', 'finalitzada') DEFAULT 'activa',
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_user_id (user_id),
    INDEX idx_estat (estat),
    INDEX idx_hora_entrada (hora_entrada),
    UNIQUE KEY unique_sessio_activa (user_id, estat)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Taula REGISTRES DE TEMPS PER PROJECTE
-- -----------------------------------------------------
CREATE TABLE registres_temps (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    projecte_id INT NOT NULL,
    sessio_id INT NOT NULL,
    descripcio VARCHAR(255) NULL,
    hora_inici TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    hora_fi TIMESTAMP NULL,
    hores DECIMAL(4,2) GENERATED ALWAYS AS (
        TIMESTAMPDIFF(SECOND, hora_inici, hora_fi)/3600
    ) STORED NULL,
    estat ENUM('en_curs', 'finalitzat') DEFAULT 'en_curs',
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (projecte_id) REFERENCES projectes(id) ON DELETE CASCADE,
    FOREIGN KEY (sessio_id) REFERENCES sessions_treball(id) ON DELETE CASCADE,
    
    INDEX idx_user_projecte (user_id, projecte_id),
    INDEX idx_sessio_id (sessio_id)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Taula ALERTES I INCOMPLIMENTS (Llista Vermella)
-- -----------------------------------------------------
CREATE TABLE incompliments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    sessio_id INT NULL,
    tipus ENUM('arribada_tardana', 'sortida_anticipada', 'hores_insuficients', 'sense_marcatge') NOT NULL,
    data DATE NOT NULL,
    minuts_diferencia INT NOT NULL,
    missatge VARCHAR(255) NOT NULL,
    llegit BOOLEAN DEFAULT FALSE,
    data_creacio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (sessio_id) REFERENCES sessions_treball(id) ON DELETE SET NULL,
    
    INDEX idx_data (data),
    INDEX idx_llegit (llegit),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Taula LOGS D'ACTIVITAT
-- -----------------------------------------------------
CREATE TABLE log_activitat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    accio VARCHAR(100) NOT NULL,
    detalls TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_data (data)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- VISTES PER RÀPIDA CONSULTA
-- -----------------------------------------------------

-- Vista personal activa actualment
CREATE VIEW v_empleats_actius AS
SELECT 
    u.id, u.nom, u.cognoms, u.departament, u.rol,
    s.id AS sessio_id, s.hora_entrada,
    TIMESTAMPDIFF(HOUR, s.hora_entrada, NOW()) AS hores_transcorregudes,
    (SELECT nom FROM projectes p 
     JOIN registres_temps rt ON p.id = rt.projecte_id 
     WHERE rt.user_id = u.id AND rt.estat = 'en_curs' LIMIT 1) AS projecte_actual
FROM users u
LEFT JOIN sessions_treball s ON u.id = s.user_id AND s.estat = 'activa'
WHERE u.actiu = TRUE
ORDER BY u.cognoms;

-- Vista hores per projecte
CREATE VIEW v_hores_per_projecte AS
SELECT 
    p.id, p.codi, p.nom, p.client, p.hores_pressupostades,
    SUM(rt.hores) AS hores_reals,
    (SUM(rt.hores) / p.hores_pressupostades * 100) AS percentatge_utilitzat
FROM projectes p
LEFT JOIN registres_temps rt ON p.id = rt.projecte_id AND rt.estat = 'finalitzat'
WHERE p.actiu = TRUE
GROUP BY p.id
ORDER BY percentatge_utilitzat DESC;

-- -----------------------------------------------------
-- USUARI ADMINISTRADOR PER DEFECTE
-- -----------------------------------------------------
INSERT INTO users (dni, email, password, nom, cognoms, rol) 
VALUES ('00000000A', 'admin@empresa.cat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', 'superadmin');
-- Contrasenya per defecte: password