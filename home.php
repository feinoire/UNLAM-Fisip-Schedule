<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Halaman Home</title>
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
    .home-box {
      position: relative;
      z-index: 1;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.5);
      width: 350px;
      text-align: center;
      color: #fff;
      animation: fadeIn 1s ease;
    }
    .home-box h1 {
      margin-bottom: 20px;
      font-size: 26px;
      color: #fff;
      letter-spacing: 1px;
    }
    .home-box a {
      display: block;
      margin: 12px 0;
      padding: 12px;
      border-radius: 8px;
      font-weight: bold;
      font-size: 15px;
      text-decoration: none;
      color: white;
      background: linear-gradient(135deg, #0d6efd, #6610f2);
      transition: 0.3s;
    }
    .home-box a:hover {
      background: linear-gradient(135deg, #6610f2, #0d6efd);
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="home-box">
    <h1>Selamat Datang</h1>
    <p>Pilih akses sesuai peran Anda</p>
    <a href="mahasiswa.php">Masuk sebagai Mahasiswa</a>
    <a href="login.php">Login sebagai Admin</a>
  </div>
</body>
</html>
