<?php
require __DIR__.'/../includes/auth_check.php';
require __DIR__.'/../config/db.php';
$barang=$conn->query('SELECT id_barang,kode_barang,nama_barang,jumlah FROM inventaris ORDER BY nama_barang');

// Filters
$q = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$id_barang_filter = (int)($_GET['id_barang'] ?? 0);
$from = trim($_GET['from'] ?? '');
$to = trim($_GET['to'] ?? '');

$where = [];
if($q !== ''){
  $safe = '%'.$conn->real_escape_string($q).'%';
  $where[] = "(p.nama_peminjam LIKE '$safe' OR i.nama_barang LIKE '$safe' OR i.kode_barang LIKE '$safe')";
}
if($status === 'Dipinjam' || $status === 'Dikembalikan'){
  $where[] = "p.status='".$conn->real_escape_string($status)."'";
}
if($id_barang_filter>0){
  $where[] = 'p.id_barang='.(int)$id_barang_filter;
}
if($from !== ''){ $where[] = "DATE(p.tanggal_pinjam) >= '".$conn->real_escape_string($from)."'"; }
if($to !== ''){ $where[] = "DATE(p.tanggal_pinjam) <= '".$conn->real_escape_string($to)."'"; }

$sqlTrans = 'SELECT p.*, i.nama_barang, i.kode_barang FROM peminjaman p JOIN inventaris i ON i.id_barang=p.id_barang';
if($where){ $sqlTrans .= ' WHERE '.implode(' AND ',$where); }
$sqlTrans .= ' ORDER BY p.id_pinjam DESC';
$trans=$conn->query($sqlTrans);
include __DIR__.'/../includes/header.php';
?>
<h2>Peminjaman Barang</h2>
<div class="actions">
  <button id="openFilter" class="btn secondary" type="button">Filter</button>
  <button id="openPinjam" class="btn" type="button">Pinjam Barang</button>
</div>
<?php if(!empty($_GET['err'])): ?>
  <div style="color:#ef4444; margin-bottom:10px;">&nbsp;<?= htmlspecialchars($_GET['err']) ?></div>
<?php endif; ?>

<!-- Modal Filter -->
<div id="modalFilter" class="modal-overlay" aria-hidden="true">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="filterTitle">
    <header>
      <h3 id="filterTitle" style="margin:0;">Filter Peminjaman</h3>
      <button type="button" class="btn secondary" data-close="modalFilter">Tutup</button>
    </header>
    <form class="form compact" method="get">
      <div class="filters">
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari (kode/nama/peminjam)">
        <select name="status">
          <option value="">Semua Status</option>
          <option value="Dipinjam" <?= $status==='Dipinjam'?'selected':'' ?>>Dipinjam</option>
          <option value="Dikembalikan" <?= $status==='Dikembalikan'?'selected':'' ?>>Dikembalikan</option>
        </select>
        <select name="id_barang">
          <option value="0">Semua Barang</option>
          <?php
            $barang2=$conn->query('SELECT id_barang,kode_barang,nama_barang FROM inventaris ORDER BY nama_barang');
            while($b2=$barang2->fetch_assoc()):
          ?>
            <option value="<?= (int)$b2['id_barang'] ?>" <?= $id_barang_filter==(int)$b2['id_barang']?'selected':'' ?>>[<?= htmlspecialchars($b2['kode_barang']) ?>] <?= htmlspecialchars($b2['nama_barang']) ?></option>
          <?php endwhile; ?>
        </select>
        <input type="date" name="from" value="<?= htmlspecialchars($from) ?>">
        <input type="date" name="to" value="<?= htmlspecialchars($to) ?>">
      </div>
      <div class="modal-actions">
        <a class="btn secondary" href="index.php">Reset</a>
        <button class="btn" type="submit">Terapkan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Pinjam -->
