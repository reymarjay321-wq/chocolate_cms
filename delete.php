<?php
/**
 * Chocolate Management System — Delete Single (delete.php)
 * Deletes one chocolate and its image file, then redirects with flash
 */
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Fetch image filename first
    $stmt = $conn->prepare("SELECT name, image FROM chocolates WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ch = $result->fetch_assoc();
    $stmt->close();

    if ($ch) {
        // Delete image file if exists
        if (!empty($ch['image']) && file_exists(UPLOAD_DIR . $ch['image'])) {
            @unlink(UPLOAD_DIR . $ch['image']);
        }

        // Delete DB record
        $del = $conn->prepare("DELETE FROM chocolates WHERE id = ?");
        $del->bind_param('i', $id);
        if ($del->execute()) {
            setFlash('success', "🗑️ \"{$ch['name']}\" has been deleted.");
        } else {
            setFlash('error', 'Failed to delete: ' . $del->error);
        }
        $del->close();
    } else {
        setFlash('warning', 'Chocolate not found.');
    }
} else {
    setFlash('error', 'Invalid chocolate ID.');
}

header('Location: index.php');
exit;
