<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/** Halaman Daftar Paket — design: paket.html */
$metaTitle = 'Paket Umroh & Ibadah — ' . APP_NAME;
$metaDescription = 'Kurasi paket umroh rombongan intim maksimal ' . (!empty($profil['stat_grup_maks']) ? (int) $profil['stat_grup_maks'] : '24') . ' jamaah dengan hotel pelataran utama, bimbingan manasik, dan pendampingan asatidzah berjiwa asuh.';

$modelPaket = model('paket');
$paketList = $modelPaket->getDaftarAktif();

$rows = [];
$durasiSet = [];
foreach ($paketList as $p) {
    $id = (int) $p['id'];
    $jadwal = array_values(array_filter(
        model('jadwal')->getByPaket($id),
        static fn (array $j): bool => (string) $j['tanggal_berangkat'] >= date('Y-m-d')
    ));
    $bulans = [];
    foreach ($jadwal as $j) {
        $ts = strtotime($j['tanggal_berangkat']);
        if ($ts !== false) {
            $bulans[] = (int) date('n', $ts);
        }
    }
    $durasiSet[(int) $p['durasi_hari']] = (int) $p['durasi_hari'];

    $harga = $p['harga_min'] !== null ? (float) $p['harga_min'] : null;
    if ($harga === null) {
        $tier = 'any';
    } elseif ($harga < 40000000) {
        $tier = 'under-40';
    } elseif ($harga <= 50000000) {
        $tier = '40-50';
    } else {
        $tier = 'above-50';
    }

    $rows[] = [
        'row'       => $p,
        'id'        => $id,
        'jadwal'    => $jadwal,
        'bulans'    => array_values(array_unique($bulans)),
        'harga'     => $harga,
        'tier'      => $tier,
        'hotel'     => $modelPaket->getHotels($id),
        'maskapai'  => $modelPaket->getMaskapai($id),
        'fasilitas' => $modelPaket->getFasilitas($id),
    ];
}
ksort($durasiSet);

$featured = $rows[0] ?? null;
$supporting = $rows ? array_slice($rows, 1) : [];

$durationSubtitle = [
    9  => 'Fokus ibadah & itikaf mandiri',
    10 => 'Efisien untuk pasangan & profesional',
    12 => 'Keluarga & jamaah sepuh',
    14 => 'Ritme Ramadhan yang lapang',
    16 => 'Napak tilas sirah & ziarah luas',
];

meta_halaman_apply('paket', $metaTitle, $metaDescription);

require PUBLIC_PATH . '/partials/head.php';
require PUBLIC_PATH . '/partials/header.php';
?>
<!-- Section 1: Editorial Header -->
<section class="w-full bg-surface py-space-lg lg:py-space-xl">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-on-surface-variant font-label-md text-label-md mb-space-sm">
      <a class="hover:text-primary transition-colors" href="<?= url('') ?>">Beranda</a>
      <span class="text-outline-variant text-[11px]">/</span>
      <span class="text-primary font-medium">Paket Ibadah</span>
    </nav>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-md items-end">
      <div class="lg:col-span-8">
        <p class="font-label-md text-label-md text-secondary tracking-wide mb-space-xs font-semibold">Kurasi Khidmat &amp; Kenyamanan Jamaah</p>
        <h1 class="font-headline-lg lg:font-display-md text-headline-lg lg:text-display-md text-primary leading-tight font-normal">
          Pilihan Perjalanan Ibadah yang Teduh dan Terencana
        </h1>
      </div>
      <div class="lg:col-span-4 lg:pb-1">
        <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
          Menghadirkan ketenangan dalam setiap rukun melalui rombongan intim 16&ndash;20 jamaah, bimbingan asatidzah berjiwa asuh, serta hotel pelataran utama tanpa ketergesaan langkah.
        </p>
      </div>
    </div>
    <div class="w-full h-px bg-outline-variant/30 mt-space-lg"></div>
  </div>
</section>

