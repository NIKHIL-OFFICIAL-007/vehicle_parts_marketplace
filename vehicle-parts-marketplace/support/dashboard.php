<?php
session_start();
include 'includes/config.php';

// Check if user is logged in and is an approved support agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'support' || $_SESSION['role_status'] !== 'approved') {
    header("Location: ../login.php");
    exit();
}

$user_name = htmlspecialchars($_SESSION['name']);

// Fetch stats
try {
    // Total tickets assigned
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE assigned_to = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $total_tickets = (int)$stmt->fetchColumn();

    // Open tickets
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE assigned_to = ? AND status = 'open'");
    $stmt->execute([$_SESSION['user_id']]);
    $open_tickets = (int)$stmt->fetchColumn();

    // Resolved tickets
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE assigned_to = ? AND status = 'resolved'");
    $stmt->execute([$_SESSION['user_id']]);
    $resolved_tickets = (int)$stmt->fetchColumn();

    // Pending complaints
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM complaints WHERE status = 'pending'");
    $stmt->execute();
    $pending_complaints = (int)$stmt->fetchColumn();

} catch (Exception $e) {
    error_log("Support dashboard stats query failed: " . $e->getMessage());
    $total_tickets = $open_tickets = $resolved_tickets = $pending_complaints = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Support Dashboard - AutoParts Hub</title>

  <!-- ✅ Correct Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="bg-gray-50 text-gray-900">

  <?php include 'includes/support_header.php'; ?>

  <!-- Stats Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Tickets -->
    <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1">
      <div class="flex items-center">
        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 text-xl mr-4">
          <i class="fas fa-ticket-alt"></i>
        </div>
        <div>
          <div class="text-sm font-medium text-gray-500">Total Tickets</div>
          <div class="text-2xl font-bold text-gray-800"><?= number_format($total_tickets) ?></div>
        </div>
      </div>
    </div>

    <!-- Open Tickets -->
    <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1">
      <div class="flex items-center">
        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600 text-xl mr-4">
          <i class="fas fa-clock"></i>
        </div>
        <div>
          <div class="text-sm font-medium text-gray-500">Open Tickets</div>
          <div class="text-2xl font-bold text-gray-800"><?= number_format($open_tickets) ?></div>
        </div>
      </div>
    </div>

    <!-- Resolved Tickets -->
    <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1">
      <div class="flex items-center">
        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 text-xl mr-4">
          <i class="fas fa-check-circle"></i>
        </div>
        <div>
          <div class="text-sm font-medium text-gray-500">Resolved Tickets</div>
          <div class="text-2xl font-bold text-gray-800"><?= number_format($resolved_tickets) ?></div>
        </div>
      </div>
    </div>

    <!-- Complaints -->
    <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1">
      <div class="flex items-center">
        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center text-red-600 text-xl mr-4">
          <i class="fas fa-exclamation-circle"></i>
        </div>
        <div>
          <div class="text-sm font-medium text-gray-500">Pending Complaints</div>
          <div class="text-2xl font-bold text-gray-800"><?= number_format($pending_complaints) ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="bg-white rounded-xl shadow-md p-6 mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <a href="tickets.php" class="flex flex-col items-center p-4 bg-gray-50 hover:bg-blue-600 hover:text-white rounded-lg transition transform hover:-translate-y-1">
        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-blue-600 mb-3">
          <i class="fas fa-ticket-alt text-xl"></i>
        </div>
        <span class="font-medium">View Tickets</span>
      </a>

      <a href="complaints.php" class="flex flex-col items-center p-4 bg-gray-50 hover:bg-yellow-600 hover:text-white rounded-lg transition transform hover:-translate-y-1">
        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-yellow-600 mb-3">
          <i class="fas fa-exclamation-circle text-xl"></i>
        </div>
        <span class="font-medium">Handle Complaints</span>
      </a>

      <a href="knowledge_base.php" class="flex flex-col items-center p-4 bg-gray-50 hover:bg-green-600 hover:text-white rounded-lg transition transform hover:-translate-y-1">
        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-green-600 mb-3">
          <i class="fas fa-book text-xl"></i>
        </div>
        <span class="font-medium">Knowledge Base</span>
      </a>

      <a href="report.php" class="flex flex-col items-center p-4 bg-gray-50 hover:bg-gray-600 hover:text-white rounded-lg transition transform hover:-translate-y-1">
        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-gray-600 mb-3">
          <i class="fas fa-chart-bar text-xl"></i>
        </div>
        <span class="font-medium">Reports</span>
      </a>
    </div>
  </div>

  <!-- Recent Activity -->
  <div class="bg-white rounded-xl shadow-md p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Recent Activity</h2>
    <div class="space-y-4">
      <div class="flex items-start p-4 bg-gray-50 rounded-lg">
        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
          <i class="fas fa-ticket-alt text-blue-600"></i>
        </div>
        <div>
          <p class="font-medium text-gray-800">New ticket assigned: <span class="text-blue-600">#TICK-1001</span></p>
          <p class="text-sm text-gray-500">2 hours ago</p>
        </div>
      </div>
      
      <div class="flex items-start p-4 bg-gray-50 rounded-lg">
        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
          <i class="fas fa-check-circle text-green-600"></i>
        </div>
        <div>
          <p class="font-medium text-gray-800">Ticket resolved: <span class="text-green-600">#TICK-1000</span></p>
          <p class="text-sm text-gray-500">4 hours ago</p>
        </div>
      </div>
      
      <div class="flex items-start p-4 bg-gray-50 rounded-lg">
        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
          <i class="fas fa-exclamation-circle text-red-600"></i>
        </div>
        <div>
          <p class="font-medium text-gray-800">New complaint received: <span class="text-red-600">Refund Request</span></p>
          <p class="text-sm text-gray-500">Yesterday</p>
        </div>
      </div>
    </div>
  </div>

  <?php include 'includes/support_footer.php'; ?>