<?php
include 'db.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $message = "❌ Passwords do not match.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed, $role);

        if ($stmt->execute()) {
            $message = "✅ Signup successful! <a href='login.php'>Login now</a>";
        } else {
            $message = "❌ Signup failed: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>AutoParts – Sign Up</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      height: 100vh;
      overflow: hidden;
      background: #f1f3f6;
    }

    .background-iframe {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: none;
      z-index: -3;
    }

    .dim-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.3);
      z-index: -2;
    }

    .signup-modal {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 360px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
      padding: 32px 28px;
      display: flex;
      flex-direction: column;
      gap: 22px;
      animation: fadeIn 0.4s ease-out;
      z-index: 10;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translate(-50%, -60%);
      }
      to {
        opacity: 1;
        transform: translate(-50%, -50%);
      }
    }

    .modal-header {
      display: flex;
      justify-content: center;
      position: relative;
    }

    .close-btn {
      position: absolute;
      right: -6px;
      top: -6px;
      background: #fff;
      border: none;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      font-size: 18px;
      cursor: pointer;
      box-shadow: 0 0 6px rgba(0, 0, 0, 0.12);
    }

    .close-btn:hover {
      background: #f5f5f5;
    }

    .modal-header h1 {
      font-size: 24px;
      font-weight: 700;
      text-align: center;
      color: #1b1b1b;
    }

    .subtext {
      font-size: 14px;
      color: #777;
      text-align: center;
    }

    .signup-form {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .form-control {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .form-control input,
    .form-control select {
      padding: 12px 14px;
      font-size: 14px;
      border: 1px solid #b2b2b2;
      border-radius: 6px;
      transition: border-color 0.2s;
    }

    .form-control input:focus,
    .form-control select:focus {
      border-color: #2874f0;
      outline: none;
    }

    .signup-btn {
      background: #20c261;
      color: #fff;
      font-weight: 600;
      border: none;
      padding: 12px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 15px;
      transition: background 0.2s;
    }

    .signup-btn:hover {
      background: #199e4f;
    }

    .have-account {
      text-align: center;
      font-size: 14px;
    }

    .have-account a {
      color: #2874f0;
      text-decoration: none;
      font-weight: 600;
    }

    @media (max-width: 420px) {
      .signup-modal {
        width: 92%;
        padding: 28px 22px;
      }
    }
  </style>
</head>
<body>

  <!-- Background -->
  <iframe class="background-iframe" src="index.html" aria-hidden="true"></iframe>
  <div class="dim-overlay"></div>

  <!-- Signup Modal -->
  <div class="signup-modal" role="dialog" aria-modal="true">
    <div class="modal-header">
      <button class="close-btn" onclick="window.history.back()">&times;</button>
      <h1>Create an<br>account</h1>
    </div>
    <p class="subtext">Join AutoParts today – it's free!</p>

    <?php if (!empty($message)) echo "<p style='color:red; text-align:center;'>$message</p>"; ?>

    <form class="signup-form" method="POST" action="">
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

      <button class="signup-btn" type="submit">Sign Up</button>
    </form>

    <p class="have-account">Already have an account? <a href="login.php">Login</a></p>
  </div>
</body>
</html>