<!-- Section 2: Sidebar Filter + Dynamic Packages Grid -->
<section class="w-full pb-space-xl">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="flex flex-col lg:flex-row gap-gutter-desktop items-start">
      <aside class="w-full lg:w-72 shrink-0 lg:sticky lg:top-28 space-y-space-md">
        <div class="p-space-md rounded-xl bg-surface-container-low shadow-sm space-y-space-md">
          <details id="filterAccordion" class="lg:open group">
            <summary class="flex items-center justify-between pb-space-xs border-b border-outline-variant/20 list-none cursor-pointer">
              <span class="font-headline-sm text-headline-sm text-primary font-medium">Saring Perjalanan</span>
              <span class="flex items-center gap-2">
                <button class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors cursor-pointer" id="reset-filter-btn" type="button">Atur Ulang</button>
                <span aria-hidden="true" class="material-symbols-outlined text-on-surface-variant text-[20px] transition-transform group-open:rotate-180 lg:hidden">expand_more</span>
              </span>
            </summary>

          <div class="space-y-space-xs">
            <span class="font-label-md text-label-md text-tertiary font-semibold block">Durasi Manasik &amp; Ibadah</span>
            <div aria-label="Filter Durasi" class="space-y-1.5" role="radiogroup">
              <label class="duration-option group flex items-start gap-space-xs p-2.5 rounded-lg cursor-pointer transition-colors bg-surface hover:bg-surface-container">
                <input checked="" class="mt-1 accent-[#1f5c56] cursor-pointer" name="filter-duration" type="radio" value="all"/>
                <div class="flex flex-col">
                  <span class="font-body-sm text-body-sm text-on-surface font-medium leading-tight">Semua Durasi</span>
                  <span class="font-label-sm text-label-sm text-on-surface-variant">Koleksi lengkap musim ini</span>
                </div>
              </label>
              <?php foreach ($durasiSet as $d): ?>
              <label class="duration-option group flex items-start gap-space-xs p-2.5 rounded-lg cursor-pointer transition-colors bg-surface-container-lowest hover:bg-surface-container">
                <input class="mt-1 accent-[#1f5c56] cursor-pointer" name="filter-duration" type="radio" value="<?= $d ?>"/>
                <div class="flex flex-col">
                  <span class="font-body-sm text-body-sm text-on-surface font-medium leading-tight"><?= $d ?> Hari</span>
                  <span class="font-label-sm text-label-sm text-on-surface-variant"><?= e($durationSubtitle[$d] ?? 'Program ' . $d . ' hari penuh') ?></span>
                </div>
              </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="space-y-space-xs pt-space-xs border-t border-outline-variant/20">
            <div class="flex justify-between items-center">
              <span class="font-label-md text-label-md text-tertiary font-semibold">Kisaran Kontribusi</span>
              <span class="font-label-sm text-label-sm text-secondary font-medium" id="tier-indicator">Semua Tier</span>
            </div>
            <div aria-label="Rentang Biaya" class="grid grid-cols-1 gap-1.5" role="radiogroup">
              <button class="tier-chip w-full text-left px-3 py-2 rounded-lg bg-surface text-primary text-body-sm font-body-sm font-medium transition-all shadow-sm" data-tier="all" type="button">Seluruh Program</button>
              <button class="tier-chip w-full text-left px-3 py-2 rounded-lg bg-surface-container-lowest text-on-surface-variant hover:text-on-surface text-body-sm font-body-sm transition-all" data-tier="under-40" type="button">Di bawah Rp 40 Jt</button>
              <button class="tier-chip w-full text-left px-3 py-2 rounded-lg bg-surface-container-lowest text-on-surface-variant hover:text-on-surface text-body-sm font-body-sm transition-all" data-tier="40-50" type="button">Rp 40 Jt &ndash; Rp 50 Jt</button>
              <button class="tier-chip w-full text-left px-3 py-2 rounded-lg bg-surface-container-lowest text-on-surface-variant hover:text-on-surface text-body-sm font-body-sm transition-all" data-tier="above-50" type="button">Di atas Rp 50 Jt</button>
            </div>
          </div>

          <div class="space-y-space-xs pt-space-xs border-t border-outline-variant/20">
            <span class="font-label-md text-label-md text-tertiary font-semibold block">Musim Pelaksanaan</span>
            <div class="flex flex-wrap gap-1.5">
              <button class="season-pill px-2.5 py-1.5 rounded text-label-md font-label-md bg-surface text-primary font-medium transition-colors" data-season="all" type="button">Semua Musim</button>
              <button class="season-pill px-2.5 py-1.5 rounded text-label-md font-label-md bg-surface-container-lowest text-on-surface-variant hover:text-on-surface transition-colors" data-season="1" type="button">Awal Tahun (Jan &ndash; Mar)</button>
              <button class="season-pill px-2.5 py-1.5 rounded text-label-md font-label-md bg-surface-container-lowest text-on-surface-variant hover:text-on-surface transition-colors" data-season="2" type="button">Pertengahan (Apr &ndash; Agu)</button>
              <button class="season-pill px-2.5 py-1.5 rounded text-label-md font-label-md bg-surface-container-lowest text-on-surface-variant hover:text-on-surface transition-colors" data-season="3" type="button">Akhir Tahun (Sep &ndash; Des)</button>
            </div>
          </div>

          <div class="p-space-sm rounded-lg bg-surface-container-highest/60 space-y-2">
            <div class="flex items-center gap-1.5 text-primary">
              <span class="material-symbols-outlined text-[18px]">family_restroom</span>
              <span class="font-label-md text-label-md font-semibold">Penyesuaian Keluarga?</span>
            </div>
            <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
              Kami menyediakan konfigurasi kamar terhubung, jadwal khusus anak, serta asisten lansia privat.
            </p>
            <a class="inline-block font-label-md text-label-md text-primary font-medium underline underline-offset-4 hover:text-primary-container transition-colors" href="<?= url('kontak') ?>#konsultasi">
              Konsultasikan Bersama Tim Ahli
            </a>
          </div>
          </details>
        </div>
      </aside>

      <div class="flex-1 w-full min-w-0 flex flex-col gap-space-lg">
        <?php if (!$rows): ?>
        <div class="p-space-lg text-center bg-surface-container rounded-xl">
          <span class="material-symbols-outlined text-outline text-[40px] mb-2">search_off</span>
          <h4 class="font-headline-sm text-headline-sm text-tertiary">Belum Ada Paket Aktif</h4>
          <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 max-w-md mx-auto">
            Program terbaru sedang kami susun. Silakan hubungi konsultan kami untuk keberangkatan khusus.
          </p>
        </div>
        <?php endif; ?>
        <?php if ($featured): $f = $featured; ?>
        <!-- Featured / Signature Package -->
        <article class="package-card group w-full bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm transition-all duration-300 hover:shadow-md"
                 data-duration="<?= (int) $f['row']['durasi_hari'] ?>"
                 data-price="<?= $f['harga'] !== null ? (int) $f['harga'] : 0 ?>"
                 data-tier="<?= e($f['tier']) ?>"
                 data-seasons="<?= e($f['bulans'] ? implode(',', $f['bulans']) : '') ?>">
          <div class="grid grid-cols-1 lg:grid-cols-12">
            <div class="lg:col-span-5 relative min-h-[300px] lg:min-h-full overflow-hidden">
              <img class="absolute inset-0 w-full h-full object-cover object-center group-hover:scale-[1.02] transition-transform duration-700"
                   alt="<?= e($f['row']['nama']) ?>"
                   src="<?= upload_url($f['row']['thumbnail'], 'paket') ?>"/>
              <div class="absolute inset-0 bg-gradient-to-t from-inverse-surface/60 via-transparent to-transparent lg:hidden"></div>
              <?php if ($f['fasilitas']): ?>
              <div class="absolute top-4 left-4">
                <span class="inline-flex items-center px-3 py-1 rounded bg-surface/95 backdrop-blur text-primary font-label-sm text-label-sm font-semibold tracking-wide shadow-sm">
                  <?= e($f['fasilitas'][0]['nama']) ?>
                </span>
              </div>
              <?php endif; ?>
            </div>

            <div class="lg:col-span-7 p-space-md lg:p-space-lg flex flex-col justify-between space-y-space-md">
              <div>
                <div class="flex flex-wrap items-center gap-space-xs text-on-surface-variant font-label-sm text-label-sm mb-1.5">
                  <span class="inline-flex items-center gap-1 font-medium text-primary">
                    <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                    <?= (int) $f['row']['durasi_hari'] ?> Hari <?= max(0, (int) $f['row']['durasi_hari'] - 1) ?> Malam
                  </span>
                  <span>&bull;</span>
                  <span class="inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">groups</span> Grup Intim 16&ndash;20 Jamaah
                  </span>
                  <?php if ($f['jadwal']): ?>
                  <span>&bull;</span>
                  <span class="text-tertiary">Keberangkatan: <?= tanggal($f['jadwal'][0]['tanggal_berangkat'], 'M Y') ?></span>
                  <?php endif; ?>
                </div>
                <h2 class="font-headline-lg text-headline-lg text-primary mb-space-xs font-normal">
                  <?= e($f['row']['nama']) ?>
                </h2>
                <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed mb-space-md">
                  <?= e(excerpt($f['row']['deskripsi'], 300)) ?>
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-space-xs">
                  <?php
                  $amenities = [
                      ['icon' => 'apartment', 'title' => $f['hotel'][0]['nama'] ?? 'Bintang 5 Pelataran', 'sub' => 'Langkah tanpa lelah'],
                      ['icon' => 'menu_book', 'title' => 'Asatidzah Senior', 'sub' => 'Bimbingan intensif 1:' . (!empty($profil['stat_rasio_pembimbing']) ? (int) $profil['stat_rasio_pembimbing'] : '15')],
                      ['icon' => 'flight', 'title' => $f['maskapai'][0]['nama'] ?? 'Maskapai Berjadwal', 'sub' => 'Penerbangan langsung'],
                  ];
                  foreach ($amenities as $a): ?>
                  <div class="p-2.5 rounded-lg bg-surface-container-low flex items-start gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px] shrink-0"><?= e($a['icon']) ?></span>
                    <div class="flex flex-col">
                      <span class="font-label-md text-label-md font-semibold text-on-surface"><?= e($a['title']) ?></span>
                      <span class="font-label-sm text-label-sm text-on-surface-variant"><?= e($a['sub']) ?></span>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="pt-space-md border-t border-outline-variant/20 flex flex-col sm:flex-row sm:items-end justify-between gap-space-sm">
                <div>
                  <span class="font-label-sm text-label-sm text-on-surface-variant block mb-0.5">Biaya Kontribusi Mulai</span>
                  <div class="flex items-baseline gap-1">
                    <span class="font-headline-lg text-headline-lg font-normal text-secondary">
                      <?= $f['harga'] !== null ? rupiah($f['harga']) : 'Hubungi kami' ?>
                    </span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant">/ jamaah (Quad)</span>
                  </div>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                  <a class="px-space-md py-space-sm bg-surface-container hover:bg-surface-container-high text-primary font-label-md text-label-md font-semibold rounded transition-colors inline-flex items-center justify-center" href="<?= url('kontak') ?>#konsultasi">
                    Konsultasi Paket
                  </a>
                  <a class="px-space-md py-space-sm bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md font-medium rounded transition-colors shadow-sm inline-flex items-center justify-center" href="<?= url('paket/' . $f['row']['slug']) ?>">
                    Lihat Rincian &amp; Jadwal
                  </a>
                </div>
              </div>
            </div>
          </div>
        </article>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
          <?php foreach ($supporting as $s): $p = $s['row']; ?>
          <article class="package-card flex flex-col justify-between bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300"
                   data-duration="<?= (int) $p['durasi_hari'] ?>"
                   data-price="<?= $s['harga'] !== null ? (int) $s['harga'] : 0 ?>"
                   data-tier="<?= e($s['tier']) ?>"
                   data-seasons="<?= e($s['bulans'] ? implode(',', $s['bulans']) : '') ?>">
            <div class="relative w-full h-52 overflow-hidden">
              <img class="w-full h-full object-cover object-center transition-transform duration-500 hover:scale-105"
                   alt="<?= e($p['nama']) ?>" src="<?= upload_url($p['thumbnail'], 'paket') ?>"/>
              <div class="absolute top-3 left-3">
                <span class="px-2.5 py-1 rounded bg-surface/90 backdrop-blur font-label-sm text-label-sm text-primary font-medium shadow-sm">
                  <?= (int) $p['durasi_hari'] ?> Hari <?= max(0, (int) $p['durasi_hari'] - 1) ?> Malam
                </span>
              </div>
            </div>
            <div class="p-space-md flex-1 flex flex-col justify-between space-y-space-md">
              <div class="space-y-space-xs">
                <div class="flex items-center gap-2 text-on-surface-variant font-label-sm text-label-sm">
                  <span><?= $s['jadwal'] ? 'Berangkat ' . tanggal($s['jadwal'][0]['tanggal_berangkat']) : 'Jadwal menyusul' ?></span>
                  <span>&bull;</span>
                  <span>Grup Intim 16&ndash;20 Jamaah</span>
                </div>
                <h3 class="font-headline-md text-headline-md text-primary font-normal"><?= e($p['nama']) ?></h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed"><?= e(excerpt($p['deskripsi'], 170)) ?></p>
                <div class="flex flex-wrap items-center gap-4 text-on-surface-variant font-label-sm text-label-sm pt-2">
                  <?php if ($s['hotel']): ?>
                  <span class="inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-primary">location_city</span>
                    <?= e($s['hotel'][0]['nama']) ?>
                  </span>
                  <?php endif; ?>
                  <?php if ($s['maskapai']): ?>
                  <span class="inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-primary">flight_takeoff</span>
                    <?= e($s['maskapai'][0]['nama']) ?>
                  </span>
                  <?php endif; ?>
                </div>
              </div>
              <div class="pt-space-sm border-t border-outline-variant/20 flex items-center justify-between gap-2">
                <div>
                  <span class="font-label-sm text-label-sm text-on-surface-variant block">Mulai dari</span>
                  <span class="font-headline-sm text-headline-sm text-secondary font-medium">
                    <?= $s['harga'] !== null ? rupiah($s['harga']) : 'Hubungi kami' ?>
                  </span>
                </div>
                <a class="px-4 py-2 bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md rounded transition-colors inline-flex items-center" href="<?= url('paket/' . $p['slug']) ?>">
                  Lihat Rincian Paket
                </a>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        </div>

        <div class="hidden p-space-lg text-center bg-surface-container rounded-xl" id="no-results">
          <span class="material-symbols-outlined text-outline text-[40px] mb-2">search_off</span>
          <h4 class="font-headline-sm text-headline-sm text-tertiary">Tidak Ada Paket yang Sesuai</h4>
          <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 max-w-md mx-auto">
            Silakan sesuaikan pilihan durasi, rentang biaya, atau musim keberangkatan, atau hubungi konsultan kami untuk keberangkatan khusus.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Section 3: Jaminan Kenyamanan & Bimbingan Keluarga -->
