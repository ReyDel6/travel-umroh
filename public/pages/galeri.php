<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/** Halaman Galeri — design: galeri.html (converted to PHP dynamic) */
$metaTitle = 'Galeri & Catatan Perjalanan — ' . APP_NAME;
$metaDescription = 'Dokumentasi khidmat perjalanan ibadah ke Tanah Suci, penuh ketenangan dan syukur para jamaah.';

$galeriModel = model('galeri');
$perPage = 12;
$page = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
if ($page < 1) {
    $page = 1;
}
$totalCount = $galeriModel->countPublic();
$items = $galeriModel->getPublic(null, $perPage, ($page - 1) * $perPage);
$kategoris = $galeriModel->getKategori();

$paginationHtml = '';
if ($totalCount > $perPage) {
    $paginationHtml = pagination($totalCount, $perPage, $page, 'galeri');
}

meta_halaman_apply('galeri', $metaTitle, $metaDescription);

require PUBLIC_PATH . '/partials/head.php';
require PUBLIC_PATH . '/partials/header.php';
?>
<!-- Top Intro & Curator Section -->
<section class="w-full bg-surface-container-low/70 py-space-xl">
    <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
        <!-- Breadcrumb -->
        <nav aria-label="Jalur Navigasi" class="flex items-center gap-space-xs text-on-surface-variant font-body-sm text-body-sm mb-space-md">
            <a class="hover:text-primary transition-colors" href="<?= url('') ?>">Beranda</a>
            <span class="text-outline-variant font-light">/</span>
            <span class="text-tertiary font-medium">Galeri &amp; Catatan Perjalanan</span>
        </nav>
        <!-- Editorial Header Asymmetric Split -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter-desktop items-end">
            <div class="lg:col-span-8 flex flex-col gap-space-xs">
                <span class="font-label-md text-label-md text-secondary tracking-normal font-medium">
                    Dokumentasi Khidmat &amp; Jejak Syukur
                </span>
                <h1 class="font-headline-lg text-headline-lg lg:font-display-lg lg:text-display-lg text-tertiary font-normal tracking-tight leading-tight">
                    Rekaman saat-saat hening<br class="hidden sm:inline"/>
                    dan kebersamaan yang penuh arti.
                </h1>
            </div>
            <div class="lg:col-span-4 pb-1">
                <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                    Setiap dokumentasi kami abadikan dengan penuh takzim tanpa sedikit pun mengusik kesyahduan munajat pribadi. Sebuah arsip tulus yang merekam senyum teduh para sepuh, langkah mantap di pelataran Madinah, serta ketulusan pendampingan keluarga di Tanah Suci.
                </p>
            </div>
        </div>
        <!-- Filter Controls: Simple, Unadorned Editorial Tabs -->
        <div class="mt-space-xl pt-space-md flex flex-wrap items-center gap-space-xs border-t border-outline-variant/30">
            <span class="font-label-sm text-label-sm text-on-surface-variant mr-space-sm hidden sm:inline">Kurasi Momen:</span>
            <button class="gallery-filter-btn px-space-md py-space-xs font-label-md text-label-md rounded bg-primary text-on-primary transition-colors cursor-pointer" data-category="all" type="button">
                Semua Momen
            </button>
            <?php foreach ($kategoris as $kategori): ?>
                <button class="gallery-filter-btn px-space-md py-space-xs font-label-md text-label-md rounded bg-surface-container text-on-surface-variant hover:text-primary transition-colors cursor-pointer" data-category="<?= e($kategori) ?>" type="button">
                    <?= e($kategori) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Editorial Masonry Gallery Grid -->
