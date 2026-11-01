<?php
require 'config.php';
$barber = ambilBarber($pdo, (int)($_GET['barber'] ?? 0));
if (!$barber) { header('Location: index.php'); exit; }
$layanan = $pdo->query('SELECT * FROM layanan ORDER BY harga')->fetchAll();
header_html('Select a service', 'index.php');
?>
<div class="grid">
  <div class="card">
    <h3>Services</h3>
    <?php foreach ($layanan as $l): ?>
      <a class="service" href="jadwal.php?barber=<?= (int)$barber['id_barber'] ?>&layanan=<?= (int)$l['id_layanan'] ?>">
        <div>
          <strong><?= e($l['nama_layanan']) ?></strong><br>
          <small><?= $l['durasi'] / 60 ?> hr · <?= rupiah($l['harga']) ?></small>
        </div>
        <span>&#8250;</span>
      </a>
    <?php endforeach; ?>
  </div>
  <?php kartu_barber($barber); ?>
</div>
<?php footer_html(); ?>
