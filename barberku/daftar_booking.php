<?php
require 'bootstrap.php';

$error = '';
$editId = 0;

if (isset($_POST['hapus'])) {
    $id = (int)($_POST['id_booking'] ?? 0);
    if ($id > 0) $bookingModel->hapus($id);
    header('Location: daftar_booking.php');
    exit;
}

if (isset($_POST['edit'])) {
    $editId = (int)($_POST['id_booking'] ?? 0);
    $idBarber = (int)($_POST['id_barber'] ?? 0);
    $idLayanan = (int)($_POST['id_layanan'] ?? 0);
    $tanggal = trim($_POST['tanggal'] ?? '');
    $jam = trim($_POST['jam'] ?? '');

    $validTanggal = preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) &&
        checkdate((int)substr($tanggal, 5, 2), (int)substr($tanggal, 8, 2), (int)substr($tanggal, 0, 4));

    if ($editId <= 0) {
        $error = 'Data booking tidak valid.';
    } elseif (!$barberModel->cari($idBarber) || !$layananModel->cari($idLayanan)) {
        $error = 'Pilih barber dan layanan yang tersedia.';
    } elseif (!$validTanggal) {
        $error = 'Format tanggal tidak valid.';
    } elseif (!in_array($jam, $JAM_TERSEDIA, true)) {
        $error = 'Pilih jam yang tersedia.';
    } elseif (strtotime("$tanggal $jam") < time()) {
        $error = 'Jadwal tidak boleh di waktu yang sudah lewat.';
    } else {
        $waktu = "$tanggal $jam:00";
        try {
            $bookingModel->ubah($editId, $idBarber, $idLayanan, $waktu);
            header('Location: daftar_booking.php');
            exit;
        } catch (PDOException $e) {
            $error = $e->getCode() == 23000
                ? 'Jam tersebut sudah dibooking untuk barber ini. Pilih jam lain.'
                : 'Terjadi kesalahan, coba lagi.';
        }
    }
}

if (!$editId && isset($_GET['edit'])) $editId = (int)$_GET['edit'];
$editData = $editId > 0 ? $bookingModel->cari($editId) : null;
$barbers = $editData ? $barberModel->semua() : [];
$layanans = $editData ? $layananModel->semua() : [];
if ($editData) {
    $fBarber = (int)($_POST['id_barber'] ?? $editData['id_barber']);
    $fLayanan = (int)($_POST['id_layanan'] ?? $editData['id_layanan']);
    $fTanggal = $_POST['tanggal'] ?? substr($editData['tanggal'], 0, 10);
    $fJam = $_POST['jam'] ?? substr($editData['tanggal'], 11, 5);
}

$data = $bookingModel->semua();
View::header('Daftar Booking', 'index.php');
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
    <?php if ($error): ?><div class="alert"><?= View::e($error) ?></div><?php endif; ?>

    <?php if ($editData): ?>
        <h3>Edit Booking #<?= (int)$editData['id_booking'] ?></h3>
        <form method="post" class="form-edit">
            <input type="hidden" name="id_booking" value="<?= (int)$editData['id_booking'] ?>">
            <label>Barber</label>
            <select name="id_barber" required>
                <?php foreach ($barbers as $b): ?>
                    <option value="<?= (int)$b['id_barber'] ?>" <?= $fBarber === (int)$b['id_barber'] ? 'selected' : '' ?>>
                        <?= View::e($b['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label>Layanan</label>
            <select name="id_layanan" required>
                <?php foreach ($layanans as $l): ?>
                    <option value="<?= (int)$l['id_layanan'] ?>" <?= $fLayanan === (int)$l['id_layanan'] ? 'selected' : '' ?>>
                        <?= View::e($l['nama_layanan']) ?> (<?= View::rupiah($l['harga']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <label>Tanggal</label>
            <input type="date" name="tanggal" min="<?= date('Y-m-d') ?>" value="<?= View::e($fTanggal) ?>" required>
            <label>Jam</label>
            <select name="jam" required>
                <?php foreach ($JAM_TERSEDIA as $j): ?>
                    <option value="<?= View::e($j) ?>" <?= $fJam === $j ? 'selected' : '' ?>><?= View::e($j) ?></option>
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
            <?php if ($data): foreach ($data as $b): ?>
                <tr>
                    <td><?= (int)$b['id_booking'] ?></td>
                    <td><?= View::e($b['nama_pelanggan'] ?? '-') ?></td>
                    <td><?= View::e($b['no_hp'] ?? '-') ?></td>
                    <td><?= View::e($b['nama_barber'] ?? '-') ?></td>
                    <td><?= View::e($b['nama_layanan'] ?? '-') ?></td>
                    <td><?= View::e(date('d M Y, H:i', strtotime($b['tanggal']))) ?></td>
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
            <?php endforeach; else: ?>
                <tr><td colspan="7">Belum ada data booking.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>
<?php View::footer(); ?>
