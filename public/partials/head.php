<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/**
 * <head> bersama seluruh halaman public — design system "Serene Sanctuary Umrah"
 * Variabel: $metaTitle, $metaDescription, ($canonicalPath opsional, path relatif tanpa base)
 */
$metaTitle = $metaTitle ?? APP_NAME;
$metaDescription = $metaDescription ?? '';

// Canonical: halaman ber-slug menetapkan $canonicalPath; lainnya pakai path request.
// Path query-string/rute internal dipetakan ke URL cantik agar tidak ada duplikat konten.
$canonicalPath = $canonicalPath ?? (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
if ($canonicalPath === '' || in_array($canonicalPath, ['/index.php', '/home', '/paket-detail', '/artikel-detail'], true)) {
    $canonicalPath = '/';
}
$canonicalUrl = full_url($canonicalPath);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= e($metaTitle) ?></title>
<meta name="description" content="<?= e($metaDescription) ?>"/>
<meta property="og:title" content="<?= e($metaTitle) ?>"/>
<meta property="og:description" content="<?= e($metaDescription) ?>"/>
<meta property="og:type" content="website"/>
<meta property="og:url" content="<?= e($canonicalUrl) ?>"/>
<meta property="og:image" content="<?= asset('img/logo.png') ?>"/>
<link rel="canonical" href="<?= e($canonicalUrl) ?>"/>
<link rel="icon" href="<?= asset('img/logo.png') ?>"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..700;1,400..700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}</style>
<script src="https://cdn.tailwindcss.com"></script>
<script src="<?= url('public/assets/tailwind-config.js') ?>"></script>
</head>
<body class="bg-surface font-body-md text-body-md text-on-surface antialiased selection:bg-secondary-fixed selection:text-on-secondary-fixed">