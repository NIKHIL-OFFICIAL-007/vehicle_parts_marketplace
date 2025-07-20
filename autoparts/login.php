<?php
include 'db.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $full_name, $hashedPassword, $role);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['full_name'] = $full_name;
            $_SESSION['email'] = $email;
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
            $message = "❌ Incorrect password.";
        }
    } else {
        $message = "❌ User not found.";
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
      background-image: url('images/background.jpg');
      background-size: cover;
      background-position: center;
      position: relative;
    }

    .dim-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.3);
      z-index: 0;
    }

    .login-modal {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 360px;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
      padding: 32px 28px;
      display: flex;
      flex-direction: column;
      gap: 22px;
      animation: fadeIn 0.4s ease-out;
      z-index: 10;
      border: 1px solid rgba(255, 255, 255, 0.3);
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
      color: #fff;
    }

    .subtext {
      font-size: 14px;
      color: #ddd;
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

    .signup-link {
      text-align: center;
      font-size: 14px;
      color: #eee;
    }

    .signup-link a {
      color: #90cdf4;
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


  <div class="dim-overlay"></div>

  <div class="login-modal" role="dialog" aria-modal="true">
    <div class="modal-header">
      <button class="close-btn" onclick="window.history.back()">&times;</button>
      <h1>Welcome Back</h1>
    </div>
    <p class="subtext">Login to continue shopping with AutoParts</p>

    <?php if (!empty($message)) echo "<p style='color:#fff; text-align:center;'>$message</p>"; ?>

    <form class="login-form" method="POST" action="">
      <div class="form-control">
        <input type="email" name="email" placeholder="E‑mail" required />
      </div>
      <div class="form-control">
        <input type="password" name="password" placeholder="Password" required />
      </div>

      <button class="login-btn" type="submit">Login</button>
    </form>

    <p class="signup-link">New to AutoParts? <a href="signup.php">Create an account</a></p>
  </div>
</body>
</html>
