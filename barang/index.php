<?php
require __DIR__.'/../includes/auth_check.php';
require __DIR__.'/../config/db.php';
$rows = $conn->query('SELECT * FROM inventaris ORDER BY id_barang DESC');
include __DIR__.'/../includes/header.php';
?>
<h2>Data Barang</h2>
<div class="actions">
  <button id="openAdd" class="btn" type="button">Tambah Barang</button>
</div>
<!-- Modal Tambah Barang -->
<div id="modalAdd" class="modal-overlay" aria-hidden="true">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="addTitle">
    <header>
      <h3 id="addTitle" style="margin:0;">Tambah Barang</h3>
      <button type="button" class="btn secondary" data-close="modalAdd">Tutup</button>
    </header>
    <form class="form compact" method="post" action="tambah.php">
      <input name="nama_barang" placeholder="Nama Barang" required>
      <input name="kategori" placeholder="Kategori" required>
      <input type="number" name="jumlah" placeholder="Jumlah" min="0" required>
      <select name="kondisi" required>
        <option value="">-- Pilih Kondisi --</option>
        <option value="Baru">Baru</option>
        <option value="Bekas">Bekas</option>
      </select>
      <select name="lokasi" required>
        <option value="">-- Pilih Lokasi --</option>
        <option value="Kantor Pusat (Jerman)">Kantor Pusat (Jerman) </option>
        <option value="Cabang Citayem">Cabang Citayem</option>
        <option value="Cabang Sobang">Cabang Sobang</option>
      </select>
      <div class="modal-actions">
        <button class="btn" type="submit">Simpan</button>
      </div>
    </form>
  </div>
  </div>

<div class="table-responsive">
<table>
  <thead>
    <tr>
      <th>Kode</th>
      <th>Nama</th>
      <th>Kategori</th>
      <th>Jumlah</th>
      <th>Kondisi</th>
      <th>Lokasi</th>
      <th>Tanggal Input</th>
      <th class="no-print">Aksi</th>
    </tr>
  </thead>
  <tbody>
  <?php if($rows){ while($r=$rows->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($r['kode_barang']) ?></td>
      <td><?= htmlspecialchars($r['nama_barang']) ?></td>
      <td><?= htmlspecialchars($r['kategori']) ?></td>
      <td><?= (int)$r['jumlah'] ?></td>
      <td><?= htmlspecialchars($r['kondisi']) ?></td>
      <td><?= htmlspecialchars($r['lokasi']) ?></td>
      <td><?= htmlspecialchars($r['tanggal_input']) ?></td>
      <td class="no-print">
        <a class="btn" href="edit.php?id=<?= (int)$r['id_barang'] ?>">Edit</a>
        <a class="btn danger" href="hapus.php?id=<?= (int)$r['id_barang'] ?>">Hapus</a>
      </td>
    </tr>
  <?php endwhile; } ?>
  </tbody>
</table>
</div>
<?php include __DIR__.'/../includes/footer.php'; ?>
<script>
(function(){
  function openModal(id){
    var el=document.getElementById(id);
    if(el){ el.classList.add('open'); el.setAttribute('aria-hidden','false'); document.body.classList.add('modal-open');
      var first=el.querySelector('input,select,button'); if(first){ try{first.focus();}catch(e){} }
    }
  }
  function closeModal(id){
    var el=document.getElementById(id);
    if(el){ el.classList.remove('open'); el.setAttribute('aria-hidden','true'); document.body.classList.remove('modal-open'); }
  }
  var btn=document.getElementById('openAdd');
  if(btn){ btn.addEventListener('click', function(){ openModal('modalAdd'); }); }
  document.querySelectorAll('[data-close]').forEach(function(b){ b.addEventListener('click', function(){ closeModal(b.getAttribute('data-close')); }); });
  document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ closeModal('modalAdd'); } });
  document.querySelectorAll('.modal-overlay').forEach(function(ov){ ov.addEventListener('click', function(e){ if(e.target===ov){ closeModal('modalAdd'); } }); });
})();
</script>
