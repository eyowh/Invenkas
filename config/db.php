<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'invenkas';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die('Koneksi gagal'); }
$conn->set_charset('utf8mb4');
?>
