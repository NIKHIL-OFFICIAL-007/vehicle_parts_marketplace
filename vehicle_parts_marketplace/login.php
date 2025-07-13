<?php
include 'db.php';
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $full_name, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['full_name'] = $full_name;
            $_SESSION['role'] = $role;

            // Redirect based on role
            if ($role === 'admin') {
                header("Location: dashboard/admin.php");
            } elseif ($role === 'seller') {
                header("Location: dashboard/seller.php");
            } else {
                header("Location: dashboard/buyer.php");
            }
            exit;
        } else {
            $error = "❌ Invalid password.";
        }
    } else {
        $error = "❌ No account found with that email.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>AutoParts – Login</title>
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

    .login-modal {
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

    .login-form {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .form-control {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .form-control input {
      padding: 12px 14px;
      font-size: 14px;
      border: 1px solid #b2b2b2;
      border-radius: 6px;
      transition: border-color 0.2s;
    }

    .form-control input:focus {
      border-color: #2874f0;
      outline: none;
    }

    .login-btn {
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

    .login-btn:hover {
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
      .login-modal {
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

  <!-- Login Modal -->
  <div class="login-modal" role="dialog" aria-modal="true">
    <div class="modal-header">
      <button class="close-btn" onclick="window.history.back()">&times;</button>
      <h1>Welcome Back</h1>
    </div>
    <p class="subtext">Login to your AutoParts account</p>

    <?php if (!empty($error)) echo "<p style='color:red;text-align:center;'>$error</p>"; ?>

    <form class="login-form" method="POST" action="">
      <div class="form-control">
        <input type="email" name="email" placeholder="E‑mail" required />
      </div>
      <div class="form-control">
        <input type="password" name="password" placeholder="Password" required />
      </div>

      <button class="login-btn" type="submit">Login</button>
    </form>

    <p class="have-account">Don't have an account? <a href="signup.php">Sign up</a></p>
  </div>
</body>
</html>
