-- ============================================================
-- SCHEMA BASE DE DONNÉES : Revue de Philosophie de Libreville (RPL)
-- Base : rpldb
-- Encodage : utf8mb4
-- Version : 1.0 — Avril 2026
-- ============================================================

CREATE DATABASE IF NOT EXISTS `rpldb`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `rpldb`;

-- ------------------------------------------------------------
-- TABLE : utilisateurs
-- Comptes membres et administrateurs
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id`           INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
  `nom`          VARCHAR(120)    NOT NULL,
  `email`        VARCHAR(255)    NOT NULL UNIQUE,
  `mot_de_passe` VARCHAR(255)    NOT NULL COMMENT 'Hash bcrypt',
  `role`         ENUM('utilisateur','admin') NOT NULL DEFAULT 'utilisateur',
  `bio`          TEXT            DEFAULT NULL,
  `affiliation`  VARCHAR(255)    DEFAULT NULL,
  `actif`        TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`   DATETIME        DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME        DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_role`  (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLE : articles
-- Articles publiés dans la revue
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `articles` (
  `id`               INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
  `titre`            VARCHAR(500)    NOT NULL,
  `auteur`           VARCHAR(255)    NOT NULL,
  `email_auteur`     VARCHAR(255)    DEFAULT NULL,
  `affiliation`      VARCHAR(255)    DEFAULT NULL,
  `rubrique`         ENUM(
                       'Article de recherche',
                       'Commentaire',
                       'Recension',
                       'Traduction'
                     ) NOT NULL DEFAULT 'Article de recherche',
  `resume`           TEXT            DEFAULT NULL,
  `contenu`          LONGTEXT        DEFAULT NULL,
  `fichier_path`     VARCHAR(500)    DEFAULT NULL COMMENT 'Chemin relatif vers le PDF',
  `annee`            YEAR            DEFAULT NULL,
  `volume`           TINYINT UNSIGNED DEFAULT NULL,
  `numero`           TINYINT UNSIGNED DEFAULT NULL,
  `pages`            VARCHAR(20)     DEFAULT NULL COMMENT 'Ex: 12-34',
  `statut`           ENUM('en_attente','publie','retire') NOT NULL DEFAULT 'publie',
  `date_publication` DATE            DEFAULT NULL,
  `created_at`       DATETIME        DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME        DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_statut`  (`statut`),
  INDEX `idx_annee`   (`annee`),
  INDEX `idx_rubrique`(`rubrique`),
  FULLTEXT KEY `ft_recherche` (`titre`, `auteur`, `resume`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLE : soumissions
-- Manuscrits soumis par les auteurs (non encore publiés)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `soumissions` (
  `id`           INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
  `nom_auteur`   VARCHAR(255)    NOT NULL,
  `email`        VARCHAR(255)    NOT NULL,
  `affiliation`  VARCHAR(255)    DEFAULT NULL,
  `titre`        VARCHAR(500)    NOT NULL,
  `rubrique`     ENUM(
                   'Article de recherche',
                   'Commentaire',
                   'Recension',
                   'Traduction'
                 ) NOT NULL DEFAULT 'Article de recherche',
  `resume`       TEXT            DEFAULT NULL,
  `fichier_path` VARCHAR(500)    NOT NULL COMMENT 'Chemin vers le fichier uploadé',
  `statut`       ENUM(
                   'recu',
                   'en_evaluation',
                   'accepte',
                   'refuse',
                   'revision_demandee'
                 ) NOT NULL DEFAULT 'recu',
  `commentaire_admin` TEXT        DEFAULT NULL,
  `user_id`      INT UNSIGNED    DEFAULT NULL,
  `date_soumission` DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME        DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_statut`  (`statut`),
  INDEX `idx_email`   (`email`),
  CONSTRAINT `fk_soumissions_user` FOREIGN KEY (`user_id`)
    REFERENCES `utilisateurs`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLE : system_logs
-- Journal des actions administratives
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `system_logs` (
  `id`           INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
  `utilisateur`  VARCHAR(120)    NOT NULL,
  `action_type`  VARCHAR(50)     NOT NULL COMMENT 'LOGIN, UPDATE, DELETE, BACKUP...',
  `cible`        VARCHAR(255)    DEFAULT NULL COMMENT 'Table ou entité concernée',
  `details`      TEXT            DEFAULT NULL,
  `ip_address`   VARCHAR(45)     DEFAULT NULL COMMENT 'Supporte IPv6',
  `date_action`  DATETIME        DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_action_type` (`action_type`),
  INDEX `idx_date_action` (`date_action`),
  INDEX `idx_utilisateur` (`utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLE : password_resets
-- Tokens de réinitialisation de mot de passe
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id`         INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT UNSIGNED  NOT NULL,
  `token`      VARCHAR(64)   NOT NULL UNIQUE,
  `expires_at` DATETIME      NOT NULL,
  `used`       TINYINT(1)    NOT NULL DEFAULT 0,
  `created_at` DATETIME      DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_token`   (`token`),
  INDEX `idx_user_id` (`user_id`),
  CONSTRAINT `fk_resets_user` FOREIGN KEY (`user_id`)
    REFERENCES `utilisateurs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLE : newsletter_subscribers
-- Abonnés à la newsletter scientifique
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id`            INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
  `email`         VARCHAR(255)  NOT NULL UNIQUE,
  `actif`         TINYINT(1)    NOT NULL DEFAULT 1,
  `subscribed_at` DATETIME      DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DONNÉES D'EXEMPLE
-- Créez un compte admin par défaut (mot de passe : Admin@RPL2026)
-- CHANGEZ CE MOT DE PASSE IMMÉDIATEMENT EN PRODUCTION !
-- ============================================================
INSERT IGNORE INTO `utilisateurs` (`nom`, `email`, `mot_de_passe`, `role`)
VALUES (
  'Administrateur RPL',
  'admin@rpl-libreville.org',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password (CHANGER !)
  'admin'
);

-- Article d'exemple
INSERT IGNORE INTO `articles` (`titre`, `auteur`, `affiliation`, `rubrique`, `resume`, `annee`, `statut`, `date_publication`)
VALUES (
  'La phénoménologie de l''existence chez Merleau-Ponty : perspectives africaines',
  'Dr. Exemple Auteur',
  'Université Omar Bongo, Libreville',
  'Article de recherche',
  'Cet article explore les résonances entre la phénoménologie merleau-pontienne et les conceptions africaines de la corporéité, en proposant une lecture croisée enrichissante.',
  2026,
  'publie',
  CURDATE()
);

-- ============================================================
-- NOTES D'INSTALLATION
-- ============================================================
-- 1. Importez ce fichier dans phpMyAdmin ou via la CLI :
--    mysql -u root -p < schema_bdd.sql
--
-- 2. Mettez à jour db_config.php et db_connect.php avec vos
--    identifiants MySQL.
--
-- 3. Créez le dossier d'upload et accordez les droits :
--    mkdir -p uploads/soumissions
--    chmod 755 uploads/soumissions
--
-- 4. Changez le mot de passe admin via l'interface :
--    admin_dashboard.php > Gestion utilisateurs
-- ============================================================