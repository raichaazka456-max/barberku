<?php
require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/Pelanggan.php';

class Booking extends Model {
    public function semua(): array {
        $sql = "SELECT b.id_booking, b.tanggal,
                       p.nama AS nama_pelanggan, p.no_hp,
                       br.nama AS nama_barber,
                       l.nama_layanan, l.harga
                FROM booking b
                LEFT JOIN pelanggan p ON b.id_pelanggan = p.id_pelanggan
                LEFT JOIN barber br ON b.id_barber = br.id_barber
                LEFT JOIN layanan l ON b.id_layanan = l.id_layanan
                ORDER BY b.tanggal DESC, b.id_booking DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function cari(int $id): ?array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM booking WHERE id_booking = ?"
        );
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ?: null;
    }

    public function jadwalTerisi(int $idBarber, string $tanggal): array {
        $stmt = $this->pdo->prepare(
            "SELECT DATE_FORMAT(tanggal, '%H:%i') AS jam
             FROM booking
             WHERE id_barber = ? AND DATE(tanggal) = ?"
        );
        $stmt->execute([$idBarber, $tanggal]);
        return array_column($stmt->fetchAll(), 'jam');
    }

    public function buat(
        string $nama,
        string $noHp,
        int $idBarber,
        int $idLayanan,
        string $waktu
    ): int {
        $pelanggan = new Pelanggan($this->pdo);

        try {
            $this->pdo->beginTransaction();

            $idPelanggan = $pelanggan->simpanJikaBelumAda($nama, $noHp);

            $stmt = $this->pdo->prepare(
                "INSERT INTO booking
                 (id_pelanggan, id_barber, id_layanan, tanggal)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $idPelanggan,
                $idBarber,
                $idLayanan,
                $waktu
            ]);

            $idBooking = (int)$this->pdo->lastInsertId();
            $this->pdo->commit();

            return $idBooking;
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function ubah(
        int $idBooking,
        int $idBarber,
        int $idLayanan,
        string $waktu
    ): void {
        $stmt = $this->pdo->prepare(
            "UPDATE booking
             SET id_barber = ?, id_layanan = ?, tanggal = ?
             WHERE id_booking = ?"
        );
        $stmt->execute([$idBarber, $idLayanan, $waktu, $idBooking]);
    }

    public function hapus(int $idBooking): void {
        $stmt = $this->pdo->prepare(
            "DELETE FROM booking WHERE id_booking = ?"
        );
        $stmt->execute([$idBooking]);
    }

    public function detailLengkap(int $id): ?array {
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
}
