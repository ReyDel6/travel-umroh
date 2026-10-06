<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/** Halaman Daftar Artikel — design: artikel.html */
$metaTitle = 'Jurnal Manasik, Adab & Catatan Perjalanan — ' . APP_NAME;
$metaDescription = 'Kumpulan tulisan asatidzah dan dokter kontingen mengenai persiapan batin, adab di Tanah Suci, serta ikhtiar menjaga kesehatan keluarga selama beribadah.';

$cari = isset($_GET['cari']) ? trim((string) $_GET['cari']) : '';
if (mb_strlen($cari) > 100) {
    $cari = mb_substr($cari, 0, 100);
}
if ($cari !== '') {
    $metaTitle = 'Cari: ' . $cari . ' — ' . APP_NAME;
}

$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
if ($halaman < 1) {
    $halaman = 1;
}

$perPage = 9;

$modelArtikel = model('artikel');
$total = $modelArtikel->countPublish($cari);

// Hero memakai artikel terbaru; daftar feed menampilkan sisanya agar tidak ganda.
$featured = ($cari === '') ? $modelArtikel->getFeatured() : null;
$heroOffset = $featured ? 1 : 0;
$totalFeed = max(0, $total - $heroOffset);

$totalPages = (int) ceil($totalFeed / $perPage);
if ($halaman > max(1, $totalPages)) {
    $halaman = max(1, $totalPages);
}

$rows = $totalFeed > 0
    ? $modelArtikel->getPublish($perPage, $heroOffset + (($halaman - 1) * $perPage), $cari)
    : [];

$dari = $rows ? $heroOffset + (($halaman - 1) * $perPage) + 1 : 0;
$sampai = $heroOffset + (($halaman - 1) * $perPage) + count($rows);

$terbaru = [];
foreach ($modelArtikel->getPublish(5) as $t) {
    if ($featured && (int) $t['id'] === (int) $featured['id']) {
        continue;
    }
    $terbaru[] = $t;
    if (count($terbaru) >= 4) {
        break;
    }
}

meta_halaman_apply('artikel', $metaTitle, $metaDescription);

require PUBLIC_PATH . '/partials/head.php';
require PUBLIC_PATH . '/partials/header.php';
?>
<!-- Top Editorial Header & Breadcrumb Context -->
<section class="w-full bg-surface pt-space-md pb-space-lg">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <!-- Breadcrumb with subtle typographic cadence -->
    <nav aria-label="Navigasi Jejak" class="flex items-center gap-space-xs text-on-surface-variant font-label-md text-label-md pb-space-md">
      <a class="hover:text-primary transition-colors" href="<?= url('') ?>">Beranda</a>
      <span class="text-tertiary-fixed-dim select-none">/</span>
      <span class="text-primary font-medium">Jurnal &amp; Panduan Ibadah</span>
    </nav>
    <div class="max-w-3xl">
      <span class="inline-block font-label-sm text-label-sm text-secondary font-semibold tracking-wider pb-2">Catatan Reflektif &amp; Risalah Manasik</span>
      <h1 class="font-headline-lg text-headline-lg lg:font-display-md lg:text-display-md text-tertiary font-normal tracking-tight leading-snug">
        Jurnal Manasik, Adab &amp; Catatan Perjalanan
      </h1>
      <p class="font-body-lg text-body-lg text-on-surface-variant pt-space-xs leading-relaxed max-w-2xl font-light">
        Kumpulan tulisan asatidzah dan dokter kontingen mengenai persiapan batin, adab di Tanah Suci, serta ikhtiar menjaga kesehatan keluarga selama beribadah.
      </p>
    </div>
  </div>
</section>

