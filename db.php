<?php
/**
 * Chocolate Management System
 * Database Configuration File
 * Connects to MySQL using MySQLi with prepared statements
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'chocolate_cms');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("<div style='font-family:sans-serif;padding:2rem;background:#1a0a00;color:#ff6b35;'>
        <h2>⚠ Database Connection Failed</h2>
        <p>" . $conn->connect_error . "</p>
        <p>Make sure XAMPP MySQL is running and credentials are correct in <code>db.php</code>.</p>
    </div>");
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db(DB_NAME);

// Create chocolates table
$conn->query("CREATE TABLE IF NOT EXISTS `chocolates` (
    `id`              INT(11) NOT NULL AUTO_INCREMENT,
    `name`            VARCHAR(150) NOT NULL,
    `brand`           VARCHAR(100) NOT NULL,
    `category`        VARCHAR(100) NOT NULL,
    `flavor`          VARCHAR(100) NOT NULL,
    `price`           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `quantity`        INT(11) NOT NULL DEFAULT 0,
    `expiration_date` DATE NOT NULL,
    `description`     TEXT,
    `image`           VARCHAR(255) DEFAULT NULL,
    `date_added`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Helper: flash message via session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function setFlash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}
function getFlash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

// Uploads directory
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', 'uploads/');
