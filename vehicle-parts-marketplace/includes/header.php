<?php
// ✅ DO NOT include session_start() here!
// It's already started in index.php, login.php, etc.

include 'config.php';

$logged_in = isset($_SESSION['user_id']);
$user_name = '';
$role = 'buyer';  // Default role
$role_status = 'approved';

if ($logged_in) {
    try {
        // Fetch user data
        $stmt = $pdo->prepare("SELECT name, role, role_status FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['role_status'] = $user['role_status'];

            $user_name = htmlspecialchars($user['name']);
            $role = $user['role'];
            $role_status = $user['role_status'];
        } else {
            session_destroy();
            header("Location: ../login.php");
            exit();
        }
    } catch (Exception $e) {
        error_log("Header error: " . $e->getMessage());
        session_destroy();
        header("Location: ../login.php");
        exit();
    }
}

// Fetch unread notification count
$unread_count = 0;
if ($logged_in) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = FALSE");
        $stmt->execute([$_SESSION['user_id']]);
        $unread_count = (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        $unread_count = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>AutoParts Hub</title>

  <!-- ✅ Fixed: Removed extra spaces in CDN URLs -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8fafc;
    }
    .dropdown-menu {
      display: none;
      position: absolute;
      right: 0;
      top: 100%;
      background: white;
      border: 1px solid #e2e8f0;
      border-radius: 0.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      z-index: 50;
    }
    .dropdown.open .dropdown-menu {
      display: block;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-900">
  <!-- Navigation -->
  <header class="bg-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-6 py-4 flex items-center justify-between">
      <!-- Logo -->
      <a href="../index.php" class="flex items-center space-x-3">
        <i class="fas fa-tools text-3xl text-blue-600"></i>
        <span class="text-2xl font-bold text-gray-800">AutoParts Hub</span>
      </a>

      <!-- Desktop Nav -->
      <nav class="hidden md:flex space-x-8">
        <a href="../index.php#home" class="font-medium hover:text-blue-600 transition">Home</a>
        <a href="../index.php#features" class="font-medium hover:text-blue-600 transition">Features</a>
        <a href="../index.php#how-it-works" class="font-medium hover:text-blue-600 transition">How It Works</a>
        <a href="../index.php#role-apply" class="font-medium hover:text-blue-600 transition">Apply Role</a>
        <a href="../index.php#contact" class="font-medium hover:text-blue-600 transition">Contact</a>

        <!-- Dashboard Links -->
        <?php if ($logged_in): ?>
          <?php if ($role === 'admin'): ?>
            <a href="../admin/dashboard.php" class="font-medium hover:text-blue-600 transition">Admin Dashboard</a>
          <?php elseif ($role === 'seller'): ?>
            <a href="../seller/dashboard.php" class="font-medium hover:text-blue-600 transition">Seller Dashboard</a>
          <?php elseif ($role === 'support'): ?>
            <a href="../support/dashboard.php" class="font-medium hover:text-blue-600 transition">Support Dashboard</a>
          <?php else: ?>
            <a href="../buyer/dashboard.php" class="font-medium hover:text-blue-600 transition">Buyer Dashboard</a>
          <?php endif; ?>
        <?php endif; ?>
      </nav>

      <!-- User Actions -->
      <div class="flex items-center space-x-4">
        <?php if ($logged_in): ?>
          <!-- Profile Dropdown -->
          <div class="relative dropdown">
            <button class="text-sm font-medium text-gray-700"
                    onclick="this.parentElement.classList.toggle('open')">
              <?= $user_name ?> <i class="fas fa-chevron-down text-xs ml-1"></i>
            </button>
            <div class="dropdown-menu">
              <a href="../profile.php" class="flex items-center px-4 py-2 hover:bg-gray-50 border-b border-gray-100">
                <i class="fas fa-user text-gray-500 mr-3"></i> My Profile
              </a>
              <a href="../my_requests.php" class="flex items-center px-4 py-2 hover:bg-gray-50 border-b border-gray-100">
                <i class="fas fa-tasks text-gray-500 mr-3"></i> My Requests
              </a>
              <a href="../notifications.php" class="flex items-center px-4 py-2 hover:bg-gray-50 border-b border-gray-100 relative">
                <i class="fas fa-bell text-gray-500 mr-3"></i> Notifications
                <?php if ($unread_count > 0): ?>
                  <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full"><?= $unread_count ?></span>
                <?php endif; ?>
              </a>
              <a href="../logout.php" class="flex items-center px-4 py-2 hover:bg-gray-50 text-red-600">
                <i class="fas fa-sign-out-alt text-red-500 mr-3"></i> Logout
              </a>
            </div>
          </div>
        <?php else: ?>
          <a href="../login.php" class="px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition">Login</a>
          <a href="../register.php" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Join Now</a>
        <?php endif; ?>
      </div>
    </div>
  </header>