-- Migration: Ajouter la table modules et importer les données du JSON

USE `skyvault`;

-- Créer la table modules
CREATE TABLE IF NOT EXISTS `modules` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10,2) DEFAULT 0.00,
  `path` VARCHAR(255) NOT NULL,
  `category` VARCHAR(50) DEFAULT NULL,
  `active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_category` (`category`),
  INDEX `idx_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Importer les données du modules.json
INSERT INTO `modules` (`slug`, `title`, `description`, `price`, `path`, `category`) VALUES
-- Finance
('comptabilite', 'Comptabilité', 'Gestion des comptes et journaux', 20.00, 'modules/finance/comptabilite/index.html', 'finance'),
('facturation', 'Facturation', 'Devis, factures et relances', 15.00, 'modules/finance/facturation/index.html', 'finance'),
('notes-de-frais', 'Notes de frais', 'Saisie et remboursement', 8.00, 'modules/finance/notes-de-frais/index.html', 'finance'),
('feuilles-de-calcul', 'Feuilles de calcul (BI)', 'Tableaux de bord et BI', 25.00, 'modules/finance/feuilles-de-calcul/index.html', 'finance'),
('documents', 'Documents', 'Stockage et archivage', 5.00, 'modules/finance/documents/index.html', 'finance'),
('signature', 'Signature', 'Signature électronique', 7.00, 'modules/finance/signature/index.html', 'finance'),

-- Ventes
('crm', 'CRM', 'Gestion de la relation client', 25.00, 'modules/ventes/crm/index.html', 'ventes'),
('ventes', 'Ventes', 'Gestion commerciale', 22.00, 'modules/ventes/ventes/index.html', 'ventes'),
('pdv-boutique', 'PdV Boutique', 'Point de vente — boutique', 19.00, 'modules/ventes/pdv-boutique/index.html', 'ventes'),
('pdv-restaurant', 'PdV Restaurant', 'Point de vente — restaurant', 19.00, 'modules/ventes/pdv-restaurant/index.html', 'ventes'),
('abonnements', 'Abonnements', 'Gestion des abonnements', 12.00, 'modules/ventes/abonnements/index.html', 'ventes'),
('location', 'Location', 'Gestion de locations', 15.00, 'modules/ventes/location/index.html', 'ventes'),

-- Communication
('blog', 'Blog', 'Publication d\'articles', 8.00, 'modules/communication/blog/index.html', 'communication'),
('forum', 'Forum', 'Discussions communautaires', 6.00, 'modules/communication/forum/index.html', 'communication'),
('live-chat', 'Live Chat', 'Chat en direct', 15.00, 'modules/communication/live-chat/index.html', 'communication'),
('e-learning', 'eLearning', 'Formations en ligne', 30.00, 'modules/communication/e-learning/index.html', 'communication'),

-- Chaîne approvisionnement
('inventaire', 'Inventaire', 'Stocks et mouvements', 12.00, 'modules/chaine-approvisionnement/inventaire/index.html', 'chaine-approvisionnement'),
('fabrication', 'Fabrication', 'Gestion de production', 30.00, 'modules/chaine-approvisionnement/fabrication/index.html', 'chaine-approvisionnement'),
('achats', 'Achats', 'Demandes et fournisseurs', 18.00, 'modules/chaine-approvisionnement/achats/index.html', 'chaine-approvisionnement'),
('maintenance', 'Maintenance', 'GMAO', 9.00, 'modules/chaine-approvisionnement/maintenance/index.html', 'chaine-approvisionnement'),
('qualite', 'Qualité', 'Contrôles & audits', 8.00, 'modules/chaine-approvisionnement/qualite/index.html', 'chaine-approvisionnement'),

-- RH
('employes', 'Employés', 'Dossier salarié', 18.00, 'modules/rh/employes/index.html', 'rh'),
('recrutement', 'Recrutement', 'Offres & candidatures', 15.00, 'modules/rh/recrutement/index.html', 'rh'),
('conges', 'Congés', 'Gestion des absences', 7.00, 'modules/rh/conges/index.html', 'rh'),
('evaluations', 'Évaluations', 'Performance', 12.00, 'modules/rh/evaluations/index.html', 'rh'),
('recommandations', 'Recommandations', 'Références & matching', 8.00, 'modules/rh/recommandations/index.html', 'rh'),

-- Services
('projet', 'Projet', 'Gestion de projet', 14.00, 'modules/services/projet/index.html', 'services'),
('feuilles-de-temps', 'Feuilles de temps', 'Temps & activités', 9.00, 'modules/services/feuilles-de-temps/index.html', 'services'),
('assistance', 'Assistance', 'Ticketing', 8.00, 'modules/services/assistance/index.html', 'services'),
('planification', 'Planification', 'Calendriers & tâches', 7.00, 'modules/services/planification/index.html', 'services'),
('rendezvous', 'Rendez-vous', 'Bookings', 5.00, 'modules/services/rendezvous/index.html', 'services'),

-- Productivité
('discussion', 'Discussion', 'Chat interne', 5.00, 'modules/productivite/discussion/index.html', 'productivite'),
('validations', 'Validations', 'Workflows', 9.00, 'modules/productivite/validations/index.html', 'productivite'),
('base-de-connaissances', 'Connaissances', 'Docs & FAQ', 8.00, 'modules/productivite/base-de-connaissances/index.html', 'productivite');
