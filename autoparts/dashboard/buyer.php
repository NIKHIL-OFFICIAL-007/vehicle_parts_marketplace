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
  <title>Buyer Dashboard</title>
  <style>
    body { font-family: Arial; background: #f0f0f0; text-align: center; padding: 50px; }
    .box { background: #fff; padding: 40px; border-radius: 10px; display: inline-block; box-shadow: 0 0 10px #ccc; }
    a.logout { color: white; background: #e74c3c; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
    a.logout:hover { background: #c0392b; }
  </style>
</head>
<body>
  <div class="box">
    <h1>Welcome, Buyer 👤</h1>
    <p>Hello <?= htmlspecialchars($_SESSION['full_name']) ?>! You are logged in as <strong>Buyer</strong>.</p>
    <a class="logout" href="logout.php">Logout</a>
  </div>
</body>
</html>
