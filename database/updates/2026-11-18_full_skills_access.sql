-- Asigna por única vez el acceso a las cuentas indicadas para la matriz.
-- Selecciona primero la base del CRM. No altera usuarios ni roles existentes.
-- Una vez asignado, el acceso requiere tanto el ID como el correo autorizado.
CREATE TABLE IF NOT EXISTS `full_skills_viewers` (
  `slot` ENUM('luciana', 'jorge', 'victoria') NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`slot`),
  UNIQUE KEY `full_skills_viewers_user` (`user_id`),
  CONSTRAINT `fk_full_skills_viewer_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `full_skills_viewers` (`slot`, `user_id`)
SELECT 'luciana', `id` FROM `users`
WHERE LOWER(`email`) = 'luciana.osuna@forhumancapital.mx' AND `is_active` = 1 AND `deleted_at` IS NULL;

INSERT IGNORE INTO `full_skills_viewers` (`slot`, `user_id`)
SELECT 'jorge', `id` FROM `users`
WHERE LOWER(`email`) = 'jorgemarioblanco@forhumancapital.mx' AND `is_active` = 1 AND `deleted_at` IS NULL;

INSERT IGNORE INTO `full_skills_viewers` (`slot`, `user_id`)
SELECT 'victoria', `id` FROM `users`
WHERE LOWER(`email`) = 'victoria.torres@forhumancapital.mx' AND `is_active` = 1 AND `deleted_at` IS NULL;

-- Deben aparecer las tres cuentas. Si falta alguna, revisa el correo o estado
-- de esa cuenta antes de volver a ejecutar este archivo.
SELECT f.`slot`, u.`id`, u.`full_name`, u.`email`
FROM `full_skills_viewers` f JOIN `users` u ON u.`id` = f.`user_id`
ORDER BY f.`slot`;
