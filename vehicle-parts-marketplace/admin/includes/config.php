<?php
// Database configuration
$host = 'localhost';
$dbname = 'vehicle_parts_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// Do NOT include admin_header.php here!
// That would cause an infinite loop