<?php
session_start();
include 'includes/config.php';

// Check if user is logged in and is an approved support agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'support' || $_SESSION['role_status'] !== 'approved') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['name']);

// Fetch ALL tickets from buyers and sellers (visible to all support agents)
$tickets = [];
try {
    $stmt = $pdo->prepare("
        SELECT t.id, t.subject, t.status, t.priority, u.name as user_name, u.role as user_role, t.created_at,
               -- ✅ Count unread replies FROM BUYER/SELLER (not support) PLUS 1 if ticket is open (unread)
               (SELECT COUNT(*) 
                FROM ticket_replies tr 
                WHERE tr.ticket_id = t.id 
                  AND tr.sender_role IN ('buyer', 'seller')
                  AND tr.is_read = FALSE
               ) + 
               CASE WHEN t.status = 'open' THEN 1 ELSE 0 END as unread_replies
        FROM tickets t
        JOIN users u ON t.user_id = u.id
        WHERE u.role IN ('buyer', 'seller')
        ORDER BY 
            CASE WHEN t.priority = 'urgent' THEN 1 WHEN t.priority = 'high' THEN 2 ELSE 3 END,
            t.created_at DESC
    ");
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Failed to fetch tickets: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tickets - Support Dashboard</title>

  <!-- ✅ Correct Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <style>
    .priority-urgent {
      @apply px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold;
    }
    .priority-high {
      @apply px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-bold;
    }
    .priority-medium {
      @apply px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold;
    }
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

  <?php include 'includes/support_header.php'; ?>

  <!-- Page Header -->
  <div class="py-12 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-6 text-center">
      <h1 class="text-4xl md:text-5xl font-bold mb-4">Support Tickets</h1>
      <p class="text-blue-100 max-w-2xl mx-auto text-lg">Manage customer and seller support requests.</p>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container mx-auto px-6 py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="p-6 border-b">
        <h2 class="text-xl font-bold text-gray-800">All Tickets</h2>
        <p class="text-gray-600 mt-1">View and respond to all buyer and seller tickets.</p>
      </div>
      
      <div class="p-6">
        <?php if (empty($tickets)): ?>
          <div class="text-center py-12">
            <i class="fas fa-ticket-alt text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-medium text-gray-500">No tickets yet</h3>
            <p class="text-gray-400 mt-2">Wait for users to submit tickets.</p>
          </div>
        <?php else: ?>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                <tr>
                  <th class="px-6 py-3 text-left">ID</th>
                  <th class="px-6 py-3 text-left">Subject</th>
                  <th class="px-6 py-3 text-left">User</th>
                  <th class="px-6 py-3 text-left">Role</th>
                  <th class="px-6 py-3 text-left">Priority</th>
                  <th class="px-6 py-3 text-left">Status</th>
                  <th class="px-6 py-3 text-left">Date</th>
                  <th class="px-6 py-3 text-left">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php foreach ($tickets as $ticket): ?>
                  <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">#<?= htmlspecialchars($ticket['id']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($ticket['subject']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($ticket['user_name']) ?></td>
                    <td class="px-6 py-4">
                      <span class="px-2 py-1 rounded-full text-xs font-medium
                        <?= $ticket['user_role'] === 'buyer' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' ?>
                      ">
                        <?= ucfirst($ticket['user_role']) ?>
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <span class="priority-<?= htmlspecialchars($ticket['priority']) ?>">
                        <?= ucfirst($ticket['priority']) ?>
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <span class="status-<?= htmlspecialchars($ticket['status']) ?>">
                        <?= str_replace('_', ' ', $ticket['status']) ?>
                      </span>
                    </td>
                    <td class="px-6 py-4"><?= date('M j, Y', strtotime($ticket['created_at'])) ?></td>
                    <td class="px-6 py-4">
                      <a href="view_ticket.php?id=<?= $ticket['id'] ?>" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                        <i class="fas fa-eye mr-1"></i> View
                        <?php if ($ticket['unread_replies'] > 0): ?>
                          <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full ml-1"><?= $ticket['unread_replies'] ?></span>
                        <?php endif; ?>
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
  </div>

  <?php include 'includes/support_footer.php'; ?>
</body>
</html>