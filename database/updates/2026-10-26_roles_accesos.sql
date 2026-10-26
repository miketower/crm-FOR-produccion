-- Actualización de roles y permisos para MySQL 5.7+ / MariaDB.
-- En phpMyAdmin, selecciona la base del CRM e importa este archivo.
-- Haz una exportación de respaldo de tu base antes de actualizarla.
-- Esquema sin prefijo, igual que database/schema.sql. No importa ni borra usuarios.
-- Puede repetirse: el historial evita volver a sembrar permisos/asignaciones.
-- No es necesario ejecutar también php spark access:install.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` VARCHAR(255) NOT NULL,
  `class` VARCHAR(255) NOT NULL,
  `group` VARCHAR(255) NOT NULL,
  `namespace` VARCHAR(255) NOT NULL,
  `time` INT NOT NULL,
  `batch` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET @crm_access_new = NOT EXISTS (
  SELECT 1 FROM `migrations`
  WHERE `version` = '2026-10-26-180000' AND `namespace` = 'App'
);

SET @crm_access_ddl = IF(
  EXISTS (SELECT 1 FROM information_schema.COLUMNS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'commercial_channel'),
  'SELECT 1',
  'ALTER TABLE `users` ADD COLUMN `commercial_channel` VARCHAR(30) NULL'
);
PREPARE crm_access_statement FROM @crm_access_ddl;
EXECUTE crm_access_statement;
DEALLOCATE PREPARE crm_access_statement;

CREATE TABLE IF NOT EXISTS `access_roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(100) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `access_permissions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(100) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `access_role_permissions` (
  `role_id` INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  FOREIGN KEY (`role_id`) REFERENCES `access_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `access_permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `access_user_roles` (
  `user_id` INT UNSIGNED NOT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `access_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

START TRANSACTION;

