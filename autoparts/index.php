<?php
session_start();
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin': header("Location: dashboard/admin.php"); exit;
        case 'seller': header("Location: dashboard/seller.php"); exit;
        case 'buyer':
        default: header("Location: dashboard/buyer.php"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>AutoParts - Home</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="style.css" />

</head>
<body>

<header class="main-nav">
  <div class="logo">
    <a href="index.php"><img src="logo.png" alt="AutoParts Logo" /></a>
  </div>
  <div style="margin-left: auto; display: flex; align-items: center;">
    <div class="nav-links">
      <a href="#" class="nav-btn" id="openLogin">Login</a>
      <a href="#" class="nav-btn" id="openSignup">Sign Up</a> <!-- 🔁 Now opens modal -->
    </div>
    <div class="menu-toggle"><i class="fas fa-bars"></i></div>
  </div>
</header>

<div class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <span>Menu</span>
    <i class="fas fa-times" id="closeSidebar"></i>
  </div>
  <a href="#"><i class="fas fa-question-circle"></i> FAQ</a>
  <a href="#"><i class="fas fa-envelope"></i> Contact Us</a>
</div>

<div class="overlay" id="overlay"></div>

<!-- ✅ Quote Section -->
<div class="quote-container">
  <div class="quote-section">Log in to get access to buy and sell items</div>
</div>

<!-- ✅ Dim Overlay -->
<div class="dim-overlay" id="modalOverlay"></div>

<!-- ✅ Login Modal -->
<div class="modal" id="loginModal">
  <div class="modal-header">
    <button class="close-btn" id="closeLogin">&times;</button>
    <h1>Welcome Back</h1>
  </div>
  <p class="subtext">Login to continue shopping with AutoParts</p>
  <form class="form" method="POST" action="login.php">
    <div class="form-control">
      <input type="email" name="email" placeholder="E‑mail" required />
    </div>
    <div class="form-control">
      <input type="password" name="password" placeholder="Password" required />
    </div>
    <button class="btn" type="submit">Login</button>
  </form>
  <p class="form-footer">New to AutoParts? <a href="#" id="switchToSignup">Create an account</a></p>
</div>

<!-- ✅ Signup Modal -->
<div class="modal" id="signupModal">
  <div class="modal-header">
    <button class="close-btn" id="closeSignup">&times;</button>
    <h1>Create an<br>account</h1>
  </div>
  <p class="subtext">Join AutoParts today – it's free!</p>
  <form class="form" method="POST" action="signup.php">
    <div class="form-control">
      <input type="text" name="name" placeholder="Full Name" required />
    </div>
    <div class="form-control">
      <input type="email" name="email" placeholder="E‑mail" required />
    </div>
    <div class="form-control">
      <input type="password" name="password" placeholder="Password" required />
    </div>
    <div class="form-control">
      <input type="password" name="confirm" placeholder="Confirm Password" required />
    </div>
    <div class="form-control">
      <select name="role" required>
        <option value="" disabled selected>Select Role</option>
        <option value="buyer">Buyer</option>
        <option value="seller">Seller</option>
        <option value="admin">Admin</option>
      </select>
    </div>
    <button class="btn" type="submit">Sign Up</button>
  </form>
  <p class="form-footer">Already have an account? <a href="#" id="switchToLogin">Login</a></p>
</div>

<!-- ✅ Script -->
<script>
  const loginBtn = document.getElementById('openLogin');
  const signupBtn = document.getElementById('openSignup');
  const modalOverlay = document.getElementById('modalOverlay');
  const loginModal = document.getElementById('loginModal');
  const signupModal = document.getElementById('signupModal');

  loginBtn.onclick = () => {
    modalOverlay.style.display = 'block';
    loginModal.style.display = 'flex';
    signupModal.style.display = 'none';
  };

  signupBtn.onclick = () => {
    modalOverlay.style.display = 'block';
    signupModal.style.display = 'flex';
    loginModal.style.display = 'none';
  };

  document.getElementById('closeLogin').onclick = () => {
    modalOverlay.style.display = 'none';
    loginModal.style.display = 'none';
  };

  document.getElementById('closeSignup').onclick = () => {
    modalOverlay.style.display = 'none';
    signupModal.style.display = 'none';
  };

  document.getElementById('switchToSignup').onclick = () => {
    loginModal.style.display = 'none';
    signupModal.style.display = 'flex';
  };

  document.getElementById('switchToLogin').onclick = () => {
    signupModal.style.display = 'none';
    loginModal.style.display = 'flex';
  };

  // Sidebar
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');

  document.querySelector('.menu-toggle').onclick = () => {
    sidebar.classList.add('active');
    overlay.classList.add('active');
  };

  document.getElementById('closeSidebar').onclick = () => {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
  };

  overlay.onclick = () => {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
  };
</script>
</body>
</html>
