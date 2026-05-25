<?php
/**
 * Chocolate Management System — Delete All (delete_all.php)
 * Removes every chocolate record and clears uploaded images
 */
require_once 'db.php';

// Delete all image files from uploads folder
$imgs = $conn->query("SELECT image FROM chocolates WHERE image IS NOT NULL AND image != ''");
while ($row = $imgs->fetch_assoc()) {
    $path = UPLOAD_DIR . $row['image'];
    if (file_exists($path)) {
        @unlink($path);
    }
}

// Truncate table (resets AUTO_INCREMENT too)
if ($conn->query("TRUNCATE TABLE chocolates")) {
    setFlash('success', '🗑️ All chocolates have been permanently deleted.');
} else {
    setFlash('error', 'Failed to delete all: ' . $conn->error);
}

header('Location: index.php');
exit;