<div id="modalPinjam" class="modal-overlay" aria-hidden="true">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="pinjamTitle">
    <header>
      <h3 id="pinjamTitle" style="margin:0;">Pinjam Barang</h3>
      <button type="button" class="btn secondary" data-close="modalPinjam">Tutup</button>
    </header>
    <form class="form compact" method="post" action="proses_pinjam.php">
      <div class="inline-grid">
        <select name="id_barang" required>
          <option value="">Pilih Barang</option>
          <?php while($b=$barang->fetch_assoc()): ?>
            <option value="<?= $b['id_barang'] ?>" data-stok="<?= (int)$b['jumlah'] ?>">[<?= htmlspecialchars($b['kode_barang']) ?>] <?= htmlspecialchars($b['nama_barang']) ?> - Stok: <?= (int)$b['jumlah'] ?></option>
          <?php endwhile; ?>
        </select>
        <input name="nama_peminjam" placeholder="Nama Peminjam" required>
        <input type="number" name="jumlah_pinjam" placeholder="Jumlah Pinjam" min="1" required>
        <button class="btn" type="submit">Simpan</button>
      </div>
      <small id="stokHint" style="color:#6b7280;"></small>
    </form>
  </div>
</div>

<div class="table-responsive">
<table class="table">
  <thead>
    <tr>
      <th>Kode</th><th>Nama Barang</th><th>Peminjam</th><th>Jumlah</th><th>Tanggal</th><th>Status</th><th class="no-print">Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php while($t=$trans->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($t['kode_barang']) ?></td>
      <td><?= htmlspecialchars($t['nama_barang']) ?></td>
      <td><?= htmlspecialchars($t['nama_peminjam']) ?></td>
      <td><?= (int)$t['jumlah_pinjam'] ?></td>
      <td><?= htmlspecialchars($t['tanggal_pinjam']) ?></td>
      <td><?= htmlspecialchars($t['status']) ?></td>
      <td class="no-print">
        <?php if($t['status']==='Dipinjam'): ?>
          <a class="btn" href="../kembali/proses_kembali.php?id=<?= $t['id_pinjam'] ?>" data-confirm="Konfirmasi pengembalian?">Kembalikan</a>
        <?php endif; ?>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</div>
<?php include __DIR__.'/../includes/footer.php'; ?>
<script>
(function(){
  var select=document.querySelector('#modalPinjam select[name="id_barang"]');
  var qty=document.querySelector('#modalPinjam input[name="jumlah_pinjam"]');
  var hint=document.getElementById('stokHint');
  function updateMax(){
    if(!select) return;
    var opt=select.options[select.selectedIndex];
    var stok=opt && opt.getAttribute('data-stok');
    if(stok){
      qty.max=stok;
      if(qty.value && parseInt(qty.value,10)>parseInt(stok,10)) qty.value=stok;
      hint.textContent='Maksimal dapat dipinjam: '+stok;
    } else {
      if(qty) qty.removeAttribute('max');
      hint.textContent='';
    }
  }
  if(select){
    select.addEventListener('change', updateMax);
    updateMax();
  }

  function openModal(id){
    var el=document.getElementById(id);
    if(el){
      el.classList.add('open');
      el.setAttribute('aria-hidden','false');
      document.body.classList.add('modal-open');
      // autofocus first input/select
      var first = el.querySelector('input,select,button');
      if(first) { try{ first.focus(); }catch(e){} }
    }
  }
  function closeModal(id){
    var el=document.getElementById(id);
    if(el){
      el.classList.remove('open');
      el.setAttribute('aria-hidden','true');
      document.body.classList.remove('modal-open');
    }
  }
  document.getElementById('openFilter').addEventListener('click', function(){ openModal('modalFilter'); });
  document.getElementById('openPinjam').addEventListener('click', function(){ openModal('modalPinjam'); });
  document.querySelectorAll('[data-close]').forEach(function(btn){
    btn.addEventListener('click', function(){ closeModal(btn.getAttribute('data-close')); });
  });
  // close on overlay click
  document.querySelectorAll('.modal-overlay').forEach(function(ov){
    ov.addEventListener('click', function(e){ if(e.target===ov){ ov.classList.remove('open'); ov.setAttribute('aria-hidden','true'); } });
  });
  // close on Escape
  document.addEventListener('keydown', function(e){
    if(e.key==='Escape'){
      document.querySelectorAll('.modal-overlay.open').forEach(function(ov){
        ov.classList.remove('open');
        ov.setAttribute('aria-hidden','true');
      });
      document.body.classList.remove('modal-open');
    }
  });
})();
</script>
