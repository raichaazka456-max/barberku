<?php
require 'bootstrap.php';
$barbers = $barberModel->semua();
View::header('BarberKu');
?>
<style>
body{min-height:100vh;background:linear-gradient(rgba(0,0,0,.35),rgba(0,0,0,.35)),url('img/barberku.jpg') center/cover no-repeat fixed!important}
.topbar h1,.brand{color:#fff;text-shadow:0 2px 6px rgba(0,0,0,.6)}
.center{max-width:760px;padding-top:30px}
.brand{font-size:40px;margin-bottom:36px}
.pill{padding:18px 28px;margin-bottom:28px;border-radius:60px;box-shadow:0 6px 18px rgba(0,0,0,.35);transition:transform .15s,box-shadow .15s}
.pill:hover{transform:translateY(-4px);box-shadow:0 10px 24px rgba(0,0,0,.45)}
.pill img{width:110px;height:110px;border:3px solid #fff}
.pill span{font-size:28px;font-weight:600}
.menu-booking{display:block;margin:10px auto 40px;padding:14px 28px;max-width:320px;text-align:center;border-radius:60px;background:rgba(0,0,0,.65);border:2px solid #fff;color:#fff;font-size:18px;font-weight:600;text-decoration:none;transition:background .15s}
.menu-booking:hover{background:#000}
</style>
<div class="center">
    <h2 class="brand">A&amp;H HAIRSTUDIO</h2>
    <?php foreach ($barbers as $b): ?>
        <a class="pill" href="layanan.php?barber=<?= (int)$b['id_barber'] ?>">
            <img src="img/<?= View::e($b['foto']) ?>" alt="" onerror="this.style.visibility='hidden'">
            <span><?= View::e($b['nama']) ?></span>
        </a>
    <?php endforeach; ?>
    <a class="menu-booking" href="daftar_booking.php">Lihat Daftar Booking</a>
</div>
<?php View::footer(); ?>
