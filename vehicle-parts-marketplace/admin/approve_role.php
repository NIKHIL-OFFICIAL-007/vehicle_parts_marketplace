<?php
session_start();
include 'includes/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !in_array('admin', $_SESSION['roles'] ?? [])) {
    header("Location: ../../login.php");
    exit();
}

if ($_POST) {
    $application_id = $_POST['application_id'] ?? null;
    $user_id = $_POST['user_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$application_id || !$user_id || !in_array($action, ['approve', 'reject'])) {
        header("Location: role_requests.php?error=invalid_request");
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Fetch the application details
        $stmt = $pdo->prepare("
            SELECT ra.requested_role, u.name 
            FROM role_applications ra 
            JOIN users u ON ra.user_id = u.id 
            WHERE ra.id = ? AND ra.user_id = ?
        ");
        $stmt->execute([$application_id, $user_id]);
        $app = $stmt->fetch();

        if (!$app) {
            throw new Exception("Application not found or unauthorized.");
        }

        $requested_role = $app['requested_role'];

        if ($action === 'approve') {
            // Check if user already has this role (approved)
            $stmt = $pdo->prepare("SELECT id FROM user_roles WHERE user_id = ? AND role = ? AND status = 'approved'");
            $stmt->execute([$user_id, $requested_role]);
            if ($stmt->fetch()) {
                throw new Exception("User already has this role.");
            }

            // Insert or update role in user_roles
            $stmt = $pdo->prepare("
                INSERT INTO user_roles (user_id, role, status) 
                VALUES (?, ?, 'approved') 
                ON DUPLICATE KEY UPDATE status = 'approved'
            ");
            $stmt->execute([$user_id, $requested_role]);

            // Notify user
            $pdo->prepare("
                INSERT INTO notifications (user_id, message, type) 
                VALUES (?, '🎉 Your request to become a " . ucfirst($requested_role) . " has been approved!', 'role_approved')
            ")->execute([$user_id]);
        }

        // Update application status
        $status = $action === 'approve' ? 'approved' : 'rejected';
        $pdo->prepare("UPDATE role_applications SET status = ? WHERE id = ?")
            ->execute([$status, $application_id]);

        // Log admin action
        $admin_id = $_SESSION['user_id'];
        $pdo->prepare("
            INSERT INTO admin_logs (admin_id, action, target_user_id, details) 
            VALUES (?, 'role_action', ?, ?)
        ")->execute([$admin_id, $user_id, "$action: $requested_role"]);

        $pdo->commit();

        $message = $action === 'approve' ? 'approved' : 'rejected';
        header("Location: role_requests.php?message=role_$message");
        exit();

    } catch (Exception $e) {
        $pdo->rollback();
        error_log("Admin role action failed: " . $e->getMessage());
        header("Location: role_requests.php?error=action_failed");
        exit();
    }
}

// Redirect if not POST
header("Location: role_requests.php");
exit();
?>