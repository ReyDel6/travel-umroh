<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/** Halaman Bantuan & FAQ — design: faq.html */
$metaTitle = 'Pertanyaan yang Sering Diajukan — ' . APP_NAME;
$metaDescription = 'Jawaban seputar persiapan fisik, bimbingan manasik, fasilitas pendampingan lansia, serta transparansi biaya ibadah Anda sekeluarga.';

$faqList = model('faq')->getPublic();

$faqCategories = [
    'biaya'     => [
        'label'    => 'Pendaftaran &amp; Biaya',
        'keywords' => ['biaya', 'bayar', 'pembayaran', 'harga', 'termin', 'tanda jadi', 'angsuran', 'pelunasan', 'pembatalan', 'batal', 'pengembalian', 'refund', 'pendaftaran', 'mendaftar', 'visa', 'paspor', 'dokumen', 'jadwal', 'keberangkatan', 'tiket'],
    ],
    'kesehatan' => [
        'label'    => 'Kesehatan &amp; Lansia',
        'keywords' => ['kesehatan', 'lansia', 'lanjut usia', 'sakit', 'dokter', 'kursi roda', 'mobilitas', 'obat', 'riwayat', 'pantangan', 'darah', 'vaksin', 'medis', 'kesehat'],
    ],
    'akomodasi' => [
        'label'    => 'Akomodasi &amp; Fasilitas',
        'keywords' => ['hotel', 'kamar', 'akomodasi', 'fasilitas', 'konsumsi', 'makan', 'quad', 'triple', 'double', 'jarak', 'pelataran', 'ring 1', 'connecting', 'shuttle'],
    ],
    'manasik'   => [
        'label'    => 'Manasik &amp; Pembimbing',
        'keywords' => ['manasik', 'bimbingan', 'pembimbing', 'asatidzah', 'muthawwif', 'ustadz', 'ustaz', "ta'lim", 'kurikulum', 'sesi', 'pemandu'],
    ],
];

$faqItems = [];
$faqUsed  = [];
foreach ($faqList as $row) {
    $haystack = mb_strtolower($row['pertanyaan'] . ' ' . (string) $row['jawaban']);
    $cat = 'lainnya';
    $best = 0;
    foreach ($faqCategories as $key => $def) {
        $score = 0;
        foreach ($def['keywords'] as $kw) {
            $pos = 0;
            while (($pos = mb_strpos($haystack, $kw, $pos)) !== false) {
                $score++;
                $pos += mb_strlen($kw);
            }
        }
        if ($score > $best) {
            $best = $score;
            $cat = $key;
        }
    }
    $faqUsed[$cat] = ($faqUsed[$cat] ?? 0) + 1;
    $faqItems[] = ['row' => $row, 'category' => $cat];
}

meta_halaman_apply('faq', $metaTitle, $metaDescription);

