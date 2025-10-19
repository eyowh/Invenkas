<?php
session_start();
require __DIR__.'/config/db.php';
if(isset($_SESSION['admin'])){ header('Location: /INVENKAS/dashboard.php'); exit; }
$err = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u = trim($_POST['username']??'');
  $p = trim($_POST['password']??'');
  $stmt = $conn->prepare('SELECT id,username,password FROM users WHERE username=? LIMIT 1');
  $stmt->bind_param('s',$u);
  $stmt->execute();
  $res = $stmt->get_result();
  if($row = $res->fetch_assoc()){
    if(password_verify($p,$row['password'])){
      $_SESSION['admin'] = ['id'=>$row['id'],'username'=>$row['username']];
      header('Location: /INVENKAS/dashboard.php'); exit;
    } else { $err='Username atau password salah'; }
  } else { $err='Username atau password salah'; }
}
include __DIR__.'/includes/header.php';
?>
<div class="card" style="max-width:420px;margin:60px auto;">
  <h2>Masuk Admin</h2>
  <?php if($err): ?><div style="color:#ef4444;"><?= htmlspecialchars($err) ?></div><?php endif; ?>
  <form class="form" method="post">
    <input name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button class="btn" type="submit">Masuk</button>
  </form>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>
