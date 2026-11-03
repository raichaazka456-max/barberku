<?php
require_once __DIR__ . '/Model.php';

class Barber extends Model {
    public function semua(): array {
        $sql = "SELECT * FROM barber ORDER BY id_barber";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function cari(int $id): ?array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM barber WHERE id_barber = ?"
        );
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ?: null;
    }
}
