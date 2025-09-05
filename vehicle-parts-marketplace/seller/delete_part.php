<?php
session_start();
include 'includes/config.php';

// Check if user is logged in and is an approved seller
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller' || $_SESSION['role_status'] !== 'approved') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get part ID
$part_id = $_GET['id'] ?? null;
if (!$part_id) {
    header("Location: manage_parts.php");
    exit();
}

// Verify the part belongs to this seller
try {
    $stmt = $pdo->prepare("SELECT id FROM parts WHERE id = ? AND seller_id = ?");
    $stmt->execute([$part_id, $user_id]);
    $part = $stmt->fetch();

    if (!$part) {
        // Part not found or doesn't belong to seller
        header("Location: manage_parts.php");
        exit();
    }
} catch (Exception $e) {
    error_log("Failed to verify part ownership: " . $e->getMessage());
    header("Location: manage_parts.php");
    exit();
}

// Delete the part
try {
    $stmt = $pdo->prepare("DELETE FROM parts WHERE id = ? AND seller_id = ?");
    $stmt->execute([$part_id, $user_id]);

    header("Location: manage_parts.php?message=part_deleted");
    exit();
} catch (Exception $e) {
    error_log("Failed to delete part: " . $e->getMessage());
    header("Location: manage_parts.php?error=delete_failed");
    exit();
}
?>