<?php
declare(strict_types=1);

/* =========================================================
 * Bootstrap: muat konfigurasi, helper, model
 * Di-require di awal setiap entry point (public & admin)
 * =======================================================*/

require_once __DIR__ . '/config.php';

// Lindungi output dari error PHP apa pun (jalankan sebelum modul lain)
ini_set('display_errors', '0');
error_reporting(E_ALL);
set_exception_handler(static function (Throwable $e): void {
    http_response_code(500);
    error_log(sprintf('[unhandled] %s in %s:%d', $e->getMessage(), $e->getFile(), $e->getLine()));
    $judul = 'Terjadi Kesalahan Server';
    $pesan = 'Permintaan tidak dapat diproses. Silakan coba lagi beberapa saat, atau hubungi tim kami jika masalah berlanjut.';
    $diAdmin = isset($_SERVER['SCRIPT_NAME']) && str_contains((string) $_SERVER['SCRIPT_NAME'], '/admin/');
    $tujuan  = $diAdmin ? url('admin/dashboard.php') : url('');
    $tombol  = $diAdmin ? 'Kembali ke Dashboard' : 'Kembali ke Beranda';
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=utf-8');
    }
    echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>' . htmlspecialchars($judul, ENT_QUOTES, 'UTF-8') . '</title>'
        . '<style>body{font-family:system-ui,-apple-system,sans-serif;background:#F2EEE6;color:#2B2520;display:flex;min-height:100vh;align-items:center;justify-content:center;margin:0}'
        . 'main{max-width:32rem;padding:2rem;text-align:center}h1{font-size:1.5rem;color:#1F5C56}p{line-height:1.6;color:#5C524A}'
        . 'a{display:inline-block;margin-top:1rem;padding:.6rem 1.25rem;background:#1F5C56;color:#FAF7F2;text-decoration:none;border-radius:.25rem}</style></head>'
        . '<body><main><h1>' . htmlspecialchars($judul, ENT_QUOTES, 'UTF-8') . '</h1><p>' . htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<a href="' . htmlspecialchars($tujuan, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($tombol, ENT_QUOTES, 'UTF-8') . '</a></main></body></html>';
    exit;
});

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/upload.php';
require_once __DIR__ . '/validation.php';

require_once __DIR__ . '/models/PaketModel.php';
require_once __DIR__ . '/models/JadwalModel.php';
require_once __DIR__ . '/models/FasilitasModel.php';
require_once __DIR__ . '/models/HotelModel.php';
require_once __DIR__ . '/models/MaskapaiModel.php';
require_once __DIR__ . '/models/GaleriModel.php';
require_once __DIR__ . '/models/ArtikelModel.php';
require_once __DIR__ . '/models/FaqModel.php';
require_once __DIR__ . '/models/InquiryModel.php';
require_once __DIR__ . '/models/ProfilModel.php';
require_once __DIR__ . '/models/AdminModel.php';
require_once __DIR__ . '/models/MetaHalamanModel.php';
require_once __DIR__ . '/models/TestimoniModel.php';
