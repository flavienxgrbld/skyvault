-- Créer une table dédiée pour les administrateurs
USE `skyvault`;

-- Créer la table admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `last_login` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Migrer l'admin existant de users vers admins
INSERT INTO `admins` (`name`, `email`, `status`)
SELECT `name`, `email`, 'active'
FROM `users` 
WHERE `email` = 'admin@localhost'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Supprimer l'admin de la table users
DELETE FROM `users` WHERE `email` = 'admin@localhost';

-- Modifier la table users pour enlever le rôle admin
ALTER TABLE `users` 
MODIFY COLUMN `role` ENUM('client', 'user') DEFAULT 'client';