<section class="w-full py-space-xl bg-surface">
    <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
        <?php if (empty($items)): ?>
            <div class="flex flex-col items-center justify-center py-space-xl text-center">
                <span class="material-symbols-outlined text-[48px] text-on-surface-variant/50 mb-space-sm">photo_library</span>
                <h3 class="font-headline-md text-headline-md text-tertiary mb-space-xs">Belum Ada Dokumentasi</h3>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-md">Galeri kami akan segera diisi dengan momen-momen berharga perjalanan ibadah para jamaah.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-space-lg items-start" id="gallery-container">
                <?php foreach ($items as $idx => $item): ?>
                    <?php
                    $kategori = $item['kategori'] ?? '';
                    $gambar = $item['gambar'] ?? '';
                    $judul = $item['judul'] ?? '';
                    $spanClass = 'lg:col-span-4';
                    $aspectClass = 'aspect-[4/3]';
                    if ($idx % 6 === 0) {
                        $spanClass = 'lg:col-span-5';
                        $aspectClass = 'aspect-[3/4]';
                    } elseif ($idx % 6 === 1) {
                        $spanClass = 'lg:col-span-7';
                        $aspectClass = 'aspect-[16/10]';
                    } elseif ($idx % 6 === 2) {
                        $spanClass = 'lg:col-span-4';
                        $aspectClass = 'aspect-[4/3]';
                    } elseif ($idx % 6 === 3) {
                        $spanClass = 'lg:col-span-4';
                        $aspectClass = 'aspect-[4/3]';
                    } elseif ($idx % 6 === 4) {
                        $spanClass = 'lg:col-span-4';
                        $aspectClass = 'aspect-[3/4]';
                    } elseif ($idx % 6 === 5) {
                        $spanClass = 'lg:col-span-8';
                        $aspectClass = 'aspect-[16/9]';
                    }
                    ?>
                    <article class="gallery-card <?= $spanClass ?> flex flex-col gap-space-sm" data-group="<?= e($kategori) ?>">
                        <div class="relative overflow-hidden rounded-lg bg-surface-container-high <?= $aspectClass ?> group">
                            <img alt="<?= e($judul ?: 'Dokumentasi perjalanan ibadah') ?>" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.02]" src="<?= e(upload_url($gambar, 'galeri')) ?>"/>
                            <?php if ($kategori): ?>
                                <div class="absolute top-space-sm left-space-sm bg-surface-container-lowest/85 backdrop-blur-sm px-space-sm py-space-xs rounded text-primary font-label-sm text-label-sm">
                                    <?= e($kategori) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($judul): ?>
                            <div class="px-space-xs flex flex-col gap-0.5">
                                <p class="font-body-md text-body-md text-tertiary font-medium">
                                    <?= e($judul) ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="hidden py-space-xl text-center" id="gallery-empty">
                <span class="material-symbols-outlined text-[48px] text-on-surface-variant/50 mb-space-sm">filter_alt_off</span>
                <h3 class="font-headline-md text-headline-md text-tertiary mb-space-xs">Tidak Ada Momen di Kategori Ini</h3>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-md mx-auto">Pilih kategori lain atau tampilkan semua momen dokumentasi perjalanan.</p>
            </div>
            <?php if ($paginationHtml): ?>
                <div class="mt-space-xl flex justify-center">
                    <?= $paginationHtml ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Documentary Ethics Section (Intermezzo) -->
