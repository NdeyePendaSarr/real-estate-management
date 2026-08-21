-- ============================================================================
-- Agence Immobilière — schéma refondu (2026)
-- Modèle unifié : une table `biens` (appartement|villa) et une table
-- `utilisateurs` (client|commercial|admin), au lieu des tables dupliquées
-- Appartements/Villas et des trois tables d'authentification d'origine.
-- Compatible MySQL 8+ / MariaDB 10.4+
-- ============================================================================

CREATE DATABASE IF NOT EXISTS agence_immobiliere
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agence_immobiliere;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS paiements;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS favoris;
DROP TABLE IF EXISTS images;
DROP TABLE IF EXISTS biens;
DROP TABLE IF EXISTS utilisateurs;
SET FOREIGN_KEY_CHECKS = 1;

-- Utilisateurs : un seul modèle, un rôle décide des droits.
CREATE TABLE utilisateurs (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nom          VARCHAR(80)  NOT NULL,
    prenom       VARCHAR(80)  NOT NULL,
    email        VARCHAR(160) NOT NULL,
    telephone    VARCHAR(30)  NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role         ENUM('client','commercial','admin') NOT NULL DEFAULT 'client',
    statut       ENUM('actif','inactif') NOT NULL DEFAULT 'actif',
    cree_le      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Biens : appartements et villas dans une seule table (colonne `type`).
CREATE TABLE biens (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    type        ENUM('appartement','villa') NOT NULL,
    titre       VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    prix        INT UNSIGNED NOT NULL,
    ville       VARCHAR(80)  NOT NULL DEFAULT 'Dakar',
    chambres    TINYINT UNSIGNED NOT NULL DEFAULT 1,
    surface     SMALLINT UNSIGNED NULL,
    statut      ENUM('disponible','loue') NOT NULL DEFAULT 'disponible',
    cree_le     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_statut (statut),
    INDEX idx_ville (ville)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Images : rattachées à un bien unique (fini les colonnes appart/villa nullables).
CREATE TABLE images (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    bien_id    INT NOT NULL,
    fichier    VARCHAR(255) NOT NULL,
    principale TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_images_bien FOREIGN KEY (bien_id)
        REFERENCES biens(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Favoris : contrainte d'unicité => plus de doublons possibles.
CREATE TABLE favoris (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    bien_id        INT NOT NULL,
    cree_le        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_favori (utilisateur_id, bien_id),
    CONSTRAINT fk_fav_user FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateurs(id) ON DELETE CASCADE,
    CONSTRAINT fk_fav_bien FOREIGN KEY (bien_id)
        REFERENCES biens(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Réservations : bien + client + période + statut.
CREATE TABLE reservations (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    bien_id        INT NOT NULL,
    utilisateur_id INT NOT NULL,
    date_debut     DATE NOT NULL,
    date_fin       DATE NOT NULL,
    statut         ENUM('en_attente','confirmee','annulee') NOT NULL DEFAULT 'en_attente',
    cree_le        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_res_bien FOREIGN KEY (bien_id)
        REFERENCES biens(id) ON DELETE CASCADE,
    CONSTRAINT fk_res_user FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_res_statut (statut),
    INDEX idx_res_dates (bien_id, date_debut, date_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Paiements : rattachés à une réservation (et non plus « au client » dans le vide).
CREATE TABLE paiements (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    montant        INT UNSIGNED NOT NULL,
    date_paiement  DATE NOT NULL,
    CONSTRAINT fk_pay_res FOREIGN KEY (reservation_id)
        REFERENCES reservations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
