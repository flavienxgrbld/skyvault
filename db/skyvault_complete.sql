-- ============================================
-- SkyVault - Base de données complète
-- ============================================
-- Date de création: 28 décembre 2025
-- Description: Schéma complet pour la plateforme SkyVault
-- ============================================

-- Création de la base de données
DROP DATABASE IF EXISTS `skyvault`;
CREATE DATABASE `skyvault` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `skyvault`;

-- ============================================
-- TABLE: admins
-- Description: Gestion des administrateurs
-- ============================================
CREATE TABLE `admins` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Table des administrateurs de la plateforme';

-- ============================================
-- TABLE: users
-- Description: Gestion des utilisateurs clients
-- ============================================
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) DEFAULT NULL,
  `role` ENUM('client', 'user') DEFAULT 'client',
  `status` ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
  `phone` VARCHAR(20) DEFAULT NULL,
  `company` VARCHAR(150) DEFAULT NULL,
  `last_login` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_role` (`role`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Table des utilisateurs clients';

-- ============================================
-- TABLE: modules
-- Description: Catalogue des modules disponibles
-- ============================================
CREATE TABLE `modules` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10,2) DEFAULT 0.00,
  `path` VARCHAR(255) NOT NULL,
  `category` VARCHAR(50) DEFAULT NULL,
  `active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_slug` (`slug`),
  INDEX `idx_category` (`category`),
  INDEX `idx_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Catalogue des modules SkyVault';

