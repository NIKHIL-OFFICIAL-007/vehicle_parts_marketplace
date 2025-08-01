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
  <title>Air Filters - AutoParts</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="navbar">
    <h1>AutoParts</h1>
    <a href="buyer.php" class="logout">← Back</a>
  </div>

  <div class="main">
    <div class="product-section">
      <div class="section-title">🌬️ Air Filters</div>
      <div class="products-grid">
        <div class="product-card">
          <img src="images/air_filter.jpg" alt="Air Filter">
          <h3>Engine Air Filter</h3>
          <div class="brand">Brand: Mann</div>
          <div class="rating">4.1 ★</div>
          <div class="price">₹299</div>
          <button>Add to Cart</button>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
