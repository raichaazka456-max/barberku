<?php
require_once __DIR__ . '/Model.php';

class Nota extends Model {
    public function semua(): array {
        return $this->pdo->query("SELECT * FROM v_booking ORDER BY tanggal DESC")
                         ->fetchAll();
    }

    public function berdasarkanBooking(int $id): ?array {
        $stmt = $this->pdo->prepare(
            "SELECT bk.*, p.nama AS pelanggan, p.no_hp AS hp,
                    b.nama AS barber, l.nama_layanan, l.harga, l.durasi
             FROM booking bk
             JOIN pelanggan p ON p.id_pelanggan = bk.id_pelanggan
             JOIN barber b ON b.id_barber = bk.id_barber
             JOIN layanan l ON l.id_layanan = bk.id_layanan
             WHERE bk.id_booking = ?"
        );
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ?: null;
    }

    public function nomor(int $id): string {
        return 'A&H' . str_pad((string)$id, 5, '0', STR_PAD_LEFT);
    }
}
