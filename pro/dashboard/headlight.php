<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'buyer') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Headlights - AutoParts</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="navbar">
    <h1>AutoParts</h1>
    <a href="buyer.php" class="logout">← Back</a>
  </div>

  <div class="main">
    <div class="product-section">
      <div class="section-title">💡 Headlights</div>
      <div class="products-grid">
        <div class="product-card">
          <img src="images/headlight.jpg" alt="Headlight">
          <h3>LED Headlight</h3>
          <div class="brand">Brand: Philips</div>
          <div class="rating">4.3 ★</div>
          <div class="price">₹1,500</div>
          <button>Add to Cart</button>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
