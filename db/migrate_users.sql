-- Migration: Améliorer la table users avec plus de champs
USE `skyvault`;

-- Modifier la table users existante
ALTER TABLE `users` 
ADD COLUMN `role` ENUM('admin', 'client', 'user') DEFAULT 'client' AFTER `email`,
ADD COLUMN `status` ENUM('active', 'inactive', 'pending') DEFAULT 'active' AFTER `role`,
ADD COLUMN `phone` VARCHAR(20) DEFAULT NULL AFTER `status`,
ADD COLUMN `company` VARCHAR(150) DEFAULT NULL AFTER `phone`,
ADD COLUMN `last_login` TIMESTAMP NULL DEFAULT NULL AFTER `company`,
ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Mettre à jour l'utilisateur admin existant
UPDATE `users` SET `role` = 'admin', `status` = 'active' WHERE `email` = 'admin@localhost';

-- Index pour améliorer les performances
CREATE INDEX `idx_role` ON `users`(`role`);
CREATE INDEX `idx_status` ON `users`(`status`);
