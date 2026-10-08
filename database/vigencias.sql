-- Tabla de vigencias: licencias, garantías y suscripciones (hardware o software)
-- Se crea sola al abrir la página; este script es solo para crearla manualmente.
CREATE TABLE IF NOT EXISTS `vigencias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `equipo_id` int(11) DEFAULT NULL,
  `tipo` enum('Software','Hardware') NOT NULL DEFAULT 'Software',
  `nombre` varchar(200) NOT NULL,
  `proveedor` varchar(150) DEFAULT NULL,
  `referencia` varchar(250) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_vencimiento` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `creado_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_vig_equipo` (`equipo_id`),
  KEY `idx_vig_vencimiento` (`fecha_vencimiento`),
  CONSTRAINT `fk_vig_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
