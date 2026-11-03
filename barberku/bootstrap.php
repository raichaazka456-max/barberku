<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Makassar');

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Barber.php';
require_once __DIR__ . '/LayananModel.php';
require_once __DIR__ . '/Pelanggan.php';
require_once __DIR__ . '/Booking.php';
require_once __DIR__ . '/NotaModel.php';
require_once __DIR__ . '/View.php';

$pdo = Database::getConnection();

$barberModel = new Barber($pdo);
$layananModel = new Layanan($pdo);
$pelangganModel = new Pelanggan($pdo);
$bookingModel = new Booking($pdo);
$notaModel = new Nota($pdo);

$JAM_TERSEDIA = ['14:00', '15:00', '16:00', '18:00', '19:00', '21:00'];
