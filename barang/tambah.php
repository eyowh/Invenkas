<?php
require __DIR__.'/../includes/auth_check.php';
require __DIR__.'/../config/db.php';

function getNextKode(mysqli $conn): string {
  $sql = "SELECT MAX(CAST(SUBSTRING(kode_barang,5) AS UNSIGNED)) AS maxnum FROM inventaris WHERE kode_barang LIKE 'BRG-%'";
  $res = $conn->query($sql);
  $row = $res ? $res->fetch_assoc() : null;
  $next = (int)($row['maxnum'] ?? 0) + 1;
  return 'BRG-'.str_pad((string)$next, 4, '0', STR_PAD_LEFT);
}

if($_SERVER['REQUEST_METHOD']!=='POST'){
  header('Location: index.php');
  exit;
}

// Abaikan input kode dari klien, selalu generate di server
$kode = getNextKode($conn);
$nama = trim($_POST['nama_barang'] ?? '');
$kategori = trim($_POST['kategori'] ?? '');
$jumlah = (int)($_POST['jumlah'] ?? 0);
$kondisi = trim($_POST['kondisi'] ?? '');
$lokasi = trim($_POST['lokasi'] ?? '');

if($nama && $kategori && $jumlah>=0 && $kondisi && $lokasi){
  $stmt = $conn->prepare('INSERT INTO inventaris(kode_barang,nama_barang,kategori,jumlah,kondisi,lokasi) VALUES(?,?,?,?,?,?)');
  $stmt->bind_param('sssiss',$kode,$nama,$kategori,$jumlah,$kondisi,$lokasi);
  if($stmt->execute()){
    header('Location: index.php');
    exit;
  }
}

// Jika gagal, kembali ke index dengan pesan error sederhana
header('Location: index.php');
exit;
