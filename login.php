<?php
session_start();

// Jika sudah login, langsung ke index (admin page)
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    if ($username === "unlam" && $password === "fisip") {
        $_SESSION['logged_in'] = true;
        $_SESSION['role'] = 'admin';
        header("Location: index.php"); // langsung ke halaman admin
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Admin</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Arial, sans-serif;
      background: url('fisip2.jpg') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
    }
    body::before {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.6);
      z-index: 0;
    }
    .login-box {
      position: relative;
      z-index: 1;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.5);
      width: 320px;
      text-align: center;
      color: #fff;
      animation: fadeIn 1s ease;
    }
    .login-box h2 {
      margin-bottom: 20px;
      font-size: 24px;
      color: #fff;
    }
    .login-box input,
    .login-box button {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 8px;
      font-size: 14px;
      box-sizing: border-box;
    }
    .login-box input[type="text"],
    .login-box input[type="password"] {
      border: none;
      outline: none;
      background: rgba(255, 255, 255, 0.9);
    }
    .login-box input:focus {
      box-shadow: 0 0 8px #0d6efd;
      border: 1px solid #0d6efd;
    }
    .password-wrapper {
      position: relative;
      width: 100%;
    }
    .password-wrapper input {
      padding-right: 40px;
    }
    .toggle-password {
      position: absolute;
      top: 50%;
      right: 12px;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 16px;
      color: #333;
      user-select: none;
    }
    .login-box button {
      border: none;
      font-weight: bold;
      background: linear-gradient(135deg, #0d6efd, #6610f2);
      color: white;
      cursor: pointer;
      font-size: 15px;
      transition: 0.3s;
    }
    .login-box button:hover {
      background: linear-gradient(135deg, #6610f2, #0d6efd);
    }
    .error {
      color: #ff6b6b;
      font-size: 13px;
      margin-top: 10px;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Login Admin</h2>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <div class="password-wrapper">
        <input type="password" id="password" name="password" placeholder="Password" required>
        <span class="toggle-password" onclick="togglePassword()">👁️</span>
      </div>
      <button type="submit">Masuk</button>
    </form>
    <?php if($error): ?>
      <p class="error"><?= $error ?></p>
    <?php endif; ?>
    <p><a href="home.php" style="color:#fff;">⬅ Kembali ke Home</a></p>
  </div>
  <script>
    function togglePassword() {
      const passwordField = document.getElementById("password");
      const toggleIcon = document.querySelector(".toggle-password");
      if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.textContent = "🙈";
      } else {
        passwordField.type = "password";
        toggleIcon.textContent = "👁️";
      }
    }
  </script>
</body>
</html>
