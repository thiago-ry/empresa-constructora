-- ============================================================================
-- Migración v3 – App Jefe de Obra / Capataz
-- Base: MariaDB 10.4 (la de tu dump). Se puede ejecutar más de una vez sin romper nada.
-- Cómo: phpMyAdmin → tu base "constructora" → pestaña SQL → pegar todo → Continuar.
-- Hacé una copia de seguridad (Exportar) antes, por costumbre.
-- ============================================================================

-- 1) SOLICITUDES DE HERRAMIENTAS ---------------------------------------------
-- Mismas tablas y columnas que ya creaste a mano. Si existen, esto no hace nada.
CREATE TABLE IF NOT EXISTS `solicitud_herramienta` (
  `id_solicitud` int(11) NOT NULL AUTO_INCREMENT,
  `id_obra` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('Pendiente','Aprobada','Rechazada','Entregada','Parcialmente devuelta','Devuelta') NOT NULL DEFAULT 'Pendiente',
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id_solicitud`),
  KEY `idx_solicitud_herramienta_obra` (`id_obra`),
  KEY `idx_solicitud_herramienta_usuario` (`id_usuario`),
  KEY `idx_solicitud_herramienta_estado` (`estado`),
  CONSTRAINT `fk_solicitud_herramienta_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_solicitud_herramienta_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `detalle_solicitud_herramienta` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_solicitud` int(11) NOT NULL,
  `id_herramienta` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `idx_detalle_solicitud` (`id_solicitud`),
  KEY `idx_detalle_herramienta` (`id_herramienta`),
  CONSTRAINT `fk_detalle_solicitud_herramienta` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitud_herramienta` (`id_solicitud`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_detalle_herramienta_catalogo` FOREIGN KEY (`id_herramienta`) REFERENCES `herramienta` (`id_herramienta`) ON UPDATE CASCADE,
  CONSTRAINT `chk_detalle_herramienta_cantidad` CHECK (`cantidad` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- OPCIONAL: motivo o nota cuando se aprueba / rechaza una solicitud (la app la muestra si existe).
ALTER TABLE `solicitud_herramienta`
  ADD COLUMN IF NOT EXISTS `respuesta` varchar(255) DEFAULT NULL;

-- 2) SOLICITUDES DE MATERIALES: quién la pidió y una nota ----------------------
-- Columnas opcionales: el panel web sigue funcionando igual (se agregan en NULL).
ALTER TABLE `solicitud_material`
  ADD COLUMN IF NOT EXISTS `id_usuario` int(11) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `observacion` text DEFAULT NULL;

-- 3) CONSUMOS POR OBRA ---------------------------------------------------------
-- Hasta ahora movimiento_material no sabía a qué obra pertenecía cada consumo.
ALTER TABLE `movimiento_material`
  ADD COLUMN IF NOT EXISTS `id_obra` int(11) DEFAULT NULL;

-- Índice para consultar rápido por obra (se ignora si ya existe)
SET @existe := (SELECT COUNT(*) FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'movimiento_material'
                  AND INDEX_NAME = 'idx_movmat_obra');
SET @sql := IF(@existe = 0, 'ALTER TABLE `movimiento_material` ADD KEY `idx_movmat_obra` (`id_obra`)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
