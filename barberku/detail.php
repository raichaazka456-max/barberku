<?php
require 'bootstrap.php';

$barber = $barberModel->cari((int)($_GET['barber'] ?? 0));
$layanan = $layananModel->cari((int)($_GET['layanan'] ?? 0));
$waktu = $_GET['waktu'] ?? '';

$formatBenar = preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $waktu);
$tanggalWaktu = $formatBenar ? substr($waktu, 0, 10) : '';
$jamWaktu = $formatBenar ? substr($waktu, 11, 5) : '';

if (!$barber || !$layanan || !$formatBenar ||
    !checkdate((int)substr($tanggalWaktu, 5, 2), (int)substr($tanggalWaktu, 8, 2), (int)substr($tanggalWaktu, 0, 4)) ||
    !in_array($jamWaktu, $JAM_TERSEDIA, true) || strtotime($waktu) < time()) {
    header('Location: index.php'); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $noHp = preg_replace('/\D/', '', $_POST['no_hp'] ?? '');
    $noHp = preg_replace('/^(62|0)/', '', $noHp);
    $noHp = '62' . $noHp;

    if ($nama === '' || strlen($noHp) < 10 || strlen($noHp) > 15) {
        $error = 'Nama dan nomor HP wajib diisi dengan benar.';
    } else {
        try {
            $id = $bookingModel->buat(
                $nama,
                $noHp,
                (int)$barber['id_barber'],
                (int)$layanan['id_layanan'],
                $waktu . ':00'
            );
            header('Location: sukses.php?id=' . $id);
            exit;
        } catch (PDOException $e) {
            $error = $e->getCode() == 23000
                ? 'Maaf, jam tersebut baru saja dibooking orang lain. Silakan pilih jam lain.'
                : 'Terjadi kesalahan, coba lagi.';
        }
    }
}

$kembali = 'jadwal.php?barber=' . $barber['id_barber'] .
           '&layanan=' . $layanan['id_layanan'] .
           '&tanggal=' . $tanggalWaktu . '&bulan=' . substr($waktu, 0, 7);
View::header('Your details', $kembali);
?>
<div class="grid">
    <div class="card">
        <?php if ($error): ?><div class="alert"><?= View::e($error) ?></div><?php endif; ?>
        <form method="post">
            <label>Full Name *</label>
            <input type="text" name="nama" required maxlength="100" value="<?= View::e($_POST['nama'] ?? '') ?>">
            <label>Phone *</label>
            <div class="phone">
                <span>+62</span>
                <input type="tel" name="no_hp" required maxlength="15" placeholder="8123456789" value="<?= View::e($_POST['no_hp'] ?? '') ?>">
            </div>
            <button class="btn" type="submit">Confirm</button>
        </form>
        <div class="policy">
            <strong>Booking policy:</strong><br>
            Maks keterlambatan 5 menit dari jadwal. Lebih dari itu kami skip antrian berikutnya.<br><br>
            Reschedule ataupun cancel bisa kapanpun hubungi tukang cukur terkait.
        </div>
    </div>
    <?php View::kartuBarber($barber, $layanan, $waktu); ?>
</div>
<?php View::footer(); ?>
