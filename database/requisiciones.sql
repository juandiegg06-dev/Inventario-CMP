-- Requisiciones de compra (formato MA-PC-FT-001) y sus artículos
-- Se crean solas al abrir la sección Requisiciones; este script es solo para crearlas manualmente.
CREATE TABLE IF NOT EXISTS `requisiciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consecutivo` int(11) NOT NULL,
  `codigo` varchar(80) NOT NULL,
  `fecha` date NOT NULL,
  `prioridad` varchar(20) NOT NULL,
  `empresa_cod` varchar(20) NOT NULL,
  `empresa_nombre` varchar(200) NOT NULL,
  `area_cod` varchar(20) NOT NULL,
  `area_nombre` varchar(150) NOT NULL,
  `solicitante_cod` int(11) NOT NULL,
  `solicitante_nombre` varchar(200) NOT NULL,
  `autorizado_por` varchar(200) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `sol_cedula` varchar(30) DEFAULT NULL,
  `sol_cargo` varchar(150) DEFAULT NULL,
  `creado_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_req_consecutivo` (`consecutivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `requisicion_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `requisicion_id` int(11) NOT NULL,
  `item` int(11) NOT NULL,
  `cantidad` decimal(12,2) NOT NULL,
  `unidad` varchar(30) DEFAULT NULL,
  `articulo` text NOT NULL,
  `proveedor` varchar(200) DEFAULT NULL,
  `centro_trabajo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reqitem_req` (`requisicion_id`),
  CONSTRAINT `fk_reqitem_req` FOREIGN KEY (`requisicion_id`) REFERENCES `requisiciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