<?php if ($featured): $f = $featured; ?>
<!-- Featured Editorial Article (Lead Story) -->
<section class="w-full bg-surface pb-space-xl">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <article class="bg-surface-container-low rounded-xl p-space-md lg:p-space-lg transition-all duration-300 hover:bg-surface-container">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-center">
        <!-- Narrative Left Column -->
        <div class="lg:col-span-7 flex flex-col justify-between h-full">
          <div>
            <div class="flex items-center gap-3 mb-space-xs">
              <span class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary font-label-md text-label-md font-medium">
                Risalah Utama
              </span>
              <span class="font-label-sm text-label-sm text-on-surface-variant"><?= tanggal($f['created_at'], 'long') ?></span>
            </div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface font-normal leading-tight mb-space-sm hover:text-primary transition-colors">
              <a href="<?= url('artikel/' . $f['slug']) ?>"><?= e($f['judul']) ?></a>
            </h2>
            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed max-w-xl mb-space-md">
              <?= e(excerpt($f['konten'], 300)) ?>
            </p>
          </div>
          <!-- Date & reading signature block -->
          <div class="flex items-center justify-between pt-space-sm">
            <div class="flex items-center gap-space-sm">
              <div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center text-primary font-headline-sm text-headline-sm">
                <span class="material-symbols-outlined text-[20px]">menu_book</span>
              </div>
              <div>
                <p class="font-label-md text-label-md text-tertiary font-semibold">Redaksi Jurnal Ibadah</p>
                <p class="font-label-sm text-label-sm text-on-surface-variant"><?= max(1, (int) ceil(str_word_count($f['konten']) / 180)) ?> mnt baca</p>
              </div>
            </div>
            <a class="font-label-sm text-label-sm text-primary font-medium hover:underline underline-offset-4 hidden sm:inline-block" href="<?= url('artikel/' . $f['slug']) ?>">
              Baca Selengkapnya
            </a>
          </div>
        </div>
        <!-- Editorial Imagery Right Column -->
        <div class="lg:col-span-5 h-72 sm:h-80 lg:h-96 rounded-lg overflow-hidden bg-surface-container">
          <a class="block w-full h-full" href="<?= url('artikel/' . $f['slug']) ?>">
            <img class="w-full h-full object-cover transition-transform duration-700 hover:scale-105"
                 alt="<?= e($f['judul']) ?>"
                 src="<?= upload_url($f['thumbnail'], 'artikel') ?>"/>
          </a>
        </div>
      </div>
    </article>
  </div>
</section>
<?php endif; ?>

