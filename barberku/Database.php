<?php
class Database {
    private static ?PDO $koneksi = null;

    private function __construct() {}

    public static function getConnection(): PDO {
        if (self::$koneksi === null) {
            $host = 'localhost';
            $dbname = 'barberku_db';
            $username = 'root';
            $password = '';

            self::$koneksi = new PDO(
                "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }

        return self::$koneksi;
    }
}
