
CREATE DATABASE IF NOT EXISTS turnos
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE turnos;

-- 1. Usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'cliente') NOT NULL DEFAULT 'cliente',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Sedes con contadores independientes
CREATE TABLE IF NOT EXISTS sedes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL UNIQUE,
    direccion VARCHAR(255) DEFAULT NULL,
    turno_actual_g INT UNSIGNED NOT NULL DEFAULT 0,
    turno_actual_r INT UNSIGNED NOT NULL DEFAULT 0,
    turno_actual_c INT UNSIGNED NOT NULL DEFAULT 0,
    activa TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Turnos registrados por los clientes
CREATE TABLE IF NOT EXISTS turnos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    sede_id INT UNSIGNED NOT NULL,
    tipo ENUM('G', 'R', 'C') NOT NULL,
    numero INT UNSIGNED NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY turno_unico (sede_id, tipo, numero),
    INDEX idx_usuario (user_id),
    INDEX idx_sede_tipo (sede_id, tipo),

    CONSTRAINT fk_turnos_usuario
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_turnos_sede
        FOREIGN KEY (sede_id) REFERENCES sedes(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Sede inicial para comenzar las pruebas
INSERT IGNORE INTO sedes (nombre, direccion)
VALUES ('Sede principal', 'Dirección por configurar');
