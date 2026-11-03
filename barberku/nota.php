<?php
require 'bootstrap.php';
$id = (int)($_GET['id'] ?? 0);
$d = $notaModel->berdasarkanBooking($id);
if (!$d) { header('Location: index.php'); exit; }

$mulai = strtotime($d['tanggal']);
$selesai = $mulai + ((int)$d['durasi'] * 60);
$noNota = $notaModel->nomor((int)$d['id_booking']);

View::header('Nota Booking', 'sukses.php?id=' . $id);
?>
<div class="nota">
    <div class="nota-head">
        <h2>A&amp;H HAIRSTUDIO</h2>
        <p class="muted">Nota Booking</p>
    </div>
    <div class="row"><span>No. Nota</span><strong><?= View::e($noNota) ?></strong></div>
    <div class="row"><span>Dicetak</span><span><?= date('d/m/Y H:i') ?></span></div>
    <hr class="dash">
    <div class="row"><span>Pelanggan</span><strong><?= View::e($d['pelanggan']) ?></strong></div>
    <div class="row"><span>No. HP</span><span>+<?= View::e($d['hp']) ?></span></div>
    <div class="row"><span>Barber</span><strong><?= View::e($d['barber']) ?></strong></div>
    <div class="row"><span>Jadwal</span><span><?= date('d/m/Y', $mulai) ?></span></div>
    <div class="row"><span>Jam</span><span><?= date('H:i', $mulai) ?> – <?= date('H:i', $selesai) ?></span></div>
    <hr class="dash">
    <div class="row"><span><?= View::e($d['nama_layanan']) ?> (<?= (int)$d['durasi'] ?> mnt)</span><span><?= View::rupiah($d['harga']) ?></span></div>
    <hr class="dash">
    <div class="row total"><span>TOTAL</span><span><?= View::rupiah($d['harga']) ?></span></div>
    <div class="row"><span>Pembayaran</span><span>Bayar di tempat</span></div>
    <hr class="dash">
    <p class="muted center-text">Maks keterlambatan 5 menit dari jadwal.<br>Terima kasih sudah booking!</p>
    <div class="no-print actions">
        <a class="btn btn-light" href="index.php">Booking lagi</a>
    </div>
</div>
<?php View::footer(); ?>
