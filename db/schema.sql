-- Schéma MySQL pour SkyVault
-- Crée la base et des tables de démonstration
CREATE DATABASE IF NOT EXISTS `skyvault` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `skyvault`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10,2) DEFAULT 0.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Données d'exemple
INSERT INTO `users` (`name`, `email`) VALUES
  ('Admin', 'admin@localhost');

INSERT INTO `products` (`name`, `description`, `price`) VALUES
  ('Exemple Produit', 'Produit de démonstration', 9.99);
