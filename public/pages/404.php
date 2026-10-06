<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/** Halaman 404 — konsisten dengan design system Serene Sanctuary */
$metaTitle = 'Halaman Tidak Ditemukan — ' . APP_NAME;
$metaDescription = 'Halaman yang Anda tuju tidak tersedia.';

require PUBLIC_PATH . '/partials/head.php';
require PUBLIC_PATH . '/partials/header.php';
?>
<section class="w-full bg-surface py-space-xl lg:py-space-xl min-h-[60vh] flex items-center">
  <div class="max-w-3xl mx-auto px-margin lg:px-gutter-desktop text-center">
    <span class="font-display-lg text-display-lg text-primary/25 select-none leading-none block">404</span>
    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary mx-auto -mt-space-sm">
      <span class="material-symbols-outlined text-[32px]">explore_off</span>
    </div>
    <h1 class="font-headline-lg text-headline-lg text-on-surface mt-space-md">Halaman ini belum tersedia</h1>
    <p class="font-body-md text-body-md text-on-surface-variant mt-space-xs max-w-xl mx-auto">
      Tautan yang Anda tuju mungkin telah dipindahkan atau belum pernah ada. Silakan kembali ke beranda atau telusuri
      paket ibadah kami.
    </p>
    <div class="mt-space-lg flex flex-col sm:flex-row items-center justify-center gap-space-sm">
      <a class="inline-flex items-center justify-center px-space-md py-space-sm bg-primary text-on-primary font-label-md text-label-md rounded hover:bg-primary-container transition-colors" href="<?= url('') ?>">Kembali ke Beranda</a>
      <a class="inline-flex items-center justify-center px-space-md py-space-sm border border-primary text-primary font-label-md text-label-md rounded hover:bg-primary/5 transition-colors" href="<?= url('paket') ?>">Lihat Paket Ibadah</a>
    </div>
  </div>
</section>
<?php require PUBLIC_PATH . '/partials/footer.php'; ?>
