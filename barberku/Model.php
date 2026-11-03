<?php
abstract class Model {
    protected PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    abstract public function semua(): array;
}
