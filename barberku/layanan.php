<?php
require 'bootstrap.php';
$barber = $barberModel->cari((int)($_GET['barber'] ?? 0));
if (!$barber) { header('Location: index.php'); exit; }
$layanans = $layananModel->semua();
View::header('Select a service', 'index.php');
?>
<div class="grid">
    <div class="card">
        <h3>Services</h3>
        <?php foreach ($layanans as $l): ?>
            <a class="service" href="jadwal.php?barber=<?= (int)$barber['id_barber'] ?>&layanan=<?= (int)$l['id_layanan'] ?>">
                <div>
                    <strong><?= View::e($l['nama_layanan']) ?></strong><br>
                    <small><?= (int)$l['durasi'] / 60 ?> hr · <?= View::rupiah($l['harga']) ?></small>
                </div>
                <span>&#8250;</span>
            </a>
        <?php endforeach; ?>
    </div>
    <?php View::kartuBarber($barber); ?>
</div>
<?php View::footer(); ?>
