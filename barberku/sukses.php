<?php
require 'bootstrap.php';
$id = (int)($_GET['id'] ?? 0);
$d = $bookingModel->detailLengkap($id);
if (!$d) { header('Location: index.php'); exit; }

View::header('Booking berhasil');
?>
<div class="card success">
    <div class="check">&#10003;</div>
    <h2>Terima kasih, <?= View::e($d['pelanggan']) ?>!</h2>
    <p class="muted">Booking #<?= (int)$d['id_booking'] ?> telah dikonfirmasi.</p>
    <div class="row"><span>Barber</span><strong><?= View::e($d['barber']) ?></strong></div>
    <div class="row"><span>Layanan</span><strong><?= View::e($d['nama_layanan']) ?></strong></div>
    <div class="row"><span>Jadwal</span><strong><?= date('l j F Y, H:i', strtotime($d['tanggal'])) ?></strong></div>
    <div class="row total"><span>Total</span><span><?= View::rupiah($d['harga']) ?></span></div>
    <a class="btn" href="nota.php?id=<?= (int)$d['id_booking'] ?>">Lihat Nota</a>
    <a class="btn btn-light" href="index.php">Booking lagi</a>
</div>
<?php View::footer(); ?>