<section class="w-full bg-surface-container py-space-xl">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="text-center max-w-2xl mx-auto mb-space-lg">
      <p class="font-label-md text-label-md text-secondary font-semibold mb-1">Integritas Pelayanan</p>
      <h2 class="font-headline-lg text-headline-lg text-primary font-normal">Pondasi Kenyamanan Keluarga Anda</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter-desktop">
      <div class="p-space-md rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between">
        <div class="space-y-space-xs">
          <div class="w-12 h-12 rounded-lg bg-surface-container-low flex items-center justify-center text-primary mb-space-xs">
            <span class="material-symbols-outlined text-[26px]">receipt_long</span>
          </div>
          <h3 class="font-headline-sm text-headline-sm text-tertiary font-medium">Transparansi Fasilitas Tanpa Biaya Tersembunyi</h3>
          <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
            Seluruh rincian biaya meliputi visa, tiket penerbangan langsung berjadwal pasti, hotel pelataran, asuransi komprehensif, dan konsumsi penuh disajikan gamblang sejak awal.
          </p>
        </div>
        <div class="mt-space-md pt-space-xs border-t border-outline-variant/20">
          <span class="font-label-sm text-label-sm text-primary font-semibold">Akad Sesuai Syariat &amp; Regulasi</span>
        </div>
      </div>
      <div class="p-space-md rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between">
        <div class="space-y-space-xs">
          <div class="w-12 h-12 rounded-lg bg-surface-container-low flex items-center justify-center text-primary mb-space-xs">
            <span class="material-symbols-outlined text-[26px]">menu_book</span>
          </div>
          <h3 class="font-headline-sm text-headline-sm text-tertiary font-medium">Bimbingan Manasik Sebelum &amp; Selama di Tanah Suci</h3>
          <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
            Tiga kali tatap muka manasik intensif di Jakarta sebelum keberangkatan, dilanjutkan ta'lim harian di Madinah dan Makkah untuk menjaga kedalaman penghayatan doa.
          </p>
        </div>
        <div class="mt-space-md pt-space-xs border-t border-outline-variant/20">
          <span class="font-label-sm text-label-sm text-primary font-semibold">Muthawwif Alumnus Timur Tengah</span>
        </div>
      </div>
      <div class="p-space-md rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between">
        <div class="space-y-space-xs">
          <div class="w-12 h-12 rounded-lg bg-surface-container-low flex items-center justify-center text-primary mb-space-xs">
            <span class="material-symbols-outlined text-[26px]">medical_services</span>
          </div>
          <h3 class="font-headline-sm text-headline-sm text-tertiary font-medium">Klinik &amp; Dokter Pendamping Terpadu</h3>
          <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
            Dokter pendamping medis senantiasa mendampingi rombongan untuk memantau kesehatan jamaah lansia, penanganan cuaca ekstrem, dan ketersediaan obat esensial.
          </p>
        </div>
        <div class="mt-space-md pt-space-xs border-t border-outline-variant/20">
          <span class="font-label-sm text-label-sm text-primary font-semibold">Siaga 24 Jam di Setiap Kota</span>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
