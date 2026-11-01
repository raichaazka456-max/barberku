<?php
session_start();
date_default_timezone_set('Asia/Makassar');

$pdo = new PDO('mysql:host=localhost;dbname=barberku_db;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$JAM_TERSEDIA = ['14:00','15:00','16:00','18:00','19:00','21:00'];

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function rupiah($n) { return 'Rp ' . number_format($n, 0, ',', '.'); }

function ambilBarber($pdo, $id) {
    $s = $pdo->prepare('SELECT * FROM barber WHERE id_barber = ?');
    $s->execute([$id]);
    return $s->fetch();
}
function ambilLayanan($pdo, $id) {
    $s = $pdo->prepare('SELECT * FROM layanan WHERE id_layanan = ?');
    $s->execute([$id]);
    return $s->fetch();
}

function header_html($judul, $back = null, $bodyClass = '') {
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>' . e($judul) . ' | A&H Hairstudio</title>
    <link rel="stylesheet" href="style.css"></head><body class="' . e($bodyClass) . '"><div class="wrap">';
    echo '<div class="topbar">';
    if ($back) echo '<a class="back" href="' . e($back) . '">&#8249;</a>';
    echo '<h1>' . e($judul) . '</h1></div>';
}
function footer_html() { echo '</div></body></html>'; }

function kartu_barber($b, $layanan = null, $waktu = null) {
    $foto = $b['foto'] ? 'img/' . $b['foto'] : '';
    echo '<aside><div class="card profil">';
    echo $foto ? '<img class="avatar" src="' . e($foto) . '" alt="" onerror="this.style.visibility=\'hidden\'">' : '<div class="avatar"></div>';
    echo '<h2>' . e($b['nama']) . '</h2><hr><div class="studio"><strong>A&H_Hairstudio</strong></div></div>';
    if ($layanan) {
        echo '<div class="card"><h3>Summary</h3>
        <div class="row"><span>' . e($layanan['nama_layanan']) . '</span><span>' . rupiah($layanan['harga']) . '</span></div>
        <small>' . ($layanan['durasi'] / 60) . ' hr</small>';
        if ($waktu) {
            $t = strtotime($waktu);
            echo '<p class="muted">' . date('l j F Y H:i', $t) . ' – ' . date('H:i', $t + $layanan['durasi'] * 60) . '</p>';
        }
        echo '<hr><div class="row total"><span>Total to pay</span><span>' . rupiah($layanan['harga']) . '</span></div></div>';
    }
    echo '</aside>';
}
