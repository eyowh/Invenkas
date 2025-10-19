<?php
require __DIR__.'/../includes/auth_check.php';
require __DIR__.'/../config/db.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){ header('Location: index.php'); exit; }
$id_barang=(int)$_POST['id_barang'];
$nama=trim($_POST['nama_peminjam']);
$qty=(int)$_POST['jumlah_pinjam'];
$conn->begin_transaction();
try{
  $st=$conn->prepare('SELECT jumlah FROM inventaris WHERE id_barang=? FOR UPDATE');
  $st->bind_param('i',$id_barang);
  $st->execute();
  $stok=$st->get_result()->fetch_assoc();
  if(!$stok){ throw new Exception('Barang tidak ditemukan'); }
  if($qty<=0 || $qty>$stok['jumlah']){ throw new Exception('Jumlah pinjam melebihi stok (maks '.$stok['jumlah'].')'); }
  $st2=$conn->prepare('UPDATE inventaris SET jumlah=jumlah-? WHERE id_barang=?');
  $st2->bind_param('ii',$qty,$id_barang);
  $st2->execute();
  $st3=$conn->prepare("INSERT INTO peminjaman(id_barang,nama_peminjam,jumlah_pinjam,status) VALUES(?,?,?,'Dipinjam')");
  $st3->bind_param('isi',$id_barang,$nama,$qty);
  $st3->execute();
  $conn->commit();
  header('Location: index.php');
}catch(Exception $e){
  $conn->rollback();
  $msg=urlencode($e->getMessage());
  header('Location: index.php?err='.$msg);
}
?>
