<?php
session_start();
if(isset($_SESSION['admin'])){ header('Location: /INVENKAS/dashboard.php'); exit; }
header('Location: /INVENKAS/login.php');
?>
