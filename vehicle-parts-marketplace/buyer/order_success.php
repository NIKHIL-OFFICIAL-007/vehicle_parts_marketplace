<?php
session_start();
include 'includes/config.php';

// Check if user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: ../../login.php");
    exit();
}

$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Order Success - AutoParts Hub</title>

  <!-- ✅ Correct Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <style>
    .success-icon {
      font-size: 4rem;
      color: #10b981;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-900">

  <?php include 'includes/buyer_header.php'; ?>

  <!-- Page Header -->
  <div class="py-12 bg-gradient-to-r from-green-600 to-green-800 text-white">
    <div class="container mx-auto px-6 text-center">
      <h1 class="text-4xl md:text-5xl font-bold mb-4">Order Confirmed!</h1>
      <p class="text-green-100 max-w-2xl mx-auto text-lg">Thank you for your purchase. Your order is being processed.</p>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container mx-auto px-6 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
      <div class="p-6 text-center">
        <i class="fas fa-check-circle success-icon mb-4"></i>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Order #<?= htmlspecialchars($order_id) ?></h2>
        <p class="text-gray-600 mb-6">Your order has been placed successfully. You will receive an email confirmation shortly.</p>

        <div class="border-t pt-6 mt-6">
          <div class="flex justify-center space-x-4">
            <a href="orders.php" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
              View Orders
            </a>
            <a href="dashboard.php" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
              Dashboard
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include 'includes/buyer_footer.php'; ?>
</body>
</html>