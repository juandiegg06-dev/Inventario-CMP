-- Historial de cambios por equipo: reasignaciones, ediciones, software, periféricos y papelera.
-- Se crea sola al usar la página; este script es solo para crearla manualmente.
CREATE TABLE IF NOT EXISTS `equipo_historial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `equipo_id` int(11) NOT NULL,
  `accion` varchar(30) NOT NULL,
  `detalle` text DEFAULT NULL,
  `creado_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_hist_equipo` (`equipo_id`, `creado_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
