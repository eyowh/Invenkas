<?php
// Sementara: hanya mulai sesi tanpa redirect agar halaman bisa diakses saat pengembangan
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
