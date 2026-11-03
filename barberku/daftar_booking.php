<?php
require 'config.php';

$error = '';
$editId = 0;

// ---------- HAPUS (POST) ----------
if (isset($_POST['hapus'])) {
    $id = (int)($_POST['id_booking'] ?? 0);
    if ($id > 0) {
        $pdo->prepare('DELETE FROM booking WHERE id_booking = ?')->execute([$id]);
    }
    header('Location: daftar_booking.php'); exit;
}

// ---------- EDIT (POST) ----------
if (isset($_POST['edit'])) {
    $editId     = (int)($_POST['id_booking'] ?? 0);
    $id_barber  = (int)($_POST['id_barber'] ?? 0);
    $id_layanan = (int)($_POST['id_layanan'] ?? 0);
    $tanggal    = trim($_POST['tanggal'] ?? '');
    $jam        = trim($_POST['jam'] ?? '');

    $valid_tgl = preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $tanggal, $m)
                 && checkdate((int)$m[2], (int)$m[3], (int)$m[1]);

    if ($editId <= 0) {
        $error = 'Data booking tidak valid.';
    } elseif (!ambilBarber($pdo, $id_barber) || !ambilLayanan($pdo, $id_layanan)) {
        $error = 'Pilih barber dan layanan yang tersedia.';
    } elseif (!$valid_tgl) {
        $error = 'Format tanggal tidak valid.';
    } elseif (!in_array($jam, $JAM_TERSEDIA, true)) {
        $error = 'Pilih jam yang tersedia.';
    } elseif (strtotime("$tanggal $jam") < time()) {
        $error = 'Jadwal tidak boleh di waktu yang sudah lewat.';
    } else {
        $waktu = "$tanggal $jam:00";

        // jam yang sama pada barber yang sama tidak boleh bentrok
        $cek = $pdo->prepare('SELECT COUNT(*) FROM booking WHERE id_barber = ? AND tanggal = ? AND id_booking <> ?');
        $cek->execute([$id_barber, $waktu, $editId]);

        if ($cek->fetchColumn() > 0) {
            $error = 'Jam tersebut sudah dibooking untuk barber ini. Pilih jam lain.';
        } else {
            try {
                $pdo->prepare('UPDATE booking SET id_barber = ?, id_layanan = ?, tanggal = ? WHERE id_booking = ?')
                    ->execute([$id_barber, $id_layanan, $waktu, $editId]);
                header('Location: daftar_booking.php'); exit;
            } catch (PDOException $ex) {
                $error = $ex->getCode() == 23000
                    ? 'Jam tersebut sudah dibooking untuk barber ini. Pilih jam lain.'
                    : 'Terjadi kesalahan, coba lagi.';
            }
        }
    }
}

// ---------- DATA UNTUK FORM EDIT ----------
if (!$editId && isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
}

$editData = null;
if ($editId > 0) {
    $s = $pdo->prepare('SELECT * FROM booking WHERE id_booking = ?');
    $s->execute([$editId]);
    $editData = $s->fetch();
}

if ($editData) {
    $f_barber  = (int)($_POST['id_barber']  ?? $editData['id_barber']);
    $f_layanan = (int)($_POST['id_layanan'] ?? $editData['id_layanan']);
    $f_tanggal = $_POST['tanggal'] ?? substr($editData['tanggal'], 0, 10);
    $f_jam     = $_POST['jam']     ?? substr($editData['tanggal'], 11, 5);
    $barbers   = $pdo->query('SELECT * FROM barber ORDER BY id_barber')->fetchAll();
    $layanans  = $pdo->query('SELECT * FROM layanan ORDER BY harga')->fetchAll();
}

// ---------- DAFTAR BOOKING ----------
$data = $pdo->query(
    'SELECT b.id_booking, b.tanggal,
            p.nama AS nama_pelanggan, p.no_hp,
            br.nama AS nama_barber,
            l.nama_layanan, l.harga
     FROM booking b
     LEFT JOIN pelanggan p ON b.id_pelanggan = p.id_pelanggan
     LEFT JOIN barber br   ON b.id_barber    = br.id_barber
     LEFT JOIN layanan l   ON b.id_layanan   = l.id_layanan
     ORDER BY b.tanggal DESC, b.id_booking DESC'
)->fetchAll();

