<?php
session_start();
include 'includes/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Fetch all parts from database
$parts = [];
try {
    $stmt = $pdo->query("
        SELECT p.id, p.name, p.category, p.price, p.stock_quantity as stock
        FROM parts p
        ORDER BY p.created_at DESC
    ");
    $parts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Failed to fetch parts: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Parts - Admin Panel</title>

  <!-- ✅ Fixed: Removed extra spaces in CDN URLs -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="bg-gray-50 text-gray-900">

  <?php include 'includes/admin_header.php'; ?>

  <!-- Page Header -->
  <div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-800">Manage Parts</h1>
    <p class="text-gray-600 mt-1">Add, edit, or remove vehicle parts from the marketplace.</p>
  </div>

  <!-- Actions -->
  <div class="flex justify-end mb-6">
    <a href="add_part.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
      <i class="fas fa-plus mr-2"></i> Add New Part
    </a>
  </div>

  <!-- Parts Table -->
  <div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
          <tr>
            <th class="px-6 py-3 text-left">Name</th>
            <th class="px-6 py-3 text-left">Category</th>
            <th class="px-6 py-3 text-left">Price</th>
            <th class="px-6 py-3 text-left">Stock</th>
            <th class="px-6 py-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php if (empty($parts)): ?>
            <tr>
              <td colspan="5" class="px-6 py-8 text-center text-gray-500">No parts found.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($parts as $part): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium"><?= htmlspecialchars($part['name']) ?></td>
                <td class="px-6 py-4">
                  <span class="capitalize px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                    <?= htmlspecialchars($part['category']) ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-gray-800 font-semibold">$<?= number_format($part['price'], 2) ?></td>
                <td class="px-6 py-4"><?= $part['stock'] ?></td>
                <td class="px-6 py-4 space-x-2">
                  <a href="edit_part.php?id=<?= $part['id'] ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                    <i class="fas fa-edit"></i> Edit
                  </a>
                  <a href="delete_part.php?id=<?= $part['id'] ?>" class="text-red-600 hover:text-red-800 text-sm">
                    <i class="fas fa-trash"></i> Delete
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php include 'includes/admin_footer.php'; ?>