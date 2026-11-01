<?php
require 'config.php';
$s = $pdo->prepare('SELECT bk.*, p.nama pelanggan, p.no_hp hp, b.nama barber, l.nama_layanan, l.harga, l.durasi
  FROM booking bk
  JOIN pelanggan p ON p.id_pelanggan = bk.id_pelanggan
  JOIN barber b ON b.id_barber = bk.id_barber
  JOIN layanan l ON l.id_layanan = bk.id_layanan
  WHERE bk.id_booking = ?');
$s->execute([(int)($_GET['id'] ?? 0)]);
$d = $s->fetch();
if (!$d) { header('Location: index.php'); exit; }

$mulai   = strtotime($d['tanggal']);
$selesai = $mulai + $d['durasi'] * 60;
$noNota  = 'A&H' . str_pad($d['id_booking'], 5, '0', STR_PAD_LEFT);
header_html('Nota Booking', 'sukses.php?id=' . (int)$d['id_booking']);
?>
<div class="nota">
  <div class="nota-head">
    <h2>A&H HAIRSTUDIO</h2>
    <p class="muted">Nota Booking</p>
  </div>
  <div class="row"><span>No. Nota</span><strong><?= e($noNota) ?></strong></div>
  <div class="row"><span>Dicetak</span><span><?= date('d/m/Y H:i') ?></span></div>
  <hr class="dash">
  <div class="row"><span>Pelanggan</span><strong><?= e($d['pelanggan']) ?></strong></div>
  <div class="row"><span>No. HP</span><span>+<?= e($d['hp']) ?></span></div>
  <div class="row"><span>Barber</span><strong><?= e($d['barber']) ?></strong></div>
  <div class="row"><span>Jadwal</span><span><?= date('d/m/Y', $mulai) ?></span></div>
  <div class="row"><span>Jam</span><span><?= date('H:i', $mulai) ?> – <?= date('H:i', $selesai) ?></span></div>
  <hr class="dash">
  <div class="row"><span><?= e($d['nama_layanan']) ?> (<?= (int)$d['durasi'] ?> mnt)</span><span><?= rupiah($d['harga']) ?></span></div>
  <hr class="dash">
  <div class="row total"><span>TOTAL</span><span><?= rupiah($d['harga']) ?></span></div>
  <div class="row"><span>Pembayaran</span><span>Bayar di tempat</span></div>
  <hr class="dash">
  <p class="muted center-text">Maks keterlambatan 5 menit dari jadwal.<br>Terima kasih sudah booking!</p>
  <div class="no-print actions">
    <a class="btn btn-light" href="index.php">Booking lagi</a>
  </div>
</div>
<?php footer_html(); ?>