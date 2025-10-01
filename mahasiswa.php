<?php
session_start();

// Set session mahasiswa
$_SESSION['logged_in'] = true;
$_SESSION['role'] = 'mahasiswa';

// Arahkan ke index
header("Location: index.php");
exit;
