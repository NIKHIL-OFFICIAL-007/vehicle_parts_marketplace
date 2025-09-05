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

// Fetch tickets assigned to this support agent
$tickets = [];
try {
    $stmt = $pdo->prepare("
        SELECT t.id, t.subject, t.status, u.name as user_name, t.created_at
        FROM tickets t
        JOIN users u ON t.user_id = u.id
        WHERE t.assigned_to = ?
        ORDER BY t.created_at DESC
    ");
    $stmt->execute([$user_id]);
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
</head>
<body class="bg-gray-50 text-gray-900">

  <?php include 'includes/support_header.php'; ?>

  <!-- Page Header -->
  <div class="py-12 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-6 text-center">
      <h1 class="text-4xl md:text-5xl font-bold mb-4">Support Tickets</h1>
      <p class="text-blue-100 max-w-2xl mx-auto text-lg">Manage customer support requests.</p>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container mx-auto px-6 py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="p-6 border-b">
        <h2 class="text-xl font-bold text-gray-800">Your Tickets</h2>
        <p class="text-gray-600 mt-1">View and manage tickets assigned to you.</p>
      </div>
      
      <div class="p-6">
        <?php if (empty($tickets)): ?>
          <div class="text-center py-12">
            <i class="fas fa-ticket-alt text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-medium text-gray-500">No tickets assigned</h3>
            <p class="text-gray-400 mt-2">Wait for new tickets to be assigned to you.</p>
          </div>
        <?php else: ?>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                <tr>
                  <th class="px-6 py-3 text-left">Ticket ID</th>
                  <th class="px-6 py-3 text-left">Subject</th>
                  <th class="px-6 py-3 text-left">User</th>
                  <th class="px-6 py-3 text-left">Status</th>
                  <th class="px-6 py-3 text-left">Date</th>
                  <th class="px-6 py-3 text-left">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php foreach ($tickets as $ticket): ?>
                  <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium"><?= htmlspecialchars($ticket['id']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($ticket['subject']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($ticket['user_name']) ?></td>
                    <td class="px-6 py-4">
                      <span class="capitalize px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                        <?= htmlspecialchars($ticket['status']) ?>
                      </span>
                    </td>
                    <td class="px-6 py-4"><?= date('M j, Y', strtotime($ticket['created_at'])) ?></td>
                    <td class="px-6 py-4 space-x-2">
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
  </div>

  <?php include 'includes/support_footer.php'; ?>
</body>
</html>