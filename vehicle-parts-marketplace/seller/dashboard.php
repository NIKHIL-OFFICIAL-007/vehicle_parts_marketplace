<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Seller Dashboard | AutoParts Hub</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 py-10">
    <div class="bg-white rounded-lg shadow p-6 max-w-3xl mx-auto text-center">
      <i class="fas fa-store text-6xl text-green-600 mb-4"></i>
      <h1 class="text-2xl font-bold text-gray-800">Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h1>
      <p class="text-gray-600 mb-6">You are logged in as a <strong>Seller</strong>.</p>

      <div class="space-y-4">
        <a href="add_product.php" class="block py-3 bg-green-600 text-white rounded hover:bg-green-700">Add New Product</a>
        <a href="my_products.php" class="block py-3 bg-teal-600 text-white rounded hover:bg-teal-700">Manage Inventory</a>
        <a href="orders.php" class="block py-3 bg-gray-600 text-white rounded hover:bg-gray-700">View Orders</a>
        <a href="reports.php" class="block py-3 bg-indigo-600 text-white rounded hover:bg-indigo-700">Sales Reports</a>
      </div>
    </div>

    <div class="text-center mt-6">
      <a href="../logout.php" class="text-red-600 hover:underline text-sm">Logout</a>
    </div>
  </div>
</body>
</html>