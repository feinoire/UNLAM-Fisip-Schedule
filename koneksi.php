<?php
$host = "sql209.infinityfree.com";
$user = "if0_40023341";
$pass = "BilalHengkuy";
$db   = "if0_40023341_db_jadwal";

$koneksi = new mysqli($host, $user, $pass, $db);

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>