<!-- Editorial Main Feed & Contextual Sidebar -->
<section class="w-full bg-surface pb-space-xl">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg lg:gap-gutter-desktop items-start">
      <!-- Left / Primary Column (List of Reflective Articles) -->
      <div class="lg:col-span-8 flex flex-col">
        <!-- Search Quick Control -->
        <form action="<?= url('artikel') ?>" class="flex items-center gap-2 pb-space-sm mb-space-md text-on-surface-variant" method="get">
          <label class="relative flex items-center flex-1 min-w-0">
            <span class="sr-only">Cari tulisan</span>
            <input class="w-full bg-surface py-2.5 pl-3.5 pr-10 rounded font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant/60 focus:outline-none focus:ring-1 focus:ring-primary border border-outline-variant/40"
                   name="cari" placeholder="Cari risalah batin, doa, bekal obat..." type="search" value="<?= e($cari) ?>"/>
            <span class="material-symbols-outlined text-on-surface-variant absolute right-3 pointer-events-none text-[20px]">search</span>
          </label>
          <button class="px-space-sm py-2.5 rounded bg-primary text-on-primary font-label-md text-label-md shrink-0 transition-colors hover:bg-primary-container" type="submit">
            Telusuri
          </button>
          <?php if ($cari !== ''): ?>
          <a class="px-space-sm py-2.5 rounded bg-surface-container-low hover:bg-surface-container-high text-on-surface font-label-md text-label-md shrink-0 transition-colors" href="<?= url('artikel') ?>">
            Atur Ulang
          </a>
          <?php endif; ?>
        </form>

        <?php if ($cari !== ''): ?>
        <p class="font-label-sm text-label-sm text-on-surface-variant mb-space-sm">
          Hasil pencarian untuk <span class="text-tertiary font-semibold">&ldquo;<?= e($cari) ?>&rdquo;</span>
          &bull; <?= (int) $total ?> tulisan ditemukan
        </p>
        <?php endif; ?>

        <!-- Vertical Editorial Article Stack -->
        <div class="flex flex-col">
          <?php if (!$rows && $cari !== ''): ?>
          <div class="p-space-lg text-center bg-surface-container rounded-xl">
            <span class="material-symbols-outlined text-outline text-[40px] mb-2">search_off</span>
            <h4 class="font-headline-sm text-headline-sm text-tertiary">Tidak Ada Tulisan yang Sesuai</h4>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 max-w-md mx-auto">
              Coba kata kunci lain, atau telusuri kembali seluruh tulisan manasik kami.
            </p>
            <a class="inline-flex items-center justify-center px-space-md py-space-sm mt-space-md rounded bg-primary text-on-primary font-label-md text-label-md hover:bg-primary-container transition-colors" href="<?= url('artikel') ?>">
              Lihat Semua Tulisan
            </a>
          </div>
          <?php elseif (!$rows): ?>
          <div class="p-space-lg text-center bg-surface-container rounded-xl">
            <span class="material-symbols-outlined text-outline text-[40px] mb-2">article</span>
            <h4 class="font-headline-sm text-headline-sm text-tertiary">Belum Ada Tulisan yang Dipublikasikan</h4>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 max-w-md mx-auto">
              Jurnal manasik kami sedang disusun. Silakan hubungi pembimbing untuk panduan keberangkatan Anda.
            </p>
          </div>
          <?php endif; ?>

          <?php foreach ($rows as $r): ?>
          <!-- Article Item -->
          <article class="group py-space-md first:pt-0">
            <div class="flex flex-col sm:flex-row gap-space-md items-start">
              <a class="w-full sm:w-44 h-48 sm:h-32 shrink-0 rounded-lg overflow-hidden bg-surface-container-low" href="<?= url('artikel/' . $r['slug']) ?>">
                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                     alt="<?= e($r['judul']) ?>"
                     src="<?= upload_url($r['thumbnail'], 'artikel') ?>"/>
              </a>
              <div class="flex flex-col justify-between flex-1">
                <div>
                  <div class="flex items-center gap-2 mb-1.5">
                    <span class="font-label-sm text-label-sm text-primary font-semibold"><?= tanggal($r['created_at']) ?></span>
                    <span class="text-tertiary-fixed-dim text-[10px]">&bull;</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant"><?= max(1, (int) ceil(str_word_count($r['konten']) / 180)) ?> mnt baca</span>
                  </div>
                  <h3 class="font-headline-md text-headline-md text-on-surface font-normal group-hover:text-primary transition-colors leading-tight mb-2">
                    <a href="<?= url('artikel/' . $r['slug']) ?>"><?= e($r['judul']) ?></a>
                  </h3>
                  <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed max-w-xl line-clamp-2 sm:line-clamp-none">
                    <?= e(excerpt($r['konten'], 220)) ?>
                  </p>
                </div>
                <div class="flex items-center gap-2 pt-space-xs text-on-surface-variant font-label-sm text-label-sm">
                  <span class="font-medium text-tertiary">Jurnal Ibadah</span>
                  <span>&bull;</span>
                  <a class="text-primary hover:underline underline-offset-4" href="<?= url('artikel/' . $r['slug']) ?>">Baca selengkapnya</a>
                </div>
              </div>
            </div>
            <div class="w-full h-px bg-surface-container-highest mt-space-md">
            </div>
          </article>
          <?php endforeach; ?>
        </div>

        <!-- Editorial Pagination -->
        <?php if ($rows): ?>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-space-sm pt-space-lg">
          <span class="font-label-sm text-label-sm text-on-surface-variant">
            Menampilkan <?= (int) $dari ?>&ndash;<?= (int) $sampai ?> dari <?= (int) $totalFeed ?> tulisan manasik
          </span>
        </div>
        <?php endif; ?>
        <?= pagination($totalFeed, $perPage, $halaman, 'artikel', ['cari' => $cari]) ?>
      </div>

      <!-- Right Column / Sidebar Editorial -->
      <aside class="lg:col-span-4 flex flex-col gap-space-lg w-full">
        <!-- Latest Articles Dossier -->
        <div class="bg-surface-container-low p-space-md rounded-xl">
          <div class="flex items-center justify-between mb-space-sm">
            <h4 class="font-headline-sm text-headline-sm text-tertiary font-normal">Tulisan Terbaru</h4>
            <a class="font-label-sm text-label-sm text-primary hover:underline underline-offset-4" href="<?= url('artikel') ?>">Semua</a>
          </div>
          <div class="flex flex-col">
            <?php if (!$terbaru): ?>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Belum ada tulisan lain untuk ditampilkan.</p>
            <?php endif; ?>
            <?php foreach ($terbaru as $t): ?>
            <a class="group flex items-start gap-3 py-space-xs border-b border-outline-variant/20 last:border-b-0" href="<?= url('artikel/' . $t['slug']) ?>">
              <div class="w-14 h-14 rounded overflow-hidden bg-surface-container-high shrink-0">
                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                     alt="<?= e($t['judul']) ?>"
                     src="<?= upload_url($t['thumbnail'], 'artikel') ?>"/>
              </div>
              <div class="flex flex-col min-w-0">
                <span class="font-body-sm text-body-sm text-on-surface font-medium leading-snug group-hover:text-primary transition-colors line-clamp-2"><?= e($t['judul']) ?></span>
                <span class="font-label-sm text-label-sm text-on-surface-variant mt-0.5"><?= tanggal($t['created_at']) ?></span>
              </div>
            </a>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Direct Consultation Concierge Banner -->
        <div class="p-space-md rounded-xl bg-surface-container text-on-surface flex flex-col justify-between">
          <div>
            <div class="flex items-center gap-2 text-secondary mb-1">
              <span class="material-symbols-outlined text-[20px]">support_agent</span>
              <span class="font-label-sm text-label-sm font-semibold tracking-wide">Layanan Bimbingan Privat</span>
            </div>
            <h5 class="font-headline-sm text-headline-sm text-primary mb-2 font-normal leading-snug">
              Konsultasikan Manasik Khusus Keluarga Anda
            </h5>
            <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed mb-space-md">
              Punya pertanyaan seputar fidyah orang tua, rukhshah shalat, atau pengaturan kursi roda selama sa'i? Tim pembimbing siap berdiskusi secara tenang.
            </p>
          </div>
          <a class="inline-flex items-center justify-center w-full py-2.5 px-space-md bg-secondary-fixed text-on-secondary-fixed rounded font-label-md text-label-md hover:bg-secondary-fixed-dim transition-colors" href="<?= url('kontak') ?>#konsultasi">
            Jadwalkan Temu Konsultasi
          </a>
        </div>
      </aside>
    </div>
  </div>
</section>

<!-- Editorial Quote & Peaceful Reflection Footnote -->
<section class="w-full bg-surface-container-low py-space-xl">
  <div class="max-w-4xl mx-auto px-margin lg:px-gutter-desktop text-center">
    <span class="material-symbols-outlined text-secondary text-[32px] mb-2 opacity-80">format_quote</span>
    <blockquote class="font-headline-md text-headline-md lg:font-headline-lg lg:text-headline-lg text-tertiary font-normal italic leading-relaxed max-w-2xl mx-auto">
      &ldquo;Bukan kemewahan jarak yang menenteramkan musafir, melainkan keikhlasan meletakkan kesombongan sebelum memasuki pelataran suci-Nya.&rdquo;
    </blockquote>
    <p class="font-label-md text-label-md text-on-surface-variant mt-space-sm">
      &mdash; Catatan Redaksi Jurnal Ibadah
    </p>
  </div>
</section>
<?php require PUBLIC_PATH . '/partials/footer.php'; ?>
