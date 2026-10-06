<?php
/**
 * Router untuk PHP built-in server (development).
 *
 * Jalankan dari root proyek:
 *   php -S localhost:8000 router.php
 *
 * URL cantik:
 *   /                 -> public/index.php?page=home
 *   /paket            -> public/index.php?page=paket
 *   /paket/<slug>     -> public/index.php?page=paket-detail&slug=...
 *   /artikel/<slug>   -> public/index.php?page=artikel-detail&slug=...
 *   /admin/...        -> file admin/*.php (langsung)
 */

$uri = urldecode((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$file = __DIR__ . $uri;

// Blokir akses publik ke file internal (padanan aturan .htaccess di root)
$blocked = '#^(/(app|database|docs|design)/|/admin/partials/|/admin/[^/]+\.html$|/public/(pages|partials)/|/router\.php$|/AGENTS\.MD$)#i';
if (preg_match($blocked, $uri)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Not Found';
    return true;
}

// File statis (aset, uploads, file admin) diserahkan ke server
if ($uri !== '/' && is_file($file)) {
    return false;
}

// Folder (mis. /admin/) -> index.php di dalamnya
if ($uri !== '/' && is_dir($file) && is_file($file . '/index.php')) {
    require $file . '/index.php';
    return true;
}

$path = trim($uri, '/');

// Permintaan eksplisit via query string (?page=...&slug=...) tetap dihormati
if (isset($_GET['page']) && $_GET['page'] !== '') {
    require __DIR__ . '/public/index.php';
    return true;
}

// Rute tanpa slug
$routes = [
    ''           => 'home',
    'paket'      => 'paket',
    'galeri'     => 'galeri',
    'artikel'    => 'artikel',
    'faq'        => 'faq',
    'kontak'     => 'kontak',
    'tentang'    => 'tentang',
];

if (isset($routes[$path])) {
    $_GET['page'] = $routes[$path];
    require __DIR__ . '/public/index.php';
    return true;
}

// Rute dengan slug
if (preg_match('#^(paket|artikel)/([a-zA-Z0-9\-]+)$#', $path, $m)) {
    $_GET['page'] = $m[1] === 'paket' ? 'paket-detail' : 'artikel-detail';
    $_GET['slug'] = $m[2];
    require __DIR__ . '/public/index.php';
    return true;
}

// Varian query string eksplisit: /paket-detail?slug=... & /artikel-detail?slug=...
if (in_array($path, ['paket-detail', 'artikel-detail'], true) && isset($_GET['slug']) && $_GET['slug'] !== '') {
    $_GET['page'] = $path;
    require __DIR__ . '/public/index.php';
    return true;
}

// Selain itu: 404
$_GET['page'] = '404';
http_response_code(404);
require __DIR__ . '/public/index.php';
return true;
