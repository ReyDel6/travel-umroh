<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/** Halaman Detail Artikel — design: artikel.html + pola paket-detail.php */
$slug = isset($_GET['slug']) ? (string) $_GET['slug'] : '';
$artikel = $slug !== '' ? model('artikel')->getBySlugPublish($slug) : null;

if (!$artikel) {
    http_response_code(404);
    $page = '404';
    $metaTitle = 'Artikel Tidak Ditemukan — ' . APP_NAME;
    $metaDescription = 'Tulisan yang Anda cari tidak tersedia atau belum dipublikasikan.';
    require PUBLIC_PATH . '/pages/404.php';
    return;
}

$id = (int) $artikel['id'];
$metaTitle = ($artikel['meta_title'] ?? '') ?: ($artikel['judul'] . ' — ' . APP_NAME);
$metaDescription = ($artikel['meta_description'] ?? '') ?: excerpt($artikel['konten'], 160);

$canonicalPath = 'artikel/' . $artikel['slug'];
$modelArtikel = model('artikel');
$terkaitList = $modelArtikel->getTerkait($id, 3);
$menitBaca = max(1, (int) ceil(str_word_count($artikel['konten']) / 180));

require PUBLIC_PATH . '/partials/head.php';
require PUBLIC_PATH . '/partials/header.php';
?>
<!-- Breadcrumb & Status Bar -->
<section class="w-full bg-surface-container-low/70 py-space-sm">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop flex flex-wrap items-center justify-between gap-space-sm">
    <nav aria-label="Breadcrumb" class="flex items-center gap-space-xs font-label-md text-label-md text-on-surface-variant">
      <a class="hover:text-primary transition-colors" href="<?= url('') ?>">Beranda</a>
      <span class="text-outline-variant">/</span>
      <a class="hover:text-primary transition-colors" href="<?= url('artikel') ?>">Jurnal &amp; Panduan Ibadah</a>
      <span class="text-outline-variant">/</span>
      <span class="text-tertiary font-medium max-w-[14rem] sm:max-w-none truncate"><?= e($artikel['judul']) ?></span>
    </nav>
    <div class="flex items-center gap-space-xs">
      <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
      <span class="font-label-sm text-label-sm text-tertiary">
        Terbit <?= tanggal($artikel['created_at'], 'long') ?> &bull; <?= $menitBaca ?> menit baca
      </span>
    </div>
  </div>
</section>

<!-- Editorial Title & Intro Header -->
<header class="w-full py-space-lg lg:py-space-xl bg-surface">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="max-w-4xl flex flex-col gap-space-sm">
      <div class="inline-flex items-center gap-space-xs self-start px-space-sm py-1 rounded-full bg-primary/10 text-primary font-label-sm text-label-sm">
        <span class="material-symbols-outlined text-[15px]">menu_book</span>
        <span> Risalah Manasik &amp; Adab Ibadah</span>
      </div>
      <h1 class="font-display-lg text-display-lg text-on-surface tracking-tight leading-tight"><?= e($artikel['judul']) ?></h1>
      <p class="font-body-md text-body-md text-on-surface-variant max-w-3xl pt-space-xs leading-relaxed">
        <?= e(excerpt($artikel['konten'], 220)) ?>
      </p>
    </div>

    <!-- Editorial Hero Image -->
    <div class="mt-space-lg h-[280px] md:h-[440px] rounded-2xl overflow-hidden relative shadow-sm group bg-surface-container">
      <img alt="<?= e($artikel['judul']) ?>"
           class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
           src="<?= upload_url($artikel['thumbnail'], 'artikel') ?>"/>
      <div class="absolute inset-0 bg-gradient-to-t from-inverse-surface/60 via-transparent to-transparent"></div>
      <div class="absolute bottom-space-md left-space-md right-space-md flex justify-between items-end text-surface gap-2">
        <p class="font-label-sm text-label-sm tracking-widest uppercase opacity-80">
          Terbit <?= tanggal($artikel['created_at']) ?>
        </p>
        <span class="px-space-sm py-0.5 rounded-full bg-surface-container-lowest/20 backdrop-blur-md font-label-sm text-label-sm text-surface text-right">
          <?= $menitBaca ?> menit baca
        </span>
      </div>
    </div>
  </div>
</header>

