<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'seller') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Seller Dashboard</title>
  <style>
    body { font-family: Arial; background: #f9f9f9; text-align: center; padding: 50px; }
    .box { background: #fff; padding: 40px; border-radius: 10px; display: inline-block; box-shadow: 0 0 10px #ccc; }
    a.logout { color: white; background: #e67e22; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
    a.logout:hover { background: #d35400; }
  </style>
</head>
<body>
  <div class="box">
    <h1>Welcome, Seller 🧑‍💼</h1>
    <p>Hello <?= htmlspecialchars($_SESSION['full_name']) ?>! You are logged in as <strong>Seller</strong>.</p>
    <a class="logout" href="logout.php">Logout</a>
  </div>
</body>
</html>
