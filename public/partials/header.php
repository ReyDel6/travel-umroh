<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/**
 * Header public — diambil dari design "beranda" dengan navigasi dinamis.
 * Variabel: $currentPage, $profil
 */
$activeGroup = [
    'home'           => 'home',
    'paket'          => 'paket',
    'paket-detail'   => 'paket',
    'artikel'        => 'artikel',
    'artikel-detail' => 'artikel',
    'galeri'         => 'galeri',
    'faq'            => 'faq',
    'kontak'         => 'kontak',
    'tentang'        => 'tentang',
][$currentPage] ?? '';

$navItems = [
    ['label' => 'Beranda',      'href' => url(''),          'key' => 'home'],
    ['label' => 'Paket Umroh',  'href' => url('paket'),     'key' => 'paket'],
    ['label' => 'Jurnal',       'href' => url('artikel'),   'key' => 'artikel'],
    ['label' => 'Galeri',       'href' => url('galeri'),    'key' => 'galeri'],
    ['label' => 'Tentang Kami', 'href' => url('tentang'),   'key' => 'tentang'],
    ['label' => 'FAQ',          'href' => url('faq'),       'key' => 'faq'],
    ['label' => 'Kontak',       'href' => url('kontak'),    'key' => 'kontak'],
];
?>
<header class="fixed top-0 left-0 w-full z-50 bg-surface/90 backdrop-blur-md shadow-[0_1px_8px_rgba(43,37,32,0.04)]">
  <div class="h-20 w-full max-w-7xl mx-auto px-margin lg:px-gutter-desktop flex items-center justify-between gap-space-md">
    <a href="<?= url('') ?>" class="flex items-center gap-space-md shrink-0">
      <img alt="<?= e($profil['nama_perusahaan'] ?? APP_NAME) ?> Logo" class="h-8 w-auto object-contain" src="<?= asset('img/logo.png') ?>"/>
      <span class="hidden sm:flex flex-col">
        <span class="font-headline-sm text-headline-sm text-primary tracking-normal"><?= e($profil['nama_perusahaan'] ?? APP_NAME) ?></span>
        <span class="font-label-sm text-label-sm text-on-surface-variant font-normal"><?= e($profil['tagline'] ?? 'Perjalanan Spiritual yang Teduh') ?></span>
      </span>
    </a>

    <nav class="hidden xl:flex items-center gap-space-lg" id="navDesktop">
      <?php foreach ($navItems as $item): ?>
        <?php $isActive = $item['key'] === $activeGroup; ?>
        <a href="<?= $item['href'] ?>"
           class="<?= $isActive ? 'text-primary font-semibold' : 'font-label-md text-label-md text-on-surface-variant hover:text-primary' ?> transition-colors py-1"
           <?= $isActive ? 'aria-current="page"' : '' ?>><?= e($item['label']) ?></a>
      <?php endforeach; ?>
    </nav>

    <div class="flex items-center gap-space-md shrink-0">
      <div class="hidden md:flex flex-col text-right">
        <span class="font-label-sm text-label-sm text-on-surface-variant">Layanan Concierge</span>
        <span class="font-body-sm text-body-sm font-medium text-tertiary"><?= e($profil['telepon'] ?? '-') ?></span>
      </div>
      <a href="<?= url('kontak') ?>#konsultasi" class="hidden sm:inline-flex items-center justify-center px-space-md py-space-sm bg-secondary-fixed text-on-secondary-fixed font-label-md text-label-md rounded hover:bg-secondary-fixed-dim transition-colors">Jadwalkan Konsultasi</a>
      <button type="button" id="btnMobileMenu" aria-label="Buka menu" aria-expanded="false" class="xl:hidden w-9 h-9 rounded border border-outline-variant/60 flex items-center justify-center text-primary">
        <span class="material-symbols-outlined text-[20px]">menu</span>
      </button>
    </div>
  </div>

  <!-- Menu mobile -->
  <div id="mobileMenu" class="hidden xl:hidden bg-surface border-t border-outline-variant/50">
    <div class="max-w-7xl mx-auto px-margin py-space-sm flex flex-col">
      <?php foreach ($navItems as $item): ?>
        <?php $isActive = $item['key'] === $activeGroup; ?>
        <a href="<?= $item['href'] ?>"
           class="py-2.5 border-b border-outline-variant/30 font-body-md <?= $isActive ? 'text-primary font-semibold' : 'text-on-surface-variant' ?>"><?= e($item['label']) ?></a>
      <?php endforeach; ?>
      <a href="<?= url('kontak') ?>#konsultasi" class="mt-space-sm mb-1 inline-flex items-center justify-center px-space-md py-space-sm bg-secondary-fixed text-on-secondary-fixed font-label-md text-label-md rounded">Jadwalkan Konsultasi</a>
    </div>
  </div>
</header>
<main class="w-full pt-20 bg-surface min-h-[calc(100vh-5rem)]">
<div class="flex flex-col w-full">
<script>
(function () {
  var btn = document.getElementById('btnMobileMenu');
  var menu = document.getElementById('mobileMenu');
  if (btn && menu) {
    btn.addEventListener('click', function () {
      var hidden = menu.classList.toggle('hidden');
      btn.setAttribute('aria-expanded', String(!hidden));
    });
  }
})();
</script>