<!-- Article Body -->
<section class="w-full py-space-xl bg-surface">
  <div class="max-w-3xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
      <?= nl2br(e($artikel['konten'])) ?>
    </div>

    <div class="mt-space-lg pt-space-md border-t border-outline-variant/30 flex flex-wrap items-center justify-between gap-space-sm">
      <span class="font-label-sm text-label-sm text-on-surface-variant">
        Terbit <?= tanggal($artikel['created_at'], 'long') ?> &bull; <?= $menitBaca ?> menit baca
      </span>
      <a class="inline-flex items-center gap-1.5 font-label-md text-label-md text-primary hover:underline underline-offset-4" href="<?= url('artikel') ?>">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali ke Jurnal Ibadah
      </a>
    </div>
  </div>
</section>

<?php if ($terkaitList): ?>
<!-- Related Articles -->
<section class="w-full py-space-xl bg-surface-container-low">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-sm mb-space-md">
      <div>
        <span class="font-label-sm text-label-sm text-primary tracking-widest uppercase">Lanjutan Membaca</span>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mt-1">Tulisan Terkait</h2>
      </div>
      <a class="font-label-md text-label-md text-primary hover:underline underline-offset-4" href="<?= url('artikel') ?>">Lihat seluruh tulisan</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
      <?php foreach ($terkaitList as $t): ?>
      <article class="group bg-surface rounded-xl overflow-hidden shadow-sm flex flex-col transition-all duration-300 hover:shadow-md">
        <a class="block h-44 overflow-hidden bg-surface-container-low" href="<?= url('artikel/' . $t['slug']) ?>">
          <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
               alt="<?= e($t['judul']) ?>"
               src="<?= upload_url($t['thumbnail'], 'artikel') ?>"/>
        </a>
        <div class="p-space-md flex-1 flex flex-col justify-between gap-space-sm">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="font-label-sm text-label-sm text-primary font-semibold"><?= tanggal($t['created_at']) ?></span>
              <span class="text-tertiary-fixed-dim text-[10px]">&bull;</span>
              <span class="font-label-sm text-label-sm text-on-surface-variant"><?= max(1, (int) ceil(str_word_count($t['konten']) / 180)) ?> mnt baca</span>
            </div>
            <h3 class="font-headline-md text-headline-md text-on-surface font-normal group-hover:text-primary transition-colors leading-tight">
              <a href="<?= url('artikel/' . $t['slug']) ?>"><?= e($t['judul']) ?></a>
            </h3>
          </div>
          <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed line-clamp-3">
            <?= e(excerpt($t['konten'], 180)) ?>
          </p>
          <a class="inline-flex items-center gap-1 font-label-sm text-label-sm text-primary font-medium hover:underline underline-offset-4" href="<?= url('artikel/' . $t['slug']) ?>">
            Baca tulisan ini
            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Consultation Concierge Banner -->
<section class="w-full py-space-xl bg-surface-container" id="konsultasi">
  <div class="max-w-4xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="p-space-md lg:p-space-lg rounded-2xl bg-surface shadow-sm flex flex-col lg:flex-row items-start lg:items-center justify-between gap-space-md">
      <div class="max-w-2xl">
        <div class="flex items-center gap-2 text-secondary mb-1">
          <span class="material-symbols-outlined text-[20px]">support_agent</span>
          <span class="font-label-sm text-label-sm font-semibold tracking-wide">Layanan Bimbingan Privat</span>
        </div>
        <h2 class="font-headline-sm text-headline-sm text-primary mb-2 font-normal leading-snug">
          Konsultasikan Manasik Khusus Keluarga Anda
        </h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
          Punya pertanyaan seputar fidyah orang tua, rukhshah shalat, atau pengaturan kursi roda selama sa'i?
          Tim pembimbing siap berdiskusi secara tenang.
        </p>
      </div>
      <a class="shrink-0 inline-flex items-center justify-center px-space-md py-space-sm bg-secondary-fixed text-on-secondary-fixed rounded font-label-md text-label-md hover:bg-secondary-fixed-dim transition-colors" href="<?= url('kontak') ?>#konsultasi">
        Jadwalkan Temu Konsultasi
      </a>
    </div>
  </div>
</section>
<?php require PUBLIC_PATH . '/partials/footer.php'; ?>
