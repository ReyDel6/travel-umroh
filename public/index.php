<?php
/**
 * Front controller public website.
 * Routing: root/.htaccess (Apache) atau router.php (php -S)
 * Parameter: ?page=&slug=
 */
require_once dirname(__DIR__) . '/app/bootstrap.php';

$page = $_GET['page'] ?? 'home';
$slug = isset($_GET['slug']) ? (string) $_GET['slug'] : '';

$allowedPages = [
    'home', 'paket', 'paket-detail', 'galeri', 'artikel', 'artikel-detail',
    'faq', 'kontak', 'tentang', '404',
];

if (!in_array($page, $allowedPages, true)) {
    $page = '404';
}
// Status 404 untuk halaman "tidak ditemukan" (entah dari whitelist maupun catch-all rewrite)
if ($page === '404') {
    http_response_code(404);
}

$profil = model('profil')->get();

// Analitik traffic publik (Spec: docs/SPEC-TRAFFIC-ANALYTICS.md) — gagal tak pernah menggagalkan render.
log_visitor();

// Default meta (bisa dioverride tiap halaman)
$metaTitle = ($profil['meta_title'] ?? APP_NAME) ?: APP_NAME;
$metaDescription = $profil['meta_description'] ?? '';
$currentPage = $page;

require PUBLIC_PATH . '/pages/' . $page . '.php';
