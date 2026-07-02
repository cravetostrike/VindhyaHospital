-- ============================================================
--  Vindhya Hospital (VHRC) — MySQL Database Schema
--  Import this file via phpMyAdmin > Import tab
--  Or let the app auto-create it by visiting the site once.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `vindhya_hospital`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `vindhya_hospital`;

-- ------------------------------------------------------------
-- Table: admins
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
    `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `username`   VARCHAR(100)  NOT NULL UNIQUE,
    `password`   VARCHAR(255)  NOT NULL,
    `created_at` DATETIME      DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin: username=admin  password=admin123
INSERT IGNORE INTO `admins` (`username`, `password`)
VALUES ('admin', '$2y$10$placeholderHashReplaceOnFirstAppLoad');

-- ------------------------------------------------------------
-- Table: appointments
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `appointments` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `patient_name`     VARCHAR(200) NOT NULL,
    `patient_phone`    VARCHAR(30)  NOT NULL,
    `patient_email`    VARCHAR(200) DEFAULT NULL,
    `appointment_date` VARCHAR(50)  NOT NULL,
    `department`       VARCHAR(200) DEFAULT NULL,
    `doctor`           VARCHAR(200) DEFAULT NULL,
    `message`          TEXT         DEFAULT NULL,
    `status`           VARCHAR(50)  NOT NULL DEFAULT 'Pending',
    `created_at`       DATETIME     DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: doctors
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `doctors` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`           VARCHAR(200) NOT NULL,
    `specialty`      VARCHAR(200) NOT NULL,
    `qualifications` TEXT         NOT NULL,
    `experience`     VARCHAR(100) DEFAULT NULL,
    `image_path`     VARCHAR(500) DEFAULT NULL,
    `social_fb`      VARCHAR(500) NOT NULL DEFAULT '',
    `social_tw`      VARCHAR(500) NOT NULL DEFAULT '',
    `social_ig`      VARCHAR(500) NOT NULL DEFAULT '',
    `social_in`      VARCHAR(500) NOT NULL DEFAULT '',
    `created_at`     DATETIME     DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: gallery_posters
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gallery_posters` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `image_path` VARCHAR(500) NOT NULL,
    `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: hero_slides
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `hero_slides` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `slide_order` INT          NOT NULL UNIQUE,
    `tag`         VARCHAR(300) DEFAULT NULL,
    `title`       TEXT         DEFAULT NULL,
    `description` TEXT         DEFAULT NULL,
    `image_path`  VARCHAR(500) DEFAULT NULL,
    `cta1_text`   VARCHAR(200) DEFAULT NULL,
    `cta1_link`   VARCHAR(500) DEFAULT NULL,
    `cta2_text`   VARCHAR(200) DEFAULT NULL,
    `cta2_link`   VARCHAR(500) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: homepage_settings
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `homepage_settings` (
    `key`   VARCHAR(100) NOT NULL,
    `value` TEXT         NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