header_html('Daftar Booking', 'index.php');
?>
<style>
  .tbl{width:100%;border-collapse:collapse}
  .tbl th,.tbl td{padding:10px 12px;border-bottom:1px solid #ddd;text-align:left;vertical-align:middle}
  .tbl-wrap{overflow-x:auto}
  .aksi{display:flex;gap:8px;align-items:center}
  .aksi form{margin:0}
  .aksi a,.aksi button{padding:6px 12px;border-radius:6px;border:1px solid #ccc;background:#fff;color:#222;font:inherit;font-size:13px;cursor:pointer;text-decoration:none}
  .aksi .hapus{border-color:#c0392b;color:#c0392b}
  .aksi .btn{margin-top:0;background:#111;color:#fff;padding:10px 18px}
  .form-edit label{display:block;margin:10px 0 4px;font-weight:600}
  .form-edit input,.form-edit select{width:100%;padding:9px;margin-bottom:6px}
</style>

<div class="card">
  <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>

  <?php if ($editData): ?>
    <h3>Edit Booking #<?= (int)$editData['id_booking'] ?></h3>
    <form method="post" class="form-edit">
      <input type="hidden" name="id_booking" value="<?= (int)$editData['id_booking'] ?>">

      <label>Barber</label>
      <select name="id_barber" required>
        <?php foreach ($barbers as $b): ?>
          <option value="<?= (int)$b['id_barber'] ?>" <?= $f_barber === (int)$b['id_barber'] ? 'selected' : '' ?>>
            <?= e($b['nama']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Layanan</label>
      <select name="id_layanan" required>
        <?php foreach ($layanans as $l): ?>
          <option value="<?= (int)$l['id_layanan'] ?>" <?= $f_layanan === (int)$l['id_layanan'] ? 'selected' : '' ?>>
            <?= e($l['nama_layanan']) ?> (<?= rupiah($l['harga']) ?>)
          </option>
        <?php endforeach; ?>
      </select>

      <label>Tanggal</label>
      <input type="date" name="tanggal" min="<?= date('Y-m-d') ?>" value="<?= e($f_tanggal) ?>" required>

      <label>Jam</label>
      <select name="jam" required>
        <?php foreach ($JAM_TERSEDIA as $j): ?>
          <option value="<?= e($j) ?>" <?= $f_jam === $j ? 'selected' : '' ?>><?= e($j) ?></option>
        <?php endforeach; ?>
      </select>

      <div class="aksi" style="margin-top:12px">
        <button class="btn" type="submit" name="edit">Simpan Perubahan</button>
        <a href="daftar_booking.php">Batal</a>
      </div>
    </form>
    <hr>
  <?php endif; ?>

  <h3>Semua Booking</h3>
  <div class="tbl-wrap">
    <table class="tbl">
      <tr>
        <th>ID</th><th>Pelanggan</th><th>No. HP</th><th>Barber</th>
        <th>Layanan</th><th>Jadwal</th><th>Aksi</th>
      </tr>
      <?php if ($data): ?>
        <?php foreach ($data as $b): ?>
        <tr>
          <td><?= (int)$b['id_booking'] ?></td>
          <td><?= e($b['nama_pelanggan'] ?? '-') ?></td>
          <td><?= e($b['no_hp'] ?? '-') ?></td>
          <td><?= e($b['nama_barber'] ?? '-') ?></td>
          <td><?= e($b['nama_layanan'] ?? '-') ?></td>
          <td><?= e(date('d M Y, H:i', strtotime($b['tanggal']))) ?></td>
          <td>
            <div class="aksi">
              <a href="daftar_booking.php?edit=<?= (int)$b['id_booking'] ?>">Edit</a>
              <form method="post" onsubmit="return confirm('Yakin ingin menghapus booking ini?')">
                <input type="hidden" name="id_booking" value="<?= (int)$b['id_booking'] ?>">
                <button class="hapus" type="submit" name="hapus">Hapus</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="7">Belum ada data booking.</td></tr>
      <?php endif; ?>
    </table>
  </div>
</div>
<?php footer_html(); ?>