-- Crear el catálogo solo durante la primera instalación; conservar nombres y roles existentes.
INSERT INTO `access_roles` (`slug`, `name`)
SELECT 'admin', 'Administrador' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_roles` WHERE `slug` = 'admin');
INSERT INTO `access_roles` (`slug`, `name`)
SELECT 'manager', 'Gerente' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_roles` WHERE `slug` = 'manager');
INSERT INTO `access_roles` (`slug`, `name`)
SELECT 'sales_rep', 'Vendedor' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_roles` WHERE `slug` = 'sales_rep');
INSERT INTO `access_roles` (`slug`, `name`)
SELECT 'events_rep', 'Eventos' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_roles` WHERE `slug` = 'events_rep');
INSERT INTO `access_roles` (`slug`, `name`)
SELECT 'commercial_rep', 'Comercial' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_roles` WHERE `slug` = 'commercial_rep');
INSERT INTO `access_roles` (`slug`, `name`)
SELECT 'operaciones', 'Operaciones' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_roles` WHERE `slug` = 'operaciones');
INSERT INTO `access_roles` (`slug`, `name`)
SELECT 'captura', 'Captura comercial' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_roles` WHERE `slug` = 'captura');
INSERT INTO `access_roles` (`slug`, `name`)
SELECT 'asociados', 'Asociados' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_roles` WHERE `slug` = 'asociados');
INSERT INTO `access_roles` (`slug`, `name`)
SELECT 'reclutamiento', 'Reclutamiento' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_roles` WHERE `slug` = 'reclutamiento');
INSERT INTO `access_roles` (`slug`, `name`)
SELECT 'perfilamiento', 'Perfilamiento' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_roles` WHERE `slug` = 'perfilamiento');
INSERT INTO `access_roles` (`slug`, `name`)
SELECT 'perfilamiento_amplio', 'Perfilamiento completo' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_roles` WHERE `slug` = 'perfilamiento_amplio');
INSERT INTO `access_roles` (`slug`, `name`)
SELECT 'web_tools', 'Herramientas web' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_roles` WHERE `slug` = 'web_tools');
INSERT INTO `access_roles` (`slug`, `name`)
SELECT 'optimizacion', 'Consulta de Optimización' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_roles` WHERE `slug` = 'optimizacion');

INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'dashboard.ver', 'Inicio: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'dashboard.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'dashboard.modificar', 'Inicio: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'dashboard.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'pipeline.ver', 'Pipeline Ventas: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'pipeline.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'pipeline.modificar', 'Pipeline Ventas: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'pipeline.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'carga.ver', 'Carga Semanal: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'carga.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'carga.modificar', 'Carga Semanal: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'carga.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'eventos.ver', 'Eventos: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'eventos.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'eventos.modificar', 'Eventos: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'eventos.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'prospectos.ver', 'Perfilamiento: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'prospectos.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'prospectos.modificar', 'Perfilamiento: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'prospectos.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'operaciones.ver', 'Bitácora Operativa: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'operaciones.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'operaciones.modificar', 'Bitácora Operativa: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'operaciones.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'forecast.ver', 'Forecast: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'forecast.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'forecast.modificar', 'Forecast: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'forecast.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'vacantes.ver', 'Pipeline Vacantes: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'vacantes.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'vacantes.modificar', 'Pipeline Vacantes: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'vacantes.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'clientes.ver', 'Clientes: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'clientes.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'clientes.modificar', 'Clientes: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'clientes.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'socios.ver', 'Asociados FOR: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'socios.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'socios.modificar', 'Asociados FOR: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'socios.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'cierre_socios.ver', 'Cierre de Mes (Asociados): consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'cierre_socios.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'cierre_socios.modificar', 'Cierre de Mes (Asociados): modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'cierre_socios.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'ventas.ver', 'Ventas e Ingresos: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'ventas.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'ventas.modificar', 'Ventas e Ingresos: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'ventas.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'importar.ver', 'Cierre Mensual: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'importar.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'importar.modificar', 'Cierre Mensual: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'importar.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'directorio.ver', 'Directorio Formularios: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'directorio.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'directorio.modificar', 'Directorio Formularios: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'directorio.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'salarios.ver', 'Guía Global de Salarios 2027: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'salarios.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'salarios.modificar', 'Guía Global de Salarios 2027: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'salarios.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'alineacion.ver', 'Alineación Estratégica: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'alineacion.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'alineacion.modificar', 'Alineación Estratégica: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'alineacion.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'calculadora.ver', 'Calculadora Nómina: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'calculadora.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'calculadora.modificar', 'Calculadora Nómina: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'calculadora.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'cotizador.ver', 'Cotizador: consultar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'cotizador.ver');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'cotizador.modificar', 'Cotizador: modificar' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'cotizador.modificar');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'prospectos.ver_todos', 'Perfilamiento: consultar todos en el listado' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'prospectos.ver_todos');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'prospectos.ver_optimizacion', 'Perfilamiento: consultar Optimización en el listado' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'prospectos.ver_optimizacion');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'carga.todos_canales', 'Carga semanal: consultar y capturar en todos los canales' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'carga.todos_canales');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'operaciones.ver_todos', 'Operaciones: consultar todos los tickets en el listado' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'operaciones.ver_todos');
INSERT INTO `access_permissions` (`slug`, `name`)
SELECT 'operaciones.firmar', 'Operaciones: firmar como supervisor' WHERE @crm_access_new = 1
AND NOT EXISTS (SELECT 1 FROM `access_permissions` WHERE `slug` = 'operaciones.firmar');

-- Permisos iniciales equivalentes a la migración PHP.
INSERT INTO `access_role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `access_roles` r CROSS JOIN `access_permissions` p
LEFT JOIN `access_role_permissions` existing ON existing.role_id = r.id AND existing.permission_id = p.id
WHERE @crm_access_new = 1 AND r.slug = 'admin'
AND p.slug IN ('dashboard.ver', 'dashboard.modificar', 'pipeline.ver', 'pipeline.modificar', 'carga.ver', 'carga.modificar', 'eventos.ver', 'eventos.modificar', 'prospectos.ver', 'prospectos.modificar', 'operaciones.ver', 'operaciones.modificar', 'forecast.ver', 'forecast.modificar', 'vacantes.ver', 'vacantes.modificar', 'clientes.ver', 'clientes.modificar', 'socios.ver', 'socios.modificar', 'cierre_socios.ver', 'cierre_socios.modificar', 'ventas.ver', 'ventas.modificar', 'importar.ver', 'importar.modificar', 'directorio.ver', 'directorio.modificar', 'salarios.ver', 'salarios.modificar', 'alineacion.ver', 'alineacion.modificar', 'calculadora.ver', 'calculadora.modificar', 'cotizador.ver', 'cotizador.modificar', 'prospectos.ver_todos', 'carga.todos_canales', 'operaciones.ver_todos', 'operaciones.firmar') AND existing.role_id IS NULL;