require PUBLIC_PATH . '/partials/head.php';
require PUBLIC_PATH . '/partials/header.php';
?>
<!-- Section 1: Editorial Header -->
<section class="w-full bg-surface">
  <div class="w-full max-w-7xl mx-auto px-margin lg:px-gutter-desktop py-space-lg lg:py-space-xl">
    <nav aria-label="Breadcrumb" class="flex items-center gap-2 mb-space-md text-on-surface-variant font-label-md text-label-md">
      <a class="hover:text-primary transition-colors" href="<?= url('') ?>">Beranda</a>
      <span class="text-surface-dim font-light">/</span>
      <span class="text-tertiary font-medium">Bantuan &amp; FAQ</span>
    </nav>

    <header class="max-w-3xl mb-space-xl">
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-surface-container-high text-primary font-label-sm text-label-sm mb-space-sm">
        <span>Pusat Informasi Jamaah</span>
      </div>
      <h1 class="font-headline-lg text-headline-lg lg:font-display-md lg:text-display-md text-primary tracking-normal mb-space-sm">
        Pertanyaan yang Sering Diajukan
      </h1>
      <p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">
        Jawaban seputar persiapan fisik, bimbingan manasik, fasilitas pendampingan lansia, serta transparansi biaya ibadah Anda sekeluarga.
      </p>
    </header>

    <!-- Main Content Area: Left-aligned, Focused Single Column -->
    <div class="max-w-[820px]">
      <?php if (!$faqItems): ?>
      <!-- Empty State -->
      <div class="p-space-lg text-center bg-surface-container rounded">
        <span class="material-symbols-outlined text-outline text-[40px] mb-2">help</span>
        <h2 class="font-headline-sm text-headline-sm text-tertiary">Belum Ada Pertanyaan yang Dipublikasikan</h2>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1 max-w-md mx-auto">
          Daftar pertanyaan umum sedang kami susun. Tim concierge dengan senang hati menjawab langsung setiap kebutuhan ibadah Anda.
        </p>
        <a class="inline-flex items-center justify-center px-6 py-3 mt-space-md rounded bg-primary text-on-primary font-label-md text-label-md hover:bg-primary-container transition-colors shadow-sm" href="<?= url('kontak') ?>#konsultasi">
          Hubungi Layanan Concierge
        </a>
      </div>
      <?php else: ?>
      <!-- Filter Category Pills -->
      <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-space-lg no-scrollbar" id="faq-filter-group">
        <button class="faq-filter-btn px-4 py-2 rounded font-label-md text-label-md transition-colors bg-primary text-on-primary" data-filter="all" type="button">
          Semua
        </button>
        <?php foreach ($faqCategories as $key => $def): if (($faqUsed[$key] ?? 0) < 1) { continue; } ?>
        <button class="faq-filter-btn px-4 py-2 rounded font-label-md text-label-md transition-colors bg-surface-container text-on-surface-variant hover:bg-surface-container-high" data-filter="<?= e($key) ?>" type="button">
          <?= $def['label'] ?> <span class="opacity-60">(<?= (int) $faqUsed[$key] ?>)</span>
        </button>
        <?php endforeach; ?>
        <?php if (($faqUsed['lainnya'] ?? 0) > 0): ?>
        <button class="faq-filter-btn px-4 py-2 rounded font-label-md text-label-md transition-colors bg-surface-container text-on-surface-variant hover:bg-surface-container-high" data-filter="lainnya" type="button">
          Lainnya <span class="opacity-60">(<?= (int) $faqUsed['lainnya'] ?>)</span>
        </button>
        <?php endif; ?>
      </div>

      <!-- FAQ Accordion List -->
      <div class="flex flex-col gap-3" id="faq-accordion-container">
        <?php foreach ($faqItems as $item): $q = $item['row']; ?>
        <article class="faq-item rounded bg-surface-container-low transition-all duration-200" data-category="<?= e($item['category']) ?>">
          <button aria-expanded="false" class="faq-trigger w-full py-5 px-6 flex items-start justify-between text-left gap-4" type="button">
            <span class="font-headline-sm text-headline-sm text-tertiary">
              <?= e($q['pertanyaan']) ?>
            </span>
            <span aria-hidden="true" class="faq-icon font-display-md-mobile text-display-md-mobile leading-none text-tertiary shrink-0 select-none ml-2 pt-0.5">+</span>
          </button>
          <div class="faq-content hidden px-6 pb-6 pt-1 font-body-md text-body-md text-on-surface-variant">
            <?= nl2br(e((string) $q['jawaban'])) ?>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Callout Box (Minimalist, Warm & Functional) -->
      <section class="mt-space-xl p-8 rounded bg-surface-container flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-1 max-w-lg">
          <h3 class="font-headline-sm text-headline-sm text-tertiary">
            Tidak menemukan jawaban yang Anda cari?
          </h3>
          <p class="font-body-md text-body-md text-on-surface-variant">
            Tim concierge keluarga dan asatidzah kami siap berbincang santai untuk menjelaskan setiap kebutuhan khusus ibadah Anda.
          </p>
        </div>
        <div class="shrink-0">
          <a class="inline-flex items-center justify-center px-6 py-3 rounded bg-primary text-on-primary font-label-md text-label-md hover:bg-primary-container transition-colors shadow-sm" href="<?= url('kontak') ?>#konsultasi">
            Hubungi Layanan Concierge
          </a>
        </div>
      </section>
    </div>
  </div>
</section>

<script>
(function () {
  var accordionItems = document.querySelectorAll('.faq-item');
  accordionItems.forEach(function (item) {
    var trigger = item.querySelector('.faq-trigger');
    var content = item.querySelector('.faq-content');
    var icon = item.querySelector('.faq-icon');
    if (!trigger || !content || !icon) { return; }

    trigger.addEventListener('click', function () {
      var isOpen = trigger.getAttribute('aria-expanded') === 'true';

      // Close all other items for a calm, singular reading experience
      accordionItems.forEach(function (otherItem) {
        if (otherItem !== item) {
          var otherTrigger = otherItem.querySelector('.faq-trigger');
          var otherContent = otherItem.querySelector('.faq-content');
          var otherIcon = otherItem.querySelector('.faq-icon');
          otherTrigger.setAttribute('aria-expanded', 'false');
          otherContent.classList.add('hidden');
          otherIcon.textContent = '+';
          otherItem.classList.remove('bg-surface-container');
          otherItem.classList.add('bg-surface-container-low');
        }
      });

      if (isOpen) {
        trigger.setAttribute('aria-expanded', 'false');
        content.classList.add('hidden');
        icon.textContent = '+';
        item.classList.remove('bg-surface-container');
        item.classList.add('bg-surface-container-low');
      } else {
        trigger.setAttribute('aria-expanded', 'true');
        content.classList.remove('hidden');
        icon.textContent = '\u2212';
        item.classList.remove('bg-surface-container-low');
        item.classList.add('bg-surface-container');
      }
    });
  });

  var filterButtons = document.querySelectorAll('.faq-filter-btn');
  filterButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var filterValue = btn.getAttribute('data-filter');

      filterButtons.forEach(function (b) {
        b.classList.remove('bg-primary', 'text-on-primary');
        b.classList.add('bg-surface-container', 'text-on-surface-variant');
      });
      btn.classList.remove('bg-surface-container', 'text-on-surface-variant');
      btn.classList.add('bg-primary', 'text-on-primary');

      accordionItems.forEach(function (item) {
        var itemCategory = item.getAttribute('data-category');
        item.classList.toggle('hidden', !(filterValue === 'all' || itemCategory === filterValue));
      });
    });
  });
})();
</script>
<?php require PUBLIC_PATH . '/partials/footer.php'; ?>
