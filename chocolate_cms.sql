-- ================================================================
-- Chocolate Management System — Database Setup SQL
-- Import this file via phpMyAdmin or MySQL CLI:
--   mysql -u root -p < chocolate_cms.sql
-- ================================================================

CREATE DATABASE IF NOT EXISTS `chocolate_cms`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `chocolate_cms`;

-- Drop and recreate table
DROP TABLE IF EXISTS `chocolates`;

CREATE TABLE `chocolates` (
    `id`              INT(11)        NOT NULL AUTO_INCREMENT,
    `name`            VARCHAR(150)   NOT NULL,
    `brand`           VARCHAR(100)   NOT NULL,
    `category`        VARCHAR(100)   NOT NULL,
    `flavor`          VARCHAR(100)   NOT NULL,
    `price`           DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `quantity`        INT(11)        NOT NULL DEFAULT 0,
    `expiration_date` DATE           NOT NULL,
    `description`     TEXT,
    `image`           VARCHAR(255)   DEFAULT NULL,
    `date_added`      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data
INSERT INTO `chocolates` (`name`,`brand`,`category`,`flavor`,`price`,`quantity`,`expiration_date`,`description`) VALUES
('Silk Dark Bar',      'Cadbury',    'Dark Chocolate',    'Dark',       3.99, 150, '2026-12-31', 'Premium dark chocolate with 70% cocoa.'),
('Milk Delight',       'Hershey',    'Milk Chocolate',    'Milk',       2.49, 200, '2026-10-15', 'Classic creamy milk chocolate.'),
('Hazelnut Crunch',    'Ferrero',    'Praline',           'Hazelnut',   5.99,  75, '2026-09-30', 'Hazelnut praline encased in chocolate.'),
('Mint Bliss',         'Lindt',      'Mint Chocolate',    'Mint',       4.50,  60, '2026-11-20', 'Swiss chocolate with refreshing mint.'),
('Caramel Swirl',      'Ghirardelli','Caramel Chocolate', 'Caramel',   6.25,  40, '2026-08-10', 'Smooth caramel filled chocolate squares.'),
('Almond Joy Bar',     'Mounds',     'Nut Chocolate',     'Almond',    3.25,  18, '2026-07-25', 'Coconut and almond in milk chocolate.'),
('White Dream',        'Nestle',     'White Chocolate',   'Vanilla',   2.99,  90, '2026-12-05', 'Creamy white chocolate with vanilla.'),
('Espresso Kick',      'Godiva',     'Dark Chocolate',    'Coffee',    7.99,  35, '2026-10-01', 'Bold espresso infused dark chocolate.'),
('Strawberry Blush',   'Milka',      'Fruit Chocolate',   'Strawberry',3.75,  12, '2026-06-30', 'Alpine milk chocolate with strawberry.'),
('Sea Salt Noir',      'Valrhona',   'Dark Chocolate',    'Sea Salt',  9.50,  28, '2026-11-15', 'Single origin dark with Fleur de Sel.');