INSERT INTO `access_role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `access_roles` r CROSS JOIN `access_permissions` p
LEFT JOIN `access_role_permissions` existing ON existing.role_id = r.id AND existing.permission_id = p.id
WHERE @crm_access_new = 1 AND r.slug = 'manager'
AND p.slug IN ('dashboard.ver', 'dashboard.modificar', 'pipeline.ver', 'pipeline.modificar', 'carga.ver', 'carga.modificar', 'eventos.ver', 'eventos.modificar', 'prospectos.ver', 'prospectos.modificar', 'operaciones.ver', 'operaciones.modificar', 'forecast.ver', 'forecast.modificar', 'clientes.ver', 'clientes.modificar', 'socios.ver', 'socios.modificar', 'cierre_socios.ver', 'cierre_socios.modificar', 'ventas.ver', 'ventas.modificar', 'importar.ver', 'importar.modificar', 'alineacion.ver', 'alineacion.modificar', 'calculadora.ver', 'calculadora.modificar', 'cotizador.ver', 'cotizador.modificar', 'carga.todos_canales', 'operaciones.ver_todos', 'operaciones.firmar') AND existing.role_id IS NULL;

INSERT INTO `access_role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `access_roles` r CROSS JOIN `access_permissions` p
LEFT JOIN `access_role_permissions` existing ON existing.role_id = r.id AND existing.permission_id = p.id
WHERE @crm_access_new = 1 AND r.slug = 'sales_rep'
AND p.slug IN ('dashboard.ver', 'dashboard.modificar', 'pipeline.ver', 'pipeline.modificar', 'carga.ver', 'carga.modificar', 'eventos.ver', 'eventos.modificar', 'prospectos.ver', 'prospectos.modificar', 'operaciones.ver', 'operaciones.modificar', 'forecast.ver', 'forecast.modificar', 'clientes.ver', 'clientes.modificar', 'socios.ver', 'socios.modificar', 'cierre_socios.ver', 'cierre_socios.modificar', 'ventas.ver', 'ventas.modificar', 'importar.ver', 'importar.modificar', 'alineacion.ver', 'alineacion.modificar', 'calculadora.ver', 'calculadora.modificar', 'cotizador.ver', 'cotizador.modificar', 'carga.todos_canales', 'operaciones.ver_todos') AND existing.role_id IS NULL;

INSERT INTO `access_role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `access_roles` r CROSS JOIN `access_permissions` p
LEFT JOIN `access_role_permissions` existing ON existing.role_id = r.id AND existing.permission_id = p.id
WHERE @crm_access_new = 1 AND r.slug = 'events_rep'
AND p.slug IN ('eventos.ver', 'eventos.modificar', 'prospectos.ver', 'prospectos.modificar', 'socios.ver', 'socios.modificar', 'cierre_socios.ver', 'cierre_socios.modificar') AND existing.role_id IS NULL;

INSERT INTO `access_role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `access_roles` r CROSS JOIN `access_permissions` p
LEFT JOIN `access_role_permissions` existing ON existing.role_id = r.id AND existing.permission_id = p.id
WHERE @crm_access_new = 1 AND r.slug = 'commercial_rep'
AND p.slug IN ('pipeline.ver', 'pipeline.modificar', 'prospectos.ver', 'prospectos.modificar', 'calculadora.ver', 'calculadora.modificar', 'cotizador.ver', 'cotizador.modificar', 'prospectos.ver_optimizacion') AND existing.role_id IS NULL;

INSERT INTO `access_role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `access_roles` r CROSS JOIN `access_permissions` p
LEFT JOIN `access_role_permissions` existing ON existing.role_id = r.id AND existing.permission_id = p.id
WHERE @crm_access_new = 1 AND r.slug = 'operaciones'
AND p.slug IN ('operaciones.ver', 'operaciones.modificar') AND existing.role_id IS NULL;

INSERT INTO `access_role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `access_roles` r CROSS JOIN `access_permissions` p
LEFT JOIN `access_role_permissions` existing ON existing.role_id = r.id AND existing.permission_id = p.id
WHERE @crm_access_new = 1 AND r.slug = 'captura'
AND p.slug IN ('carga.ver', 'carga.modificar') AND existing.role_id IS NULL;

INSERT INTO `access_role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `access_roles` r CROSS JOIN `access_permissions` p
LEFT JOIN `access_role_permissions` existing ON existing.role_id = r.id AND existing.permission_id = p.id
WHERE @crm_access_new = 1 AND r.slug = 'asociados'
AND p.slug IN ('prospectos.ver', 'prospectos.modificar', 'socios.ver', 'socios.modificar', 'cierre_socios.ver', 'cierre_socios.modificar') AND existing.role_id IS NULL;

INSERT INTO `access_role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `access_roles` r CROSS JOIN `access_permissions` p
LEFT JOIN `access_role_permissions` existing ON existing.role_id = r.id AND existing.permission_id = p.id
WHERE @crm_access_new = 1 AND r.slug = 'reclutamiento'
AND p.slug IN ('vacantes.ver', 'vacantes.modificar') AND existing.role_id IS NULL;

INSERT INTO `access_role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `access_roles` r CROSS JOIN `access_permissions` p
LEFT JOIN `access_role_permissions` existing ON existing.role_id = r.id AND existing.permission_id = p.id
WHERE @crm_access_new = 1 AND r.slug = 'perfilamiento'
AND p.slug IN ('prospectos.ver', 'prospectos.modificar') AND existing.role_id IS NULL;

INSERT INTO `access_role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `access_roles` r CROSS JOIN `access_permissions` p
LEFT JOIN `access_role_permissions` existing ON existing.role_id = r.id AND existing.permission_id = p.id
WHERE @crm_access_new = 1 AND r.slug = 'perfilamiento_amplio'
AND p.slug IN ('prospectos.ver', 'prospectos.modificar', 'prospectos.ver_todos') AND existing.role_id IS NULL;

