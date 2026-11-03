<?php
require_once __DIR__ . '/Model.php';

class Pelanggan extends Model {
    public function semua(): array {
        return $this->pdo
            ->query("SELECT * FROM pelanggan ORDER BY id_pelanggan DESC")
            ->fetchAll();
    }

    public function cari(int $id): ?array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM pelanggan WHERE id_pelanggan = ?"
        );
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ?: null;
    }

    public function cariNomorHp(string $noHp): ?int {
        $stmt = $this->pdo->prepare(
            "SELECT id_pelanggan FROM pelanggan WHERE no_hp = ?"
        );
        $stmt->execute([$noHp]);
        $id = $stmt->fetchColumn();
        return $id ? (int)$id : null;
    }

    public function simpanJikaBelumAda(string $nama, string $noHp): int {
        $id = $this->cariNomorHp($noHp);

        if ($id !== null) {
            $stmt = $this->pdo->prepare(
                "UPDATE pelanggan SET nama = ? WHERE id_pelanggan = ?"
            );
            $stmt->execute([$nama, $id]);
            return $id;
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO pelanggan (nama, no_hp) VALUES (?, ?)"
        );
        $stmt->execute([$nama, $noHp]);

        return (int)$this->pdo->lastInsertId();
    }
}
