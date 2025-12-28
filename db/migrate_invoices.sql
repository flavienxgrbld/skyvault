-- Migration pour le module Facturation
USE `skyvault`;

-- Table des clients
CREATE TABLE IF NOT EXISTS `clients` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `address` TEXT,
  `city` VARCHAR(100) DEFAULT NULL,
  `postal_code` VARCHAR(20) DEFAULT NULL,
  `country` VARCHAR(100) DEFAULT 'France',
  `siret` VARCHAR(50) DEFAULT NULL,
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des produits/services
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `type` ENUM('product', 'service') DEFAULT 'service',
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `unit` VARCHAR(50) DEFAULT 'unité',
  `tax_rate` DECIMAL(5,2) DEFAULT 20.00,
  `reference` VARCHAR(50) DEFAULT NULL,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des factures
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
  `client_id` INT UNSIGNED NOT NULL,
  `status` ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
  `issue_date` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `notes` TEXT,
  `payment_terms` TEXT,
  `paid_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE RESTRICT,
  INDEX `idx_invoice_number` (`invoice_number`),
  INDEX `idx_client_id` (`client_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des lignes de facture
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED DEFAULT NULL,
  `description` VARCHAR(255) NOT NULL,
  `quantity` DECIMAL(10,2) NOT NULL DEFAULT 1.00,
  `unit_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tax_rate` DECIMAL(5,2) DEFAULT 20.00,
  `line_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `position` INT DEFAULT 0,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL,
  INDEX `idx_invoice_id` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Données de démonstration
INSERT INTO `clients` (`name`, `email`, `phone`, `address`, `city`, `postal_code`, `siret`) VALUES
('Entreprise ABC', 'contact@abc.fr', '01 23 45 67 89', '123 Rue de la République', 'Paris', '75001', '123 456 789 00012'),
('Société XYZ', 'info@xyz.com', '01 98 76 54 32', '456 Avenue des Champs', 'Lyon', '69001', '987 654 321 00034'),
('Startup Tech', 'hello@startup.io', '06 12 34 56 78', '789 Boulevard Innovation', 'Nantes', '44000', NULL);

INSERT INTO `products` (`name`, `description`, `type`, `price`, `unit`, `tax_rate`, `reference`) VALUES
('Développement Web', 'Création de site web sur mesure', 'service', 800.00, 'jour', 20.00, 'DEV-WEB'),
('Conseil IT', 'Consultation et audit technique', 'service', 1200.00, 'jour', 20.00, 'CONS-IT'),
('Hébergement Premium', 'Serveur dédié avec support 24/7', 'service', 150.00, 'mois', 20.00, 'HOST-PRE'),
('Formation React', 'Formation développement React avancé', 'service', 2000.00, 'session', 20.00, 'FORM-REACT'),
('Licence Logiciel', 'Licence annuelle logiciel propriétaire', 'product', 499.00, 'licence', 20.00, 'LIC-SOFT');

-- Données exemple pour factures
INSERT INTO `invoices` (`invoice_number`, `client_id`, `status`, `issue_date`, `due_date`, `subtotal`, `tax_amount`, `total`) VALUES
('FAC-2024-001', 1, 'paid', '2024-12-01', '2024-12-31', 4000.00, 800.00, 4800.00),
('FAC-2024-002', 2, 'sent', '2024-12-15', '2025-01-15', 2400.00, 480.00, 2880.00),
('FAC-2024-003', 3, 'draft', '2024-12-20', '2025-01-20', 1600.00, 320.00, 1920.00);

INSERT INTO `invoice_items` (`invoice_id`, `product_id`, `description`, `quantity`, `unit_price`, `tax_rate`, `line_total`, `position`) VALUES
(1, 1, 'Développement site e-commerce', 5.00, 800.00, 20.00, 4000.00, 1),
(2, 2, 'Audit infrastructure', 2.00, 1200.00, 20.00, 2400.00, 1),
(3, 4, 'Formation React pour équipe', 0.80, 2000.00, 20.00, 1600.00, 1);
