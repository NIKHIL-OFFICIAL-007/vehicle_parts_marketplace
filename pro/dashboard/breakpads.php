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
  <title>Brake Pads - AutoParts</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="navbar">
    <h1>AutoParts</h1>
    <a href="buyer.php" class="logout">← Back</a>
  </div>

  <div class="main">
    <div class="product-section">
      <div class="section-title">🛑 Brake Pads</div>
      <div class="products-grid">
        <div class="product-card">
          <img src="images/brake_pads.jpg" alt="Brake Pads">
          <h3>Bosch Brake Pads</h3>
          <div class="brand">Brand: Bosch</div>
          <div class="rating">4.2 ★</div>
          <div class="price">₹899</div>
          <button>Add to Cart</button>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
