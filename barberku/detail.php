<?php
require 'config.php';
$barber  = ambilBarber($pdo, (int)($_GET['barber'] ?? 0));
$layanan = ambilLayanan($pdo, (int)($_GET['layanan'] ?? 0));
$waktu   = $_GET['waktu'] ?? '';
if (!$barber || !$layanan || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $waktu)
    || !in_array(substr($waktu, 11), $JAM_TERSEDIA) || strtotime($waktu) < time()) {
    header('Location: index.php'); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama'] ?? '');
    $no_hp = preg_replace('/\D/', '', $_POST['no_hp'] ?? '');
    $no_hp = ltrim($no_hp, '0');
    $no_hp = '62' . ltrim($no_hp, '62') ;

    if ($nama === '' || strlen($no_hp) < 10 || strlen($no_hp) > 15) {
        $error = 'Nama dan nomor HP wajib diisi dengan benar.';
    } else {
        try {
            $pdo->beginTransaction();
            $s = $pdo->prepare('SELECT id_pelanggan FROM pelanggan WHERE no_hp = ?');
            $s->execute([$no_hp]);
            $id_pel = $s->fetchColumn();
            if (!$id_pel) {
                $pdo->prepare('INSERT INTO pelanggan (nama, no_hp) VALUES (?, ?)')->execute([$nama, $no_hp]);
                $id_pel = $pdo->lastInsertId();
            }
            $pdo->prepare('INSERT INTO booking (id_pelanggan, id_barber, id_layanan, tanggal) VALUES (?,?,?,?)')
                ->execute([$id_pel, $barber['id_barber'], $layanan['id_layanan'], $waktu . ':00']);
            $id = $pdo->lastInsertId();
            $pdo->commit();
            header('Location: sukses.php?id=' . $id); exit;
        } catch (PDOException $ex) {
            $pdo->rollBack();
            $error = $ex->getCode() == 23000
                ? 'Maaf, jam tersebut baru saja dibooking orang lain. Silakan pilih jam lain.'
                : 'Terjadi kesalahan, coba lagi.';
        }
    }
}

header_html('Your details', "jadwal.php?barber={$barber['id_barber']}&layanan={$layanan['id_layanan']}&tanggal=" . substr($waktu, 0, 10) . '&bulan=' . substr($waktu, 0, 7));
?>
<div class="grid">
  <div class="card">
    <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <label>Full Name *</label>
      <input type="text" name="nama" required maxlength="100" value="<?= e($_POST['nama'] ?? '') ?>">
      <label>Phone *</label>
      <div class="phone"><span>+62</span>
        <input type="tel" name="no_hp" required maxlength="13" placeholder="8123456789" value="<?= e($_POST['no_hp'] ?? '') ?>">
      </div>
      <button class="btn" type="submit">Confirm</button>
    </form>
    <div class="policy">
      <strong>Booking policy:</strong><br>
      Maks keterlambatan 5 menit dari jadwal. Lebih dari itu kami skip antrian berikutnya.<br><br>
      Reschedule ataupun cancel bisa kapanpun hubungi tukang cukur terkait.
    </div>
  </div>
  <?php kartu_barber($barber, $layanan, $waktu); ?>
</div>
<?php footer_html(); ?>