<section class="w-full bg-surface-container py-space-xl">
    <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
        <!-- Quote Block -->
        <div class="max-w-3xl mx-auto text-center flex flex-col items-center gap-space-sm mb-space-xl">
            <span class="text-secondary text-headline-lg font-headline-lg leading-none select-none">“</span>
            <blockquote class="font-headline-md text-headline-md text-tertiary font-normal tracking-wide italic leading-relaxed -mt-space-sm">
                Prinsip etika dokumentasi Sakinah: Mengabadikan rasa syukur tanpa pernah mendahului kekhusyukan doa.
            </blockquote>
            <div class="w-12 h-[1px] bg-outline-variant/60 my-space-xs">
            </div>
            <cite class="font-label-md text-label-md text-on-surface-variant not-italic">
                Catatan Kurator Visual &amp; Tim Pendamping Sakinah Journeys
            </cite>
        </div>
        <!-- 3 Pillars of Visual Ethics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter-desktop">
            <!-- Pillar 1 -->
            <div class="p-space-lg rounded-lg bg-surface-container-lowest flex flex-col gap-space-xs">
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-space-xs">
                    <span class="material-symbols-outlined text-[20px]">visibility_off</span>
                </div>
                <h2 class="font-headline-sm text-headline-sm text-tertiary">Privasi Selalu Terjaga</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                    Kamera hanya membidik dengan izin sadar dan menjaga penuh aib serta ruang personal jamaah. Kami tidak memublikasikan foto air mata doa yang bersifat intim tanpa kerelaan keluarga.
                </p>
            </div>
            <!-- Pillar 2 -->
            <div class="p-space-lg rounded-lg bg-surface-container-lowest flex flex-col gap-space-xs">
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-space-xs">
                    <span class="material-symbols-outlined text-[20px]">nature_people</span>
                </div>
                <h2 class="font-headline-sm text-headline-sm text-tertiary">Candid Alami &amp; Tanpa Rekayasa</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                    Tidak ada pengarahan gaya buatan atau pose repetitif yang melelahkan. Kami hanya menangkap getar keikhlasan gerak alami saat melangkah, beristirahat, dan bersua keluarga.
                </p>
            </div>
            <!-- Pillar 3 -->
            <div class="p-space-lg rounded-lg bg-surface-container-lowest flex flex-col gap-space-xs">
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-space-xs">
                    <span class="material-symbols-outlined text-[20px]">volunteer_activism</span>
                </div>
                <h2 class="font-headline-sm text-headline-sm text-tertiary">Pendampingan Tetap Prioritas Utama</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                    Tangan petugas selalu lebih dahulu mengulurkan bantuan kursi roda, air minum, atau payung sebelum mengangkat lensa kamera. Keamanan fisik dan batin jamaah berada di atas segalanya.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Closing Family Consultation CTA -->
<section class="w-full py-space-xl bg-primary text-on-primary">
    <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-center">
            <div class="lg:col-span-8 flex flex-col gap-space-sm">
                <span class="font-label-sm text-label-sm text-secondary-fixed tracking-wider uppercase">
                    Silaturahmi Perjalanan Suci
                </span>
                <h2 class="font-headline-lg text-headline-lg lg:font-display-md lg:text-display-md text-on-primary font-normal leading-snug">
                    Rencanakan Momen Penuh Arti Bersama Keluarga Tercinta
                </h2>
                <p class="font-body-md text-body-md text-on-primary/80 max-w-2xl leading-relaxed">
                    Konsultasikan pilihan jadwal, pendampingan lansia, dan kenyamanan kamar dengan concierge kami untuk memastikan ketenangan ibadah setiap generasi keluarga Anda.
                </p>
            </div>
            <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-space-sm justify-start lg:items-end">
                <a class="inline-flex items-center justify-center px-space-lg py-space-sm bg-secondary-fixed text-on-secondary-fixed font-label-md text-label-md rounded hover:bg-secondary-fixed-dim transition-colors text-center shadow-sm" href="<?= url('kontak') ?>">
                    Jadwalkan Konsultasi Keluarga
                </a>
                <a class="inline-flex items-center justify-center px-space-lg py-space-sm bg-primary-container text-on-primary font-label-md text-label-md rounded hover:bg-primary-container/80 transition-colors text-center border-0" href="<?= url('paket') ?>">
                    Tanya Jadwal Keberangkatan
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Simple Filter Interaction Script -->
<script>
    (function() {
        const filterButtons = document.querySelectorAll('.gallery-filter-btn');
        const cards = document.querySelectorAll('.gallery-card');
        const emptyState = document.querySelector('#gallery-empty');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                const category = button.getAttribute('data-category');

                filterButtons.forEach(btn => {
                    btn.classList.remove('bg-primary', 'text-on-primary');
                    btn.classList.add('bg-surface-container', 'text-on-surface-variant');
                });
                button.classList.remove('bg-surface-container', 'text-on-surface-variant');
                button.classList.add('bg-primary', 'text-on-primary');

                let visibleCount = 0;
                cards.forEach(card => {
                    const group = card.getAttribute('data-group');
                    if (category === 'all' || group === category) {
                        card.classList.remove('hidden');
                        card.style.opacity = '1';
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                        card.style.opacity = '0';
                    }
                });

                if (emptyState) {
                    if (visibleCount === 0) {
                        emptyState.classList.remove('hidden');
                    } else {
                        emptyState.classList.add('hidden');
                    }
                }
            });
        });
    })();
</script>

<?php require PUBLIC_PATH . '/partials/footer.php'; ?>