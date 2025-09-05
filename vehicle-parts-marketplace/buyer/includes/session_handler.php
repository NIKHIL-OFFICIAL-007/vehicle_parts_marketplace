<?php
// buyer/includes/session_handler.php

function checkBuyerSession() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../login.php");
        exit();
    }

    $pdo = getPDO(); // ← Now works!
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || $user['role'] !== 'buyer') {
        header("Location: ../../index.php");
        exit();
    }

    $_SESSION['role'] = $user['role'];
}