INSERT INTO `access_role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `access_roles` r CROSS JOIN `access_permissions` p
LEFT JOIN `access_role_permissions` existing ON existing.role_id = r.id AND existing.permission_id = p.id
WHERE @crm_access_new = 1 AND r.slug = 'web_tools'
AND p.slug IN ('directorio.ver', 'directorio.modificar', 'salarios.ver', 'salarios.modificar') AND existing.role_id IS NULL;

INSERT INTO `access_role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `access_roles` r CROSS JOIN `access_permissions` p
LEFT JOIN `access_role_permissions` existing ON existing.role_id = r.id AND existing.permission_id = p.id
WHERE @crm_access_new = 1 AND r.slug = 'optimizacion'
AND p.slug IN ('prospectos.ver', 'prospectos.modificar', 'prospectos.ver_optimizacion') AND existing.role_id IS NULL;

-- Conversión inicial de las combinaciones históricas. Después se administran por roles.
INSERT INTO `access_user_roles` (`user_id`, `role_id`)
SELECT u.id, r.id FROM `users` u
JOIN `access_roles` r ON r.slug = CASE
  WHEN u.role = 'admin' THEN 'admin'
  WHEN u.role = 'operaciones' THEN 'operaciones'
  WHEN LOWER(u.email) = 'lberlangazambrano@gmail.com' THEN 'perfilamiento'
  WHEN LOWER(u.email) = 'carlos.sarmiento@forhumancapital.mx' THEN 'reclutamiento'
  WHEN LOWER(u.email) IN ('danya.cruz@forhumancapital.mx', 'samuel.nunez@theblancgroup.com', 'alicia.gutierrez@forhumancapital.mx') THEN 'captura'
  WHEN u.role = 'events_rep' THEN 'events_rep'
  WHEN LOWER(u.email) IN ('aurora.esquivel@forhumancapital.mx', 'yuridia.juarez@forhumancapital.mx') THEN 'asociados'
  ELSE u.role END
LEFT JOIN `access_user_roles` existing ON existing.user_id = u.id AND existing.role_id = r.id
WHERE @crm_access_new = 1 AND existing.user_id IS NULL;

INSERT INTO `access_user_roles` (`user_id`, `role_id`)
SELECT u.id, r.id FROM `users` u
CROSS JOIN `access_roles` r
LEFT JOIN `access_user_roles` existing ON existing.user_id = u.id AND existing.role_id = r.id
WHERE @crm_access_new = 1 AND existing.user_id IS NULL AND (
  (LOWER(u.email) = 'danya.cruz@forhumancapital.mx' AND u.role NOT IN ('admin', 'operaciones') AND r.slug IN ('reclutamiento', 'perfilamiento_amplio'))
  OR (LOWER(u.email) IN ('admin@forhuman.com', 'admin@victoria.com') AND u.role <> 'admin' AND r.slug = 'web_tools')
  OR (LOWER(u.email) = 'david.zapata@forhumancapital.mx' AND u.role <> 'admin' AND r.slug = 'optimizacion')
);

UPDATE `users` SET `commercial_channel` = CASE LOWER(email)
  WHEN 'danya.cruz@forhumancapital.mx' THEN 'Soluciones'
  WHEN 'samuel.nunez@theblancgroup.com' THEN 'Optimización'
  WHEN 'alicia.gutierrez@forhumancapital.mx' THEN 'Otros'
  ELSE commercial_channel END
WHERE @crm_access_new = 1 AND LOWER(email) IN (
  'danya.cruz@forhumancapital.mx', 'samuel.nunez@theblancgroup.com', 'alicia.gutierrez@forhumancapital.mx'
);

-- Marcar la misma migración que usa access:install para evitar una segunda instalación.
-- El literal hexadecimal conserva las barras invertidas del identificador de CodeIgniter.
INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`)
SELECT '2026-10-26-180000', 0x4170705c44617461626173655c4d6967726174696f6e735c437265617465416363657373436f6e74726f6c, 'default', 'App', UNIX_TIMESTAMP(), COALESCE(MAX(batch), 0) + 1
FROM `migrations` WHERE @crm_access_new = 1
HAVING @crm_access_new = 1;

COMMIT;
SELECT IF(@crm_access_new = 1, 'Roles y permisos instalados correctamente.', 'Los accesos ya estaban instalados; se conservaron sus permisos.') AS resultado;
