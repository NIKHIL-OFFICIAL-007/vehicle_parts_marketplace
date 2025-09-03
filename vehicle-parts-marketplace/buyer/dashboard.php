<?php
// Start session and include config
session_start();
include '../includes/config.php';

// Check if user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: ../login.php");
    exit();
}

// Get user name
$user_name = htmlspecialchars($_SESSION['name']);

// Simulate recent orders (you can replace with real DB query)
$recent_orders = [
    ['id' => 'ORD-1001', 'part' => 'Brake Pads Set', 'status' => 'Shipped', 'date' => '2025-04-01', 'total' => '$89.99'],
    ['id' => 'ORD-1002', 'part' => 'Oil Filter (Pack of 3)', 'status' => 'Delivered', 'date' => '2025-03-25', 'total' => '$24.99'],
    ['id' => 'ORD-1003', 'part' => 'Headlight Bulb (Pair)', 'status' => 'Processing', 'date' => '2025-03-20', 'total' => '$35.50']
];

// Simulate saved/wishlist items
$saved_parts = [
    ['name' => 'Engine Air Filter', 'price' => '$28.99', 'image' => 'https://via.placeholder.com/80'],
    ['name' => 'Spark Plug Set', 'price' => '$45.00', 'image' => 'https://via.placeholder.com/80'],
    ['name' => 'Wiper Blades (Front)', 'price' => '$19.99', 'image' => 'https://via.placeholder.com/80']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Buyer Dashboard - AutoParts Hub</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <!-- Custom Styles -->
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8fafc;
    }
    .card-hover:hover {
      transform: translateY(-4px);
      transition: all 0.3s ease;
    }
    .status-delivered {
      @apply bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full;
    }
    .status-shipped {
      @apply bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full;
    }
    .status-processing {
      @apply bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-900">

  <!-- Include Header -->
  <?php include '../includes/header.php'; ?>

  <!-- Dashboard Content -->
  <div class="container mx-auto px-6 py-12 max-w-6xl">

    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-2xl shadow-lg p-8 mb-10 text-center">
      <h1 class="text-3xl font-bold mb-2">Welcome back, <?= $user_name ?>!</h1>
      <p class="text-blue-100">Your one-stop dashboard for managing orders, saved parts, and vehicle needs.</p>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
      <a href="../index.php" class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg text-center card-hover border border-gray-100">
        <i class="fas fa-search text-4xl text-blue-600 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-800">Browse Parts</h3>
        <p class="text-gray-600 mt-2">Find the right vehicle parts fast</p>
      </a>

      <a href="#" class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg text-center card-hover border border-gray-100">
        <i class="fas fa-shopping-bag text-4xl text-green-600 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-800">My Orders</h3>
        <p class="text-gray-600 mt-2">Track your purchases and deliveries</p>
      </a>

      <a href="#" class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg text-center card-hover border border-gray-100">
        <i class="fas fa-heart text-4xl text-red-500 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-800">Saved Parts</h3>
        <p class="text-gray-600 mt-2">View your wishlist and favorites</p>
      </a>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-10">
      <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
        <i class="fas fa-shopping-cart mr-3 text-blue-600"></i>
        Recent Orders
      </h2>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-gray-500 border-b">
              <th class="pb-3">Order ID</th>
              <th class="pb-3">Part</th>
              <th class="pb-3">Date</th>
              <th class="pb-3">Total</th>
              <th class="pb-3">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php foreach ($recent_orders as $order): ?>
              <tr class="hover:bg-gray-50">
                <td class="py-3 font-medium">#<?= $order['id'] ?></td>
                <td class="py-3"><?= $order['part'] ?></td>
                <td class="py-3 text-gray-600"><?= $order['date'] ?></td>
                <td class="py-3 font-semibold"><?= $order['total'] ?></td>
                <td class="py-3">
                  <?php if ($order['status'] === 'Delivered'): ?>
                    <span class="status-delivered">Delivered</span>
                  <?php elseif ($order['status'] === 'Shipped'): ?>
                    <span class="status-shipped">Shipped</span>
                  <?php else: ?>
                    <span class="status-processing">Processing</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Saved Parts -->
    <div class="bg-white rounded-xl shadow-md p-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
        <i class="fas fa-heart mr-3 text-red-500"></i>
        Saved Parts
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($saved_parts as $part): ?>
          <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
            <div class="flex items-center space-x-4">
              <img src="<?= $part['image'] ?>" alt="<?= $part['name'] ?>" class="w-20 h-20 object-cover rounded">
              <div>
                <h3 class="font-semibold text-gray-800"><?= $part['name'] ?></h3>
                <p class="text-green-600 font-bold mt-1"><?= $part['price'] ?></p>
                <button class="mt-2 text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                  Add to Cart
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Call to Action -->
    <div class="mt-12 text-center">
      <p class="text-gray-600 mb-4">Need help finding the right part?</p>
      <a href="#contact" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-semibold">
        Contact Support
      </a>
    </div>

  </div>

  <!-- Include Footer -->
  <?php include '../includes/footer.php'; ?>
</body>
</html>