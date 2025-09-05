<?php
session_start();

// ✅ Include config.php to get $pdo and getPDO()
include 'includes/config.php';

$logged_in = isset($_SESSION['user_id']);
$user_name = $logged_in ? htmlspecialchars($_SESSION['name']) : '';

// Get single role (buyer by default)
$role = $logged_in && isset($_SESSION['role']) ? $_SESSION['role'] : 'buyer';
$role_status = $logged_in && isset($_SESSION['role_status']) ? $_SESSION['role_status'] : 'approved';

// Fetch unread notification count
$unread_count = 0;
if ($logged_in) {
    try {
        $pdo = getPDO(); // ← Now works!
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
  <title>AutoParts Hub - Online Vehicle Parts Marketplace</title>

  <!-- ✅ Fixed: Correct Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <!-- Custom Tailwind Config -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            poppins: ['Poppins', 'sans-serif'],
          },
          colors: {
            primary: '#2563eb',
            secondary: '#1e293b',
            accent: '#f97316',
            support: '#0d9488',
            seller: '#16a34a',
            admin: '#dc2626',
            light: '#f8fafc',
            dark: '#0f172a'
          }
        }
      }
    }
  </script>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8fafc;
      scroll-behavior: smooth;
    }
    .hero-bg {
      background: linear-gradient(135deg, rgba(29, 78, 216, 0.85), rgba(15, 23, 42, 0.9)),
                  url('https://images.unsplash.com/photo-1580273916550-e323be2ae537?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') no-repeat center center;
      background-size: cover;
    }
    .btn-primary {
      background: linear-gradient(135deg, #2563eb, #1d4ed8);
      color: white;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(29, 78, 216, 0.3);
    }
    .btn-outline {
      border: 2px solid white;
      color: white;
      transition: all 0.3s ease;
    }
    .btn-outline:hover {
      background: white;
      color: #1d4ed8;
    }
    .role-card {
      transition: all 0.3s ease;
    }
    .role-card:hover {
      transform: translateY(-5px);
    }
    .feature-icon {
      transition: transform 0.3s ease;
    }
    .feature-card:hover .feature-icon {
      transform: scale(1.1);
    }
  </style>
</head>
<body class="bg-light text-dark">

  <!-- Include Header -->
  <?php include 'includes/header.php'; ?>

  <!-- Hero Section -->
  <section id="home" class="hero-bg text-white py-32 px-6 text-center">
    <div class="container mx-auto">
      <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-6 animate-fade-in">
        Find the Right <span class="text-accent">Vehicle Part</span> in Minutes
      </h1>
      <p class="text-lg md:text-xl mb-10 max-w-3xl mx-auto opacity-90">
        A trusted online marketplace connecting buyers and sellers of auto parts nationwide. 
        Fast, secure, and transparent — just like modern e-commerce should be.
      </p>
      <div class="flex flex-col sm:flex-row justify-center gap-5 mt-8">
        <!-- Browse Parts - Available to ALL logged-in users -->
        <?php if ($logged_in): ?>
          <a href="buyer/browse_parts.php" class="btn-primary px-8 py-4 text-lg font-semibold rounded-xl shadow-lg hover:shadow-xl flex items-center justify-center">
            <i class="fas fa-search mr-2"></i> Browse Parts
          </a>
        <?php else: ?>
          <a href="#" onclick="showLoginAlert(); return false;" class="btn-primary px-8 py-4 text-lg font-semibold rounded-xl shadow-lg hover:shadow-xl cursor-pointer flex items-center justify-center">
            <i class="fas fa-search mr-2"></i> Browse Parts
          </a>
        <?php endif; ?>

        <!-- Sell Your Parts -->
        <?php if ($logged_in): ?>
          <?php if ($role === 'seller' && $role_status === 'approved'): ?>
            <a href="seller/dashboard.php" class="btn-outline px-8 py-4 text-lg font-semibold rounded-xl bg-white bg-opacity-20 backdrop-blur-sm hover:bg-opacity-30 flex items-center justify-center">
              <i class="fas fa-store mr-2"></i> Go to Seller Dashboard
            </a>
          <?php else: ?>
            <button onclick="showRoleAlert('You need to be an approved seller to sell parts.')" 
                    class="btn-outline px-8 py-4 text-lg font-semibold rounded-xl bg-white bg-opacity-20 backdrop-blur-sm hover:bg-opacity-30 flex items-center justify-center cursor-pointer">
              <i class="fas fa-store mr-2"></i> Sell Your Parts
            </button>
          <?php endif; ?>
        <?php else: ?>
          <button onclick="showLoginAlert()" 
                  class="btn-outline px-8 py-4 text-lg font-semibold rounded-xl bg-white bg-opacity-20 backdrop-blur-sm hover:bg-opacity-30 flex items-center justify-center cursor-pointer">
            <i class="fas fa-store mr-2"></i> Sell Your Parts
          </button>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section id="features" class="py-24 bg-white">
    <div class="container mx-auto px-6">
      <div class="text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-bold text-secondary">Why AutoParts Hub?</h2>
        <p class="text-gray-600 mt-4 max-w-2xl mx-auto">
          We solve the real-world problems of vehicle owners, mechanics, and part dealers with a powerful, digital-first marketplace.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        <?php foreach ([
          ['fa-globe-americas', 'Nationwide Access', 'No more local shortages. Access thousands of parts from sellers across the country.', 'primary'],
          ['fa-shield-alt', 'Verified Sellers', 'Every seller is reviewed and rated. Shop with confidence knowing quality is guaranteed.', 'green-600'],
          ['fa-tachometer-alt', 'Real-Time Tracking', 'Track your order from warehouse to doorstep with live status updates.', 'purple-600'],
          ['fa-comment-dots', '24/7 Support', 'Our support agents are ready to help with complaints, returns, and technical issues.', 'orange-600'],
          ['fa-tags', 'Transparent Pricing', 'Compare prices, read reviews, and make informed decisions — no hidden costs.', 'red-600'],
          ['fa-laptop', 'Easy Inventory Management', 'Sellers can manage stock, update listings, and track sales from a single dashboard.', 'indigo-600']
        ] as $feature): ?>
          <div class="bg-gray-50 p-8 rounded-2xl shadow-md hover:shadow-xl transition feature-card">
            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mb-5 feature-icon">
              <i class="fas <?= $feature[0] ?> text-2xl text-<?= $feature[3] ?>"></i>
            </div>
            <h3 class="text-2xl font-semibold mb-3"><?= $feature[1] ?></h3>
            <p class="text-gray-600"><?= $feature[2] ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- How It Works -->
  <section id="how-it-works" class="py-24 bg-gray-50">
    <div class="container mx-auto px-6 text-center">
      <h2 class="text-3xl md:text-4xl font-bold text-secondary mb-16">How It Works</h2>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <?php foreach (['Create Account', 'Browse or List', 'Order & Pay', 'Track & Review'] as $i => $title): ?>
          <div class="relative">
            <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4 z-10 relative">
              <?= $i+1 ?>
            </div>
            <?php if ($i < 3): ?>
              <div class="hidden md:block absolute top-8 left-1/2 w-full h-0.5 bg-primary transform translate-x-8 -translate-y-4"></div>
            <?php endif; ?>
            <h3 class="text-xl font-semibold mt-4"><?= $title ?></h3>
            <p class="text-gray-600 mt-2"><?= [
              'Register as a buyer, seller, or support agent in seconds.',
              'Buyers search parts. Sellers upload inventory with images and specs.',
              'Add to cart, checkout securely, and get order confirmation.',
              'Track delivery and leave feedback to help others.'
            ][$i] ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Role-Based Apply Section -->
  <section id="role-apply" class="py-20 bg-secondary text-white">
    <div class="container mx-auto px-6">
      <h2 class="text-3xl font-bold text-center mb-12">Apply For Roles</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ([
          ['buyer', 'fa-user', 'Buyer', 'Find parts fast. Track orders. Leave reviews.', 'buyer/dashboard.php'],
          ['seller', 'fa-store', 'Seller', 'List parts, manage inventory, grow your business.', 'apply_role/apply_seller.php'],
          ['support', 'fa-headset', 'Support', 'Handle complaints and assist users 24/7.', 'apply_role/apply_support.php'],
          ['admin', 'fa-user-shield', 'Admin', 'Manage users, categories, and platform operations.', 'apply_role/apply_admin.php']
        ] as $r): ?>
          <a href="<?= $logged_in ? $r[4] : '#' ?>" 
             onclick="<?= $logged_in ? '' : "showLoginAlert(); return false;" ?>" 
             class="p-8 bg-opacity-80 hover:bg-opacity-100 transition text-center shadow-lg rounded-xl role-card
                    <?= $r[0] === 'buyer' ? 'bg-blue-800 hover:bg-blue-900' :
                       ($r[0] === 'seller' ? 'bg-green-700 hover:bg-green-800' :
                       ($r[0] === 'support' ? 'bg-teal-600 hover:bg-teal-700' : 'bg-red-700 hover:bg-red-800')) ?>">
            <i class="fas <?= $r[1] ?> text-5xl mb-4"></i>
            <h3 class="text-2xl font-bold"><?= $r[2] ?></h3>
            <p class="mt-2 opacity-90"><?= $r[3] ?></p>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer id="contact" class="bg-gray-900 text-gray-300 py-16">
    <div class="container mx-auto px-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        <div>
          <div class="flex items-center space-x-3 mb-4">
            <i class="fas fa-tools text-2xl text-primary"></i>
            <h3 class="text-xl font-bold text-white">AutoParts Hub</h3>
          </div>
          <p class="mb-4">A modern, secure, and efficient online marketplace for buying and selling vehicle parts.</p>
          <div class="flex space-x-4">
            <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-facebook"></i></a>
            <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-linkedin"></i></a>
          </div>
        </div>

        <div>
          <h4 class="text-lg font-semibold text-white mb-4">Quick Links</h4>
          <ul class="space-y-2">
            <li><a href="#home" class="hover:text-white transition">Home</a></li>
            <li><a href="#features" class="hover:text-white transition">Features</a></li>
            <li><a href="#how-it-works" class="hover:text-white transition">How It Works</a></li>
            <li><a href="register.php" class="hover:text-white transition">Register</a></li>
          </ul>
        </div>

        <div>
          <h4 class="text-lg font-semibold text-white mb-4">Contact Us</h4>
          <ul class="space-y-2 text-sm">
            <li class="flex items-start space-x-2"><i class="fas fa-envelope mt-1"></i><span>support@autopartshub.com</span></li>
            <li class="flex items-start space-x-2"><i class="fas fa-phone mt-1"></i><span>+1 (555) 123-4567</span></li>
            <li class="flex items-start space-x-2"><i class="fas fa-map-marker-alt mt-1"></i><span>123 Auto Lane, Tech City, TC 10101</span></li>
          </ul>
        </div>
      </div>

      <hr class="border-gray-800 my-8" />
      <p class="text-center text-sm">
        &copy; <?= date("Y") ?> AutoParts Hub. All rights reserved.
      </p>
    </div>
  </footer>

  <!-- Login Alert -->
  <div id="loginAlert" class="fixed top-4 right-4 hidden p-4 bg-red-500 text-white rounded-lg shadow-lg max-w-xs z-50 transition-opacity duration-300">
    <div class="flex items-center">
      <i class="fas fa-exclamation-circle mr-3"></i>
      <span>Please login first.</span>
      <button class="ml-4" onclick="hideLoginAlert()">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>

  <!-- Role Alert -->
  <div id="roleAlert" class="fixed top-4 right-4 hidden p-4 bg-yellow-500 text-white rounded-lg shadow-lg max-w-xs z-50 transition-opacity duration-300">
    <div class="flex items-center">
      <i class="fas fa-exclamation-triangle mr-3"></i>
      <span id="roleAlertMsg">You need to be approved for this role.</span>
      <button class="ml-4" onclick="hideRoleAlert()">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>

  <script>
    function showLoginAlert() {
      document.getElementById('loginAlert').classList.remove('hidden');
      setTimeout(hideLoginAlert, 5000);
    }
    function hideLoginAlert() {
      document.getElementById('loginAlert')?.classList.add('hidden');
    }

    function showRoleAlert(message) {
      document.getElementById('roleAlertMsg').textContent = message;
      document.getElementById('roleAlert').classList.remove('hidden');
      setTimeout(hideRoleAlert, 6000);
    }
    function hideRoleAlert() {
      document.getElementById('roleAlert')?.classList.add('hidden');
    }
  </script>
</body>
</html>