-- ============================================
-- TABLE: user_modules
-- Description: Modules souscrits par les utilisateurs
-- ============================================
CREATE TABLE `user_modules` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `module_id` INT UNSIGNED NOT NULL,
  `subscribed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `status` ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_module` (`user_id`, `module_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`module_id`) REFERENCES `modules`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_module_id` (`module_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Souscriptions des utilisateurs aux modules';

-- ============================================
-- TABLE: orders
-- Description: Commandes et paiements
-- ============================================
CREATE TABLE `orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('pending', 'completed', 'cancelled', 'failed') DEFAULT 'pending',
  `payment_method` VARCHAR(50) DEFAULT NULL,
  `transaction_id` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Commandes des utilisateurs';

-- ============================================
-- TABLE: order_items
-- Description: Détails des commandes
-- ============================================
CREATE TABLE `order_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `module_id` INT UNSIGNED NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `quantity` INT DEFAULT 1,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`module_id`) REFERENCES `modules`(`id`) ON DELETE CASCADE,
  INDEX `idx_order_id` (`order_id`),
  INDEX `idx_module_id` (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Détails des articles commandés';

-- ============================================
-- TABLE: products (table de démonstration)
-- Description: Table générique pour produits
-- ============================================
CREATE TABLE `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10,2) DEFAULT 0.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Table de démonstration pour produits';

-- ============================================
-- TABLE: sessions
-- Description: Gestion des sessions utilisateurs
-- ============================================
CREATE TABLE `sessions` (
  `id` VARCHAR(128) NOT NULL,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `admin_id` INT UNSIGNED DEFAULT NULL,
  `data` TEXT,
  `last_activity` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_admin_id` (`admin_id`),
  INDEX `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Sessions utilisateurs et admin';

-- ============================================
-- TABLE: activity_logs
-- Description: Journal d'activité
-- ============================================
CREATE TABLE `activity_logs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `admin_id` INT UNSIGNED DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) DEFAULT NULL,
  `entity_id` INT UNSIGNED DEFAULT NULL,
  `details` TEXT,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_admin_id` (`admin_id`),
  INDEX `idx_action` (`action`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Journal des activités';

-- ============================================
-- DONNÉES INITIALES
-- ============================================

-- Insertion administrateur par défaut
INSERT INTO `admins` (`name`, `email`, `password_hash`, `status`) VALUES
('Super Admin', 'admin@skyvault.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active'),
('Admin', 'admin@localhost', NULL, 'active');

-- Insertion utilisateurs de test
INSERT INTO `users` (`name`, `email`, `status`, `company`) VALUES
('Jean Dupont', 'jean.dupont@example.com', 'active', 'Entreprise ABC'),
('Marie Martin', 'marie.martin@example.com', 'active', 'Tech Solutions'),
('Pierre Durand', 'pierre.durand@example.com', 'pending', NULL);

-- Insertion modules - Catégorie Finance
INSERT INTO `modules` (`slug`, `title`, `description`, `price`, `path`, `category`) VALUES
('comptabilite', 'Comptabilité', 'Gestion des comptes et journaux', 20.00, 'modules/finance/comptabilite/index.html', 'finance'),
('facturation', 'Facturation', 'Devis, factures et relances', 15.00, 'modules/finance/facturation/index.html', 'finance'),
('notes-de-frais', 'Notes de frais', 'Saisie et remboursement', 8.00, 'modules/finance/notes-de-frais/index.html', 'finance'),
('feuilles-de-calcul', 'Feuilles de calcul (BI)', 'Tableaux de bord et BI', 25.00, 'modules/finance/feuilles-de-calcul/index.html', 'finance'),
('documents', 'Documents', 'Stockage et archivage', 5.00, 'modules/finance/documents/index.html', 'finance'),
('signature', 'Signature', 'Signature électronique', 7.00, 'modules/finance/signature/index.html', 'finance');

-- Insertion modules - Catégorie Ventes
INSERT INTO `modules` (`slug`, `title`, `description`, `price`, `path`, `category`) VALUES
('crm', 'CRM', 'Gestion de la relation client', 25.00, 'modules/ventes/crm/index.html', 'ventes'),
('ventes', 'Ventes', 'Gestion commerciale', 22.00, 'modules/ventes/ventes/index.html', 'ventes'),
('pdv-boutique', 'PdV Boutique', 'Point de vente — boutique', 19.00, 'modules/ventes/pdv-boutique/index.html', 'ventes'),
('pdv-restaurant', 'PdV Restaurant', 'Point de vente — restaurant', 19.00, 'modules/ventes/pdv-restaurant/index.html', 'ventes'),
('abonnements', 'Abonnements', 'Gestion des abonnements', 12.00, 'modules/ventes/abonnements/index.html', 'ventes'),
('location', 'Location', 'Gestion de locations', 15.00, 'modules/ventes/location/index.html', 'ventes');

-- Insertion modules - Catégorie Communication
INSERT INTO `modules` (`slug`, `title`, `description`, `price`, `path`, `category`) VALUES
('blog', 'Blog', 'Publication d\'articles', 8.00, 'modules/communication/blog/index.html', 'communication'),
('forum', 'Forum', 'Discussions communautaires', 6.00, 'modules/communication/forum/index.html', 'communication'),
('live-chat', 'Live Chat', 'Chat en direct', 15.00, 'modules/communication/live-chat/index.html', 'communication'),
('e-learning', 'eLearning', 'Formations en ligne', 30.00, 'modules/communication/e-learning/index.html', 'communication');

-- Insertion modules - Catégorie Chaîne d'approvisionnement
INSERT INTO `modules` (`slug`, `title`, `description`, `price`, `path`, `category`) VALUES
('inventaire', 'Inventaire', 'Stocks et mouvements', 12.00, 'modules/chaine-approvisionnement/inventaire/index.html', 'chaine-approvisionnement'),
('fabrication', 'Fabrication', 'Gestion de production', 30.00, 'modules/chaine-approvisionnement/fabrication/index.html', 'chaine-approvisionnement'),
('achats', 'Achats', 'Demandes et fournisseurs', 18.00, 'modules/chaine-approvisionnement/achats/index.html', 'chaine-approvisionnement'),
('maintenance', 'Maintenance', 'GMAO', 9.00, 'modules/chaine-approvisionnement/maintenance/index.html', 'chaine-approvisionnement'),
('qualite', 'Qualité', 'Contrôles & audits', 8.00, 'modules/chaine-approvisionnement/qualite/index.html', 'chaine-approvisionnement');

-- Insertion modules - Catégorie RH
INSERT INTO `modules` (`slug`, `title`, `description`, `price`, `path`, `category`) VALUES
('employes', 'Employés', 'Dossier salarié', 18.00, 'modules/rh/employes/index.html', 'rh'),
('recrutement', 'Recrutement', 'Offres & candidatures', 15.00, 'modules/rh/recrutement/index.html', 'rh'),
('conges', 'Congés', 'Gestion des absences', 7.00, 'modules/rh/conges/index.html', 'rh'),
('evaluations', 'Évaluations', 'Performance', 12.00, 'modules/rh/evaluations/index.html', 'rh'),
('recommandations', 'Recommandations', 'Références & matching', 8.00, 'modules/rh/recommandations/index.html', 'rh');

-- Insertion modules - Catégorie Services
INSERT INTO `modules` (`slug`, `title`, `description`, `price`, `path`, `category`) VALUES
('projet', 'Projet', 'Gestion de projet', 14.00, 'modules/services/projet/index.html', 'services'),
('feuilles-de-temps', 'Feuilles de temps', 'Temps & activités', 9.00, 'modules/services/feuilles-de-temps/index.html', 'services'),
('assistance', 'Assistance', 'Ticketing', 8.00, 'modules/services/assistance/index.html', 'services'),
('planification', 'Planification', 'Calendriers & tâches', 7.00, 'modules/services/planification/index.html', 'services'),
('rendezvous', 'Rendez-vous', 'Bookings', 5.00, 'modules/services/rendezvous/index.html', 'services');

-- Insertion modules - Catégorie Productivité
INSERT INTO `modules` (`slug`, `title`, `description`, `price`, `path`, `category`) VALUES
('discussion', 'Discussion', 'Chat interne', 5.00, 'modules/productivite/discussion/index.html', 'productivite'),
('validations', 'Validations', 'Workflows', 9.00, 'modules/productivite/validations/index.html', 'productivite'),
('base-de-connaissances', 'Connaissances', 'Docs & FAQ', 8.00, 'modules/productivite/base-de-connaissances/index.html', 'productivite');

-- Insertion produits de démonstration
INSERT INTO `products` (`name`, `description`, `price`) VALUES
('Exemple Produit', 'Produit de démonstration', 9.99),
('Module Test', 'Module pour tests d\'intégration', 0.00),
('Service Premium', 'Service premium mensuel', 49.99);

-- Insertion souscriptions de test
INSERT INTO `user_modules` (`user_id`, `module_id`, `status`, `expires_at`) VALUES
(1, 1, 'active', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
(1, 7, 'active', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
(2, 2, 'active', DATE_ADD(NOW(), INTERVAL 6 MONTH));

-- Insertion commande de test
INSERT INTO `orders` (`user_id`, `total_amount`, `status`, `payment_method`) VALUES
(1, 45.00, 'completed', 'carte_bancaire');

INSERT INTO `order_items` (`order_id`, `module_id`, `price`, `quantity`) VALUES
(1, 1, 20.00, 1),
(1, 7, 25.00, 1);

-- ============================================
-- VUES UTILES
-- ============================================

-- Vue: Statistiques par catégorie
CREATE OR REPLACE VIEW `v_module_stats_by_category` AS
SELECT 
  `category`,
  COUNT(*) as `total_modules`,
  SUM(`price`) as `total_revenue`,
  AVG(`price`) as `avg_price`,
  MIN(`price`) as `min_price`,
  MAX(`price`) as `max_price`
FROM `modules`
WHERE `active` = 1
GROUP BY `category`;

-- Vue: Modules populaires
CREATE OR REPLACE VIEW `v_popular_modules` AS
SELECT 
  m.`id`,
  m.`title`,
  m.`category`,
  m.`price`,
  COUNT(um.`id`) as `subscribers_count`,
  SUM(CASE WHEN um.`status` = 'active' THEN 1 ELSE 0 END) as `active_subscribers`
FROM `modules` m
LEFT JOIN `user_modules` um ON m.`id` = um.`module_id`
GROUP BY m.`id`, m.`title`, m.`category`, m.`price`
ORDER BY `subscribers_count` DESC;

-- Vue: Utilisateurs actifs avec leurs modules
CREATE OR REPLACE VIEW `v_users_with_modules` AS
SELECT 
  u.`id` as `user_id`,
  u.`name`,
  u.`email`,
  u.`company`,
  COUNT(um.`id`) as `modules_count`,
  GROUP_CONCAT(m.`title` SEPARATOR ', ') as `modules_list`
FROM `users` u
LEFT JOIN `user_modules` um ON u.`id` = um.`user_id` AND um.`status` = 'active'
LEFT JOIN `modules` m ON um.`module_id` = m.`id`
WHERE u.`status` = 'active'
GROUP BY u.`id`, u.`name`, u.`email`, u.`company`;

-- ============================================
-- PROCÉDURES STOCKÉES
-- ============================================

DELIMITER //

-- Procédure: Ajouter un module à un utilisateur
CREATE PROCEDURE `sp_subscribe_user_to_module`(
  IN p_user_id INT,
  IN p_module_id INT,
  IN p_duration_months INT
)
BEGIN
  DECLARE v_exists INT;
  
  -- Vérifier si la souscription existe déjà
  SELECT COUNT(*) INTO v_exists 
  FROM `user_modules` 
  WHERE `user_id` = p_user_id AND `module_id` = p_module_id;
  
  IF v_exists > 0 THEN
    -- Mettre à jour la souscription existante
    UPDATE `user_modules`
    SET `status` = 'active',
        `expires_at` = DATE_ADD(NOW(), INTERVAL p_duration_months MONTH)
    WHERE `user_id` = p_user_id AND `module_id` = p_module_id;
  ELSE
    -- Créer une nouvelle souscription
    INSERT INTO `user_modules` (`user_id`, `module_id`, `status`, `expires_at`)
    VALUES (p_user_id, p_module_id, 'active', DATE_ADD(NOW(), INTERVAL p_duration_months MONTH));
  END IF;
END //

-- Procédure: Expirer les modules
CREATE PROCEDURE `sp_expire_modules`()
BEGIN
  UPDATE `user_modules`
  SET `status` = 'expired'
  WHERE `status` = 'active' 
    AND `expires_at` IS NOT NULL 
    AND `expires_at` < NOW();
    
  SELECT ROW_COUNT() as `expired_count`;
END //

-- Procédure: Statistiques dashboard admin
CREATE PROCEDURE `sp_admin_dashboard_stats`()
BEGIN
  SELECT 
    (SELECT COUNT(*) FROM `users` WHERE `status` = 'active') as `active_users`,
    (SELECT COUNT(*) FROM `users` WHERE `status` = 'pending') as `pending_users`,
    (SELECT COUNT(*) FROM `modules` WHERE `active` = 1) as `active_modules`,
    (SELECT COUNT(*) FROM `orders` WHERE `status` = 'completed') as `completed_orders`,
    (SELECT IFNULL(SUM(`total_amount`), 0) FROM `orders` WHERE `status` = 'completed') as `total_revenue`,
    (SELECT COUNT(*) FROM `user_modules` WHERE `status` = 'active') as `active_subscriptions`;
END //

DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================

DELIMITER //

-- Trigger: Logger création utilisateur
CREATE TRIGGER `tr_after_user_insert`
AFTER INSERT ON `users`
FOR EACH ROW
BEGIN
  INSERT INTO `activity_logs` (`user_id`, `action`, `entity_type`, `entity_id`, `details`)
  VALUES (NEW.`id`, 'user_created', 'user', NEW.`id`, CONCAT('Nouvel utilisateur: ', NEW.`name`));
END //

-- Trigger: Logger création admin
CREATE TRIGGER `tr_after_admin_insert`
AFTER INSERT ON `admins`
FOR EACH ROW
BEGIN
  INSERT INTO `activity_logs` (`admin_id`, `action`, `entity_type`, `entity_id`, `details`)
  VALUES (NEW.`id`, 'admin_created', 'admin', NEW.`id`, CONCAT('Nouvel admin: ', NEW.`name`));
END //

-- Trigger: Logger souscription module
CREATE TRIGGER `tr_after_user_module_insert`
AFTER INSERT ON `user_modules`
FOR EACH ROW
BEGIN
  INSERT INTO `activity_logs` (`user_id`, `action`, `entity_type`, `entity_id`, `details`)
  VALUES (NEW.`user_id`, 'module_subscribed', 'user_module', NEW.`id`, CONCAT('Module ID: ', NEW.`module_id`));
END //

DELIMITER ;

-- ============================================
-- INDEX ADDITIONNELS POUR PERFORMANCE
-- ============================================

-- Index composites pour requêtes fréquentes
CREATE INDEX `idx_user_module_status` ON `user_modules`(`user_id`, `status`);
CREATE INDEX `idx_order_user_status` ON `orders`(`user_id`, `status`);
CREATE INDEX `idx_module_category_active` ON `modules`(`category`, `active`);

-- ============================================
-- PERMISSIONS RECOMMANDÉES
-- ============================================
-- 
-- Pour l'application web:
-- CREATE USER 'skyvault_app'@'localhost' IDENTIFIED BY 'votre_mot_de_passe_secure';
-- GRANT SELECT, INSERT, UPDATE ON skyvault.* TO 'skyvault_app'@'localhost';
-- GRANT DELETE ON skyvault.sessions TO 'skyvault_app'@'localhost';
-- GRANT EXECUTE ON PROCEDURE skyvault.sp_subscribe_user_to_module TO 'skyvault_app'@'localhost';
-- GRANT EXECUTE ON PROCEDURE skyvault.sp_admin_dashboard_stats TO 'skyvault_app'@'localhost';
-- FLUSH PRIVILEGES;
--
-- Pour les administrateurs:
-- CREATE USER 'skyvault_admin'@'localhost' IDENTIFIED BY 'votre_mot_de_passe_admin_secure';
-- GRANT ALL PRIVILEGES ON skyvault.* TO 'skyvault_admin'@'localhost';
-- FLUSH PRIVILEGES;
--

-- ============================================
-- FIN DU SCRIPT
-- ============================================
-- Base de données SkyVault créée avec succès!
-- 
-- Tables créées: 11
-- - admins, users, modules, user_modules, orders, order_items
-- - products, sessions, activity_logs
--
-- Vues créées: 3
-- Procédures stockées: 3
-- Triggers: 3
--
-- Pour importer ce fichier:
-- mysql -u root -p < skyvault_complete.sql
-- ============================================