(function () {
  var state = { duration: 'all', tier: 'all', season: 'all' };
  var cards = Array.prototype.slice.call(document.querySelectorAll('.package-card'));
  var noResults = document.getElementById('no-results');
  var tierIndicator = document.getElementById('tier-indicator');
  var tierLabels = { all: 'Semua Tier', 'under-40': 'Di bawah Rp 40 Jt', '40-50': 'Rp 40 - 50 Jt', 'above-50': 'Di atas Rp 50 Jt' };
  var durationRadios = Array.prototype.slice.call(document.querySelectorAll('input[name="filter-duration"]'));
  var tierChips = Array.prototype.slice.call(document.querySelectorAll('.tier-chip'));
  var seasonPills = Array.prototype.slice.call(document.querySelectorAll('.season-pill'));

  function highlight(nodes, active) {
    nodes.forEach(function (n) {
      if (n === active) {
        n.classList.add('bg-surface', 'text-primary', 'font-medium', 'shadow-sm');
        n.classList.remove('bg-surface-container-lowest', 'text-on-surface-variant');
      } else {
        n.classList.remove('bg-surface', 'text-primary', 'font-medium', 'shadow-sm');
        n.classList.add('bg-surface-container-lowest', 'text-on-surface-variant');
      }
    });
  }

  function matchSeason(card) {
    if (state.season === 'all') { return true; }
    var raw = card.getAttribute('data-seasons') || '';
    if (!raw) { return true; }
    var months = raw.split(',');
    var ranges = { 1: [1, 3], 2: [4, 8], 3: [9, 12] };
    var r = ranges[state.season];
    return months.some(function (m) {
      var v = parseInt(m, 10);
      return v >= r[0] && v <= r[1];
    });
  }

  function apply() {
    var visible = 0;
    cards.forEach(function (card) {
      var dur = card.getAttribute('data-duration');
      var tier = card.getAttribute('data-tier') || 'any';
      var okDuration = state.duration === 'all' || dur === state.duration;
      var okTier = state.tier === 'all' || tier === state.tier || tier === 'any';
      var ok = okDuration && okTier && matchSeason(card);
      card.classList.toggle('hidden', !ok);
      if (ok) { visible++; }
    });
    if (noResults) { noResults.classList.toggle('hidden', visible !== 0); }
  }

  durationRadios.forEach(function (radio) {
    radio.addEventListener('change', function () { state.duration = this.value; apply(); });
  });

  tierChips.forEach(function (chip) {
    chip.addEventListener('click', function () {
      state.tier = chip.getAttribute('data-tier');
      highlight(tierChips, chip);
      if (tierIndicator) { tierIndicator.textContent = tierLabels[state.tier] || 'Semua Tier'; }
      apply();
    });
  });

  seasonPills.forEach(function (pill) {
    pill.addEventListener('click', function () {
      state.season = pill.getAttribute('data-season');
      highlight(seasonPills, pill);
      apply();
    });
  });

  var resetBtn = document.getElementById('reset-filter-btn');
  if (resetBtn) {
    resetBtn.addEventListener('click', function () {
      state = { duration: 'all', tier: 'all', season: 'all' };
      durationRadios.forEach(function (r) { r.checked = r.value === 'all'; });
      highlight(tierChips, tierChips[0]);
      highlight(seasonPills, seasonPills[0]);
      if (tierIndicator) { tierIndicator.textContent = 'Semua Tier'; }
      apply();
    });
  }

  var filterAccordion = document.getElementById('filterAccordion');
  if (filterAccordion) {
    function syncAccordion() {
      if (window.matchMedia('(min-width:1024px)').matches) { filterAccordion.open = true; }
    }
    syncAccordion();
    window.addEventListener('resize', syncAccordion);
  }
})();
</script>
<?php require PUBLIC_PATH . '/partials/footer.php'; ?>
