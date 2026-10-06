<?php
declare(strict_types=1);

/* =========================================================
 * Konfigurasi Aplikasi - Website & CMS Travel Umroh
 * Salin sebagai app/config.php dan isi kredensial DB lokal.
 * =======================================================*/

// --- Database ---
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'travel_umroh');
define('DB_USER', 'root');
define('DB_PASS', '');

// --- Aplikasi ---
define('APP_NAME', 'Sakinah Journeys');
// '' = di-root domain. Contoh subfolder: '/travel-umroh'
define('BASE_URL', '');

define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('ADMIN_PATH', ROOT_PATH . '/admin');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// --- Upload ---
define('MAX_UPLOAD_BYTES', 2 * 1024 * 1024); // 2MB
define('ALLOWED_MIME', ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp']);

// --- Pagination ---
define('PER_PAGE_PUBLIC', 9);
define('PER_PAGE_ADMIN', 10);