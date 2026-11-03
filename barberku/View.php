<?php
class View {
    public static function e($nilai): string {
        return htmlspecialchars((string)$nilai, ENT_QUOTES, 'UTF-8');
    }

    public static function rupiah($nilai): string {
        return 'Rp ' . number_format((float)$nilai, 0, ',', '.');
    }

    public static function header(string $judul, ?string $back = null, string $bodyClass = ''): void {
        echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<title>' . self::e($judul) . ' | A&H Hairstudio</title>';
        echo '<link rel="stylesheet" href="style.css"></head>';
        echo '<body class="' . self::e($bodyClass) . '"><div class="wrap">';
        echo '<div class="topbar">';
        if ($back) {
            echo '<a class="back" href="' . self::e($back) . '">&#8249;</a>';
        }
        echo '<h1>' . self::e($judul) . '</h1></div>';
    }

    public static function footer(): void {
        echo '</div></body></html>';
    }

    public static function kartuBarber(array $barber, ?array $layanan = null, ?string $waktu = null): void {
        $foto = !empty($barber['foto']) ? 'img/' . $barber['foto'] : '';
        echo '<aside><div class="card profil">';
        if ($foto) {
            echo '<img class="avatar" src="' . self::e($foto) . '" alt="" onerror="this.style.visibility=\'hidden\'">';
        } else {
            echo '<div class="avatar"></div>';
        }
        echo '<h2>' . self::e($barber['nama']) . '</h2><hr>';
        echo '<div class="studio"><strong>A&H_Hairstudio</strong></div></div>';

        if ($layanan) {
            echo '<div class="card"><h3>Summary</h3>';
            echo '<div class="row"><span>' . self::e($layanan['nama_layanan']) . '</span>';
            echo '<span>' . self::rupiah($layanan['harga']) . '</span></div>';
            echo '<small>' . ((int)$layanan['durasi'] / 60) . ' hr</small>';
            if ($waktu) {
                $mulai = strtotime($waktu);
                echo '<p class="muted">' . date('l j F Y H:i', $mulai) . ' – ' .
                     date('H:i', $mulai + ((int)$layanan['durasi'] * 60)) . '</p>';
            }
            echo '<hr><div class="row total"><span>Total to pay</span>';
            echo '<span>' . self::rupiah($layanan['harga']) . '</span></div></div>';
        }
        echo '</aside>';
    }
}
