-- Impresoras y lecturas de contadores (copia, impresion y escaneado, color y B/N)
-- Se crean solas al abrir la pagina; este script es solo para crearlas manualmente.
CREATE TABLE IF NOT EXISTS `impresoras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `ip` varchar(64) NOT NULL,
  `ubicacion` varchar(150) DEFAULT NULL,
  `creado_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `impresora_lecturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `impresora_id` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp(),
  `copia_color` int(11) DEFAULT NULL,
  `copia_bn` int(11) DEFAULT NULL,
  `copia_color_pers` int(11) DEFAULT NULL,
  `copia_dos_colores` int(11) DEFAULT NULL,
  `impresion_color` int(11) DEFAULT NULL,
  `impresion_bn` int(11) DEFAULT NULL,
  `impresion_color_pers` int(11) DEFAULT NULL,
  `impresion_dos_colores` int(11) DEFAULT NULL,
  `escaneo_color` int(11) DEFAULT NULL,
  `escaneo_bn` int(11) DEFAULT NULL,
  `contador_total` int(11) DEFAULT NULL,
  `total_color` int(11) DEFAULT NULL,
  `total_bn` int(11) DEFAULT NULL,
  `total_color_pers` int(11) DEFAULT NULL,
  `total_dos_colores` int(11) DEFAULT NULL,
  `crudo` mediumtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lec_impresora` (`impresora_id`,`fecha_hora`),
  CONSTRAINT `fk_lec_impresora` FOREIGN KEY (`impresora_id`) REFERENCES `impresoras` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
