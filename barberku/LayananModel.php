<?php
require_once __DIR__ . '/Model.php';

class Layanan extends Model {
    public function semua(): array {
        return $this->pdo
            ->query("SELECT * FROM layanan ORDER BY harga")
            ->fetchAll();
    }

    public function cari(int $id): ?array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM layanan WHERE id_layanan = ?"
        );
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ?: null;
    }
}
