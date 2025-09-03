<?php
session_start();
include '../includes/config.php';

// Check if user is logged in and has support role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'support') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['name']);

// Fetch support stats
try {
    // Get resolved tickets count
    $stmt = $pdo->prepare("SELECT COUNT(*) as resolved_count FROM support_tickets WHERE assigned_to = ? AND status = 'resolved'");
    $stmt->execute([$user_id]);
    $resolved_data = $stmt->fetch();
    $resolved_count = $resolved_data ? $resolved_data['resolved_count'] : 0;

    // Get pending tickets count
    $stmt = $pdo->prepare("SELECT COUNT(*) as pending_count FROM support_tickets WHERE assigned_to = ? AND status = 'open'");
    $stmt->execute([$user_id]);
    $pending_data = $stmt->fetch();
    $pending_count = $pending_data ? $pending_data['pending_count'] : 0;

    // Get average rating
    $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating FROM support_tickets WHERE assigned_to = ? AND rating > 0");
    $stmt->execute([$user_id]);
    $rating_data = $stmt->fetch();
    $avg_rating = $rating_data && $rating_data['avg_rating'] ? round($rating_data['avg_rating'], 1) : 'N/A';

    // Get support since date
    $stmt = $pdo->prepare("SELECT created_at FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch();
    $support_since = $user_data ? new DateTime($user_data['created_at']) : new DateTime();
    $days_as_support = $support_since->diff(new DateTime())->days;

} catch (PDOException $e) {
    error_log("Support stats error: " . $e->getMessage());
    $resolved_count = 0;
    $pending_count = 0;
    $avg_rating = 'N/A';
    $days_as_support = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Dashboard - AutoParts Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        .stat-card { 
            transition: transform 0.3s ease, box-shadow 0.3s ease; 
        }
        .stat-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>

    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-teal-600 to-teal-800 text-white p-6">
                <h1 class="text-2xl md:text-3xl font-bold text-center">Support Team Member</h1>
                <p class="text-teal-100 text-center">Welcome, <?php echo $user_name; ?>! Your support account is active.</p>
            </div>

            <div class="p-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-8 text-center">Your Support Dashboard</h2>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                    <div class="stat-card bg-teal-50 p-6 rounded-lg text-center">
                        <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-check-circle text-teal-600"></i>
                        </div>
                        <div class="text-2xl font-bold text-teal-800"><?php echo $resolved_count; ?></div>
                        <div class="text-sm text-gray-600">Tickets Resolved</div>
                    </div>

                    <div class="stat-card bg-orange-50 p-6 rounded-lg text-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-clock text-orange-600"></i>
                        </div>
                        <div class="text-2xl font-bold text-orange-800"><?php echo $pending_count; ?></div>
                        <div class="text-sm text-gray-600">Pending Tickets</div>
                    </div>

                    <div class="stat-card bg-yellow-50 p-6 rounded-lg text-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-star text-yellow-600"></i>
                        </div>
                        <div class="text-2xl font-bold text-yellow-800"><?php echo $avg_rating; ?></div>
                        <div class="text-sm text-gray-600">Average Rating</div>
                    </div>

                    <div class="stat-card bg-purple-50 p-6 rounded-lg text-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-calendar-alt text-purple-600"></i>
                        </div>
                        <div class="text-2xl font-bold text-purple-800"><?php echo $days_as_support; ?></div>
                        <div class="text-sm text-gray-600">Days as Support</div>
                    </div>
                </div>

                <!-- Support Tools -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                    <div class="bg-teal-50 p-6 rounded-lg border border-teal-100">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-ticket-alt text-teal-600"></i>
                            </div>
                            <h3 class="font-semibold text-lg">Manage Tickets</h3>
                        </div>
                        <p class="text-gray-600 mb-4">View and respond to support tickets from users needing assistance.</p>
                        <a href="support/tickets.php" class="inline-block px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">
                            View Tickets
                        </a>
                    </div>

                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-100">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-book text-blue-600"></i>
                            </div>
                            <h3 class="font-semibold text-lg">Knowledge Base</h3>
                        </div>
                        <p class="text-gray-600 mb-4">Access our knowledge base for answers to common questions and issues.</p>
                        <a href="support/knowledge_base.php" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Access Resources
                        </a>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="font-semibold text-lg mb-4 text-center">Support Resources</h3>
                    <div class="flex flex-col md:flex-row justify-center gap-6">
                        <a href="support_guide.php" class="flex items-center px-4 py-2 border border-teal-600 text-teal-600 rounded-lg hover:bg-teal-600 hover:text-white transition">
                            <i class="fas fa-book mr-2"></i> Support Guide
                        </a>
                        <a href="support/faq.php" class="flex items-center px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition">
                            <i class="fas fa-question-circle mr-2"></i> Internal FAQ
                        </a>
                        <a href="support/team.php" class="flex items-center px-4 py-2 border border-gray-600 text-gray-600 rounded-lg hover:bg-gray-600 hover:text-white transition">
                            <i class="fas fa-users mr-2"></i> Team Resources
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>