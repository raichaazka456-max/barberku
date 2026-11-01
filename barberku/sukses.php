<?php
require 'config.php';
$s = $pdo->prepare('SELECT bk.*, p.nama pelanggan, b.nama barber, l.nama_layanan, l.harga, l.durasi
  FROM booking bk
  JOIN pelanggan p ON p.id_pelanggan = bk.id_pelanggan
  JOIN barber b ON b.id_barber = bk.id_barber
  JOIN layanan l ON l.id_layanan = bk.id_layanan
  WHERE bk.id_booking = ?');
$s->execute([(int)($_GET['id'] ?? 0)]);
$d = $s->fetch();
if (!$d) { header('Location: index.php'); exit; }
header_html('Booking berhasil');
?>
<div class="card success">
  <div class="check">&#10003;</div>
  <h2>Terima kasih, <?= e($d['pelanggan']) ?>!</h2>
  <p class="muted">Booking #<?= (int)$d['id_booking'] ?> telah dikonfirmasi.</p>
  <div class="row"><span>Barber</span><strong><?= e($d['barber']) ?></strong></div>
  <div class="row"><span>Layanan</span><strong><?= e($d['nama_layanan']) ?></strong></div>
  <div class="row"><span>Jadwal</span><strong><?= date('l j F Y, H:i', strtotime($d['tanggal'])) ?></strong></div>
  <div class="row total"><span>Total</span><span><?= rupiah($d['harga']) ?></span></div>
  <a class="btn" href="nota.php?id=<?= (int)$d['id_booking'] ?>">Lihat Nota</a>
  <a class="btn btn-light" href="index.php">Booking lagi</a>
</div>
<?php footer_html(); ?>
