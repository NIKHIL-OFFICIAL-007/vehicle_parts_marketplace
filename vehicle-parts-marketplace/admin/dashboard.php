<?php
session_start();
include 'includes/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$user_name = htmlspecialchars($_SESSION['name']);

// Fetch stats
try {
    // Total users
    $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    // Pending role applications
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role_status = 'pending' AND role_request IS NOT NULL");
    $stmt->execute();
    $pending_roles = (int)$stmt->fetchColumn();

    // Total approved sellers
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'seller' AND role_status = 'approved'");
    $stmt->execute();
    $total_sellers = (int)$stmt->fetchColumn();

    // Total approved support agents
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'support' AND role_status = 'approved'");
    $stmt->execute();
    $total_support = (int)$stmt->fetchColumn();

    // Total approved admins
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin' AND role_status = 'approved'");
    $stmt->execute();
    $total_admins = (int)$stmt->fetchColumn();

    // Ticket stats
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets");
    $stmt->execute();
    $total_tickets = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE status = 'open'");
    $stmt->execute();
    $open_tickets = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE status = 'in_progress'");
    $stmt->execute();
    $in_progress_tickets = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE status = 'closed'");
    $stmt->execute();
    $closed_tickets = (int)$stmt->fetchColumn();

} catch (Exception $e) {
    error_log("Dashboard stats query failed: " . $e->getMessage());
    $total_users = $pending_roles = $total_sellers = $total_support = $total_admins = 0;
    $total_tickets = $open_tickets = $in_progress_tickets = $closed_tickets = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard - AutoParts Hub</title>

  <!-- ✅ Correct Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <style>
    .status-open {
      @apply px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium;
    }
    .status-in_progress {
      @apply px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium;
    }
    .status-resolved {
      @apply px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium;
    }
    .status-closed {
      @apply px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-900">

  <?php include 'includes/admin_header.php'; ?>

  <!-- Stats Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <!-- Total Users -->
    <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1">
      <div class="flex items-center">
        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 text-xl mr-4">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="text-sm font-medium text-gray-500">Total Users</div>
          <div class="text-2xl font-bold text-gray-800"><?= number_format($total_users) ?></div>
        </div>
      </div>
    </div>

    <!-- Pending Roles -->
    <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1">
      <div class="flex items-center">
        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600 text-xl mr-4">
          <i class="fas fa-user-clock"></i>
        </div>
        <div>
          <div class="text-sm font-medium text-gray-500">Pending Roles</div>
          <div class="text-2xl font-bold text-gray-800"><?= number_format($pending_roles) ?></div>
        </div>
      </div>
    </div>

    <!-- Sellers -->
    <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1">
      <div class="flex items-center">
        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 text-xl mr-4">
          <i class="fas fa-store"></i>
        </div>
        <div>
          <div class="text-sm font-medium text-gray-500">Sellers</div>
          <div class="text-2xl font-bold text-gray-800"><?= number_format($total_sellers) ?></div>
        </div>
      </div>
    </div>

    <!-- Support Agents -->
    <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1">
      <div class="flex items-center">
        <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center text-teal-600 text-xl mr-4">
          <i class="fas fa-headset"></i>
        </div>
        <div>
          <div class="text-sm font-medium text-gray-500">Support Agents</div>
          <div class="text-2xl font-bold text-gray-800"><?= number_format($total_support) ?></div>
        </div>
      </div>
    </div>

    <!-- Admins -->
    <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1">
      <div class="flex items-center">
        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center text-red-600 text-xl mr-4">
          <i class="fas fa-user-shield"></i>
        </div>
        <div>
          <div class="text-sm font-medium text-gray-500">Admins</div>
          <div class="text-2xl font-bold text-gray-800"><?= number_format($total_admins) ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Ticket Stats -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-md">
      <h3 class="text-lg font-semibold text-gray-800 mb-2">Total Tickets</h3>
      <div class="text-3xl font-bold text-blue-600"><?= $total_tickets ?></div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md">
      <h3 class="text-lg font-semibold text-gray-800 mb-2">Open</h3>
      <div class="text-3xl font-bold text-yellow-600"><?= $open_tickets ?></div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md">
      <h3 class="text-lg font-semibold text-gray-800 mb-2">In Progress</h3>
      <div class="text-3xl font-bold text-purple-600"><?= $in_progress_tickets ?></div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md">
      <h3 class="text-lg font-semibold text-gray-800 mb-2">Closed</h3>
      <div class="text-3xl font-bold text-green-600"><?= $closed_tickets ?></div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="bg-white rounded-xl shadow-md p-6 mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <a href="manage_users.php" class="flex flex-col items-center p-4 bg-gray-50 hover:bg-blue-600 hover:text-white rounded-lg transition transform hover:-translate-y-1">
        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-blue-600 mb-3">
          <i class="fas fa-users text-xl"></i>
        </div>
        <span class="font-medium">Manage Users</span>
      </a>

      <a href="role_requests.php" class="flex flex-col items-center p-4 bg-gray-50 hover:bg-yellow-600 hover:text-white rounded-lg transition transform hover:-translate-y-1">
        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-yellow-600 mb-3">
          <i class="fas fa-user-shield text-xl"></i>
        </div>
        <span class="font-medium">Role Requests</span>
      </a>

      <a href="manage_parts.php" class="flex flex-col items-center p-4 bg-gray-50 hover:bg-green-600 hover:text-white rounded-lg transition transform hover:-translate-y-1">
        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-green-600 mb-3">
          <i class="fas fa-cogs text-xl"></i>
        </div>
        <span class="font-medium">Manage Parts</span>
      </a>

      <a href="settings.php" class="flex flex-col items-center p-4 bg-gray-50 hover:bg-gray-600 hover:text-white rounded-lg transition transform hover:-translate-y-1">
        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-gray-600 mb-3">
          <i class="fas fa-cog text-xl"></i>
        </div>
        <span class="font-medium">Settings</span>
      </a>
    </div>
  </div>

  <!-- Recent Tickets -->
  <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
    <div class="p-6 border-b">
      <h2 class="text-xl font-bold text-gray-800">Recent Tickets</h2>
      <p class="text-gray-600 mt-1">View and manage customer support requests.</p>
    </div>
    <div class="p-6">
      <?php
      try {
          $ticket_stmt = $pdo->prepare("
              SELECT t.id, t.subject, t.status, t.priority, u.name as user_name, t.created_at
              FROM tickets t
              JOIN users u ON t.user_id = u.id
              ORDER BY t.created_at DESC
              LIMIT 5
          ");
          $ticket_stmt->execute();
          $recent_tickets = $ticket_stmt->fetchAll(PDO::FETCH_ASSOC);
      } catch (Exception $e) {
          $recent_tickets = [];
      }
      ?>
      <?php if (empty($recent_tickets)): ?>
        <p class="text-gray-500 text-center py-6">No tickets yet.</p>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
              <tr>
                <th class="px-6 py-3 text-left">ID</th>
                <th class="px-6 py-3 text-left">Subject</th>
                <th class="px-6 py-3 text-left">User</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Date</th>
                <th class="px-6 py-3 text-left">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php foreach ($recent_tickets as $ticket): ?>
                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4 font-medium">#<?= htmlspecialchars($ticket['id']) ?></td>
                  <td class="px-6 py-4"><?= htmlspecialchars($ticket['subject']) ?></td>
                  <td class="px-6 py-4"><?= htmlspecialchars($ticket['user_name']) ?></td>
                  <td class="px-6 py-4">
                    <span class="status-<?= htmlspecialchars($ticket['status']) ?>">
                      <?= ucfirst(str_replace('_', ' ', $ticket['status'])) ?>
                    </span>
                  </td>
                  <td class="px-6 py-4"><?= date('M j, Y', strtotime($ticket['created_at'])) ?></td>
                  <td class="px-6 py-4">
                    <a href="view_ticket.php?id=<?= $ticket['id'] ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                      <i class="fas fa-eye mr-1"></i> View
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <?php include 'includes/admin_footer.php'; ?>
</body>
</html>