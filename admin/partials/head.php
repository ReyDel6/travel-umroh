<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/**
 * <head> bersama seluruh halaman CMS admin — design system "Serene Sanctuary Umrah"
 * Variabel: $metaTitle (opsional)
 */
$metaTitle = $metaTitle ?? ('CMS ' . APP_NAME);
$bodyClass = $bodyClass ?? 'bg-surface font-body-md text-body-md text-on-surface antialiased selection:bg-secondary-fixed selection:text-on-secondary-fixed';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="robots" content="noindex, nofollow"/>
<title><?= e($metaTitle) ?></title>
<link rel="icon" href="<?= asset('img/logo.png') ?>"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..700;1,400..700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}</style>
<script src="https://cdn.tailwindcss.com"></script>
<script src="<?= url('public/assets/tailwind-config.js') ?>"></script>
</head>
<body class="<?= e($bodyClass) ?>">
