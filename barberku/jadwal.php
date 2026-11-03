<?php
require 'bootstrap.php';
$barber = $barberModel->cari((int)($_GET['barber'] ?? 0));
$layanan = $layananModel->cari((int)($_GET['layanan'] ?? 0));
if (!$barber || !$layanan) { header('Location: index.php'); exit; }

$bulan = $_GET['bulan'] ?? date('Y-m');
if (!preg_match('/^\d{4}-\d{2}$/', $bulan)) $bulan = date('Y-m');
$awal = strtotime($bulan . '-01');
$tanggal = $_GET['tanggal'] ?? null;
if ($tanggal && (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) ||
    !checkdate((int)substr($tanggal, 5, 2), (int)substr($tanggal, 8, 2), (int)substr($tanggal, 0, 4)))) {
    $tanggal = null;
}

$base = 'barber=' . $barber['id_barber'] . '&layanan=' . $layanan['id_layanan'];
$prev = date('Y-m', strtotime('-1 month', $awal));
$next = date('Y-m', strtotime('+1 month', $awal));
$terisi = $tanggal ? $bookingModel->jadwalTerisi((int)$barber['id_barber'], $tanggal) : [];

View::header('Select a time', 'layanan.php?barber=' . $barber['id_barber']);
?>
<div class="grid">
    <div class="card">
        <h3><?= View::e($layanan['nama_layanan']) ?></h3>
        <div class="cols">
            <div>
                <div class="cal-head">
                    <strong><?= date('F Y', $awal) ?></strong>
                    <span>
                        <a href="?<?= $base ?>&bulan=<?= $prev ?>">&#8249;</a>
                        <a href="?<?= $base ?>&bulan=<?= $next ?>">&#8250;</a>
                    </span>
                </div>
                <div class="cal">
                    <?php foreach (['M','T','W','T','F','S','S'] as $h) echo '<b>' . $h . '</b>'; ?>
                    <?php
                    $offset = (int)date('N', $awal) - 1;
                    for ($i = 0; $i < $offset; $i++) echo '<span></span>';
                    $jumlahHari = (int)date('t', $awal);
                    for ($d = 1; $d <= $jumlahHari; $d++) {
                        $tgl = sprintf('%s-%02d', $bulan, $d);
                        $lewat = $tgl < date('Y-m-d');
                        $aktif = $tgl === $tanggal ? ' active' : '';
                        if ($lewat) echo "<span class='off'>$d</span>";
                        else echo "<a class='day$aktif' href='?$base&bulan=$bulan&tanggal=$tgl'>$d</a>";
                    }
                    ?>
                </div>
                <p class="muted">Time zone: Indonesia – Makassar</p>
            </div>
            <div>
                <?php if ($tanggal): ?>
                    <p><strong><?= date('l j F', strtotime($tanggal)) ?></strong></p>
                    <div class="slots">
                        <?php foreach ($JAM_TERSEDIA as $jam):
                            $lewat = strtotime("$tanggal $jam") < time();
                            if (in_array($jam, $terisi, true) || $lewat) {
                                echo "<span class='slot off'>" . View::e($jam) . "</span>";
                                continue;
                            } ?>
                            <a class="slot" href="detail.php?<?= $base ?>&waktu=<?= urlencode("$tanggal $jam") ?>"><?= View::e($jam) ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="muted">Pilih tanggal terlebih dahulu.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php View::kartuBarber($barber, $layanan); ?>
</div>
<?php View::footer(); ?>
