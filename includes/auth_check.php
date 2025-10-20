<?php
// Sementara: hanya mulai sesi tanpa redirect agar halaman bisa diakses saat pengembangan
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin'])) {
  header('Location: /INVENKAS/login.php');
  exit;
}
?>
