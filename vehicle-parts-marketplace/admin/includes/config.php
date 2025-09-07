<?php
// admin/includes/config.php

// Include main config
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Fetch user data
try {
    $stmt = $pdo->prepare("SELECT name, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header("Location: ../../login.php");
        exit();
    }

    // Check if user is admin
    if ($user['role'] !== 'admin') {
        header("Location: ../../index.php");
        exit();
    }

    // Set session and variables
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];
    $user_name = htmlspecialchars($user['name']);

} catch (Exception $e) {
    error_log("Failed to fetch user: " . $e->getMessage());
    session_destroy();
    header("Location: ../../login.php");
    exit();
}
?>