<?php
session_start();
include 'includes/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $action = $_POST['action'] ?? '';
    
    if ($user_id && in_array($action, ['approve', 'reject'])) {
        try {
            // Fetch the requested role first
            $stmt = $pdo->prepare("SELECT role_request FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                header("Location: role_requests.php?error=action_failed");
                exit();
            }

            if ($action === 'approve') {
                $requested_role = $user['role_request'];
                
                // Validate role
                if (!in_array($requested_role, ['seller', 'support', 'admin'])) {
                    header("Location: role_requests.php?error=action_failed");
                    exit();
                }

                // Approve: set role, status, clear request
                $stmt = $pdo->prepare("UPDATE users SET role = ?, role_status = 'approved', role_request = NULL WHERE id = ?");
                $stmt->execute([$requested_role, $user_id]);
                
                // Add notification
                $pdo->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, '🎉 Your request to become a " . ucfirst($requested_role) . " has been approved!', 'role_approved')")
                    ->execute([$user_id]);
            } else {
                // Reject: clear request, set status
                $stmt = $pdo->prepare("UPDATE users SET role_status = 'rejected', role_request = NULL WHERE id = ?");
                $stmt->execute([$user_id]);

                // Add notification
                $pdo->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, '❌ Your role request has been rejected.', 'role_rejected')")
                    ->execute([$user_id]);
            }

            // Log admin action
            $pdo->prepare("INSERT INTO admin_logs (admin_id, action, target_user_id, details) VALUES (?, 'role_action', ?, ?)")
                ->execute([$_SESSION['user_id'], $user_id, "$action: " . ($user['role_request'] ?? 'unknown')]);

            $message = $action === 'approve' ? 'role_approved' : 'role_rejected';
            header("Location: role_requests.php?message=$message");
            exit();
            
        } catch (Exception $e) {
            error_log("Role approval error: " . $e->getMessage());
            header("Location: role_requests.php?error=action_failed");
            exit();
        }
    } else {
        header("Location: role_requests.php?error=action_failed");
        exit();
    }
} else {
    header("Location: role_requests.php");
    exit();
}
?>