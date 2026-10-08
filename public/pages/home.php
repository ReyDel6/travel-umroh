<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/** Halaman Beranda — design: beranda_umroh_ceria_ringan_bermakna */
$metaTitle = ($profil['meta_title'] ?? '') ?: (APP_NAME . ' — Umroh Keluarga yang Ringan & Berkesan');
$metaDescription = ($profil['meta_description'] ?? '') ?: 'Umroh keluarga terasa ringan dan penuh makna: grup kecil, dokter & muthawwif mendampingi, hotel dekat Masjid. Izin PPIU Kemenag RI, Akreditasi A.';

meta_halaman_apply('home', $metaTitle, $metaDescription);

require PUBLIC_PATH . '/partials/head.php';
require PUBLIC_PATH . '/partials/header.php';

/* ---------- Data halaman ---------- */
$paketList    = model('paket')->getDaftarAktif();
$jadwalList   = model('jadwal')->getMendatang(6);
$galeriList   = model('galeri')->getPublic(null, 10);
$artikelList  = model('artikel')->getPublish(3);
$faqList      = array_slice(model('faq')->getPublic(), 0, 6);
$testimoniDb  = model('testimoni')->getAktif(4);

/* Contoh media placeholder (foto/video) agar halaman terasa hidup. */
$mediaContoh = [
    [
        'nama' => 'Keluarga Bapak Rahmat', 'asal' => 'Medan',
        'teks' => 'Kami berangkat bertiga, ibu 68 tahun ikut mulus. Pendampingnya perhatian, hotelnya dekat masjid, dan semua kebutuhan diurus rapi.', 'foto' => 'galeri-07.jpg', 'contoh' => true,
    ],
    [
        'nama' => 'Siti & Rombongan Komplek', 'asal' => 'Makassar',
        'teks' => 'Padat ibadah tapi tetap santai. Tiga sesi manasiknya membuat semua anggota rombongan siap dan tenang, termasuk yang pertama kali.', 'foto' => 'galeri-04.jpg', 'contoh' => true,
    ],
];

$testimoniList = array_merge($testimoniDb, $mediaContoh);

$videoContoh = [
    ['judul' => 'Sesi manasik hangat keluarga', 'sumber' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4', 'poster' => 'galeri-06.jpg'],
    ['judul' => 'Cerita jamaah Rawdha Khidmat',  'sumber' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',  'poster' => 'galeri-03.jpg'],
];

$waLink = 'https://wa.me/628118892011?text=' . rawurlencode('Assalamualaikum Sakinah Journeys, saya ingin berkonsultasi rencana umroh keluarga.');
$tahunBerdiri = (int) ($profil['tahun_berdiri'] ?? 2021);
$tahunLayanan = max(1, (int) date('Y') - $tahunBerdiri);
$kuotaMax     = (int) max(array_column($jadwalList, 'kuota') ?: [1]);

$trustItems = [
    ['ikon' => 'verified_user',  'label' => 'Izin PPIU Resmi Kemenag'],
    ['ikon' => 'verified',       'label' => 'Akreditasi A'],
    ['ikon' => 'medical_services','label' => 'Dokter Pendamping'],
    ['ikon' => 'menu_book',      'label' => 'Muthawwif Bersertifikat'],
    ['ikon' => 'hotel',          'label' => 'Hotel Dekat Masjid'],
    ['ikon' => 'train',          'label' => 'Kereta Cepat Haramain'],
    ['ikon' => 'badge',          'label' => 'Visa Diurus Resmi'],
    ['ikon' => 'water_drop',     'label' => 'Air Zam-Zam 5 Liter'],
];

$pilar = [
    ['ikon' => 'groups_2',         'judul' => 'Grup Kecil & Intim',   'teks' => 'Maksimal ' . (int) ($profil['stat_grup_maks'] ?? 24) . ' jamaah per rombongan.'],
    ['ikon' => 'medical_services', 'judul' => 'Dokter & Muthawwif',   'teks' => 'Pendamping kesehatan dan ibadah siaga penuh.'],
    ['ikon' => 'hotel',            'judul' => 'Hotel Dekat Masjid',   'teks' => 'Jalan kaki santai ke shaf utama, tanpa tergesa.'],
    ['ikon' => 'school',           'judul' => 'Manasik Menyenangkan', 'teks' => (int) ($profil['stat_sesi_manasik'] ?? 3) . ' sesi manasik hangat sebelum berangkat.'],
];

$initials = static function (array $t): string {
    $kata = array_values(array_filter(
        preg_split('/[\s&]+/', (string) $t['nama']) ?: [],
        static fn($w) => !in_array(mb_strtolower(trim($w)), ['dan', 'ibu', 'bapak', 'dr.', 'dr', 'h.', 'h', 'hj.', 'hajjah', 'ust.', 'ustadz'], true) && $w !== ''
    ));
    $inT = isset($kata[0]) ? mb_strtoupper(mb_substr($kata[0], 0, 1)) : 'J';
    $inAkhir = count($kata) > 1 ? mb_strtoupper(mb_substr(end($kata), 0, 1)) : '';
    return $inT . ($inAkhir !== '' && $inAkhir !== $inT ? $inAkhir : '');
};
?>
<style>
  .anim-hero{opacity:0;animation:fadeUp .8s cubic-bezier(.22,.61,.36,1) forwards}
  @keyframes fadeUp{from{opacity:0;transform:translateY(26px)}to{opacity:1;transform:none}}
  [data-reveal]{opacity:0;transform:translateY(26px);transition:opacity .7s ease,transform .7s ease}
  [data-reveal].is-visible{opacity:1;transform:none}
  .no-scrollbar::-webkit-scrollbar{display:none}
  .no-scrollbar{scrollbar-width:none}
  .marquee-track{display:flex;width:max-content;animation:marquee 30s linear infinite}
  .marquee-track:hover{animation-play-state:paused}
  @keyframes marquee{from{transform:translateX(0)}to{transform:translateX(-50%)}}
  .faq-body{max-height:0;overflow:hidden;transition:max-height .4s ease}
  .faq-item.open .faq-body{max-height:420px}
  @media (prefers-reduced-motion:reduce){.anim-hero,[data-reveal],.marquee-track{animation:none;opacity:1;transform:none;transition:none}}
</style>

<?php /* ==================== HERO ==================== */ ?>
<section class="relative overflow-hidden bg-gradient-to-b from-primary-container/50 via-surface to-surface text-on-surface">
  <div aria-hidden="true" class="pointer-events-none absolute -top-24 -right-24 w-96 h-96 rounded-full bg-primary-container/40 blur-3xl"></div>
  <div aria-hidden="true" class="pointer-events-none absolute top-40 -left-28 w-80 h-80 rounded-full bg-secondary-fixed/50 blur-3xl"></div>

  <div class="relative max-w-7xl mx-auto px-margin lg:px-gutter-desktop pt-space-lg pb-space-xl grid grid-cols-1 lg:grid-cols-12 gap-space-lg lg:gap-gutter-desktop items-center">
    <!-- Kiri: teks pendek + pencarian -->
    <div class="lg:col-span-7 flex flex-col">
      <div class="anim-hero" style="animation-delay:.05s">
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-primary-container text-primary font-label-sm text-label-sm shadow-sm">
          <span class="w-2 h-2 rounded-full bg-secondary"></span>
          <?= e($profil['izin_ppiu'] ?? 'Travel Umroh Resmi Berizin') ?>
        </span>
      </div>

      <h1 class="anim-hero font-display-lg text-display-lg-mobile sm:text-display-lg text-on-surface mt-space-md mb-space-md" style="animation-delay:.15s">
        Umroh keluarga yang ringan, indah, dan penuh makna.
      </h1>

      <p class="anim-hero font-body-lg text-body-lg text-on-surface-variant max-w-xl mb-space-lg leading-relaxed" style="animation-delay:.25s">
        Grup kecil yang intim, manasik menyenangkan, dokter &amp; muthawwif membersamai, dan hotel dekat Masjid. Anda dan keluarga tinggal beribadah dengan tenang.
      </p>

      <!-- Pencarian cepat paket -->
      <form id="hero-search" class="anim-hero bg-white border border-outline-variant rounded-2xl shadow-sm p-space-sm sm:p-space-md flex flex-col sm:flex-row gap-space-sm items-stretch sm:items-center mb-space-md" style="animation-delay:.35s">
        <label class="sr-only" for="hero-paket">Pilih paket umroh</label>
        <select id="hero-paket" name="paket" class="flex-1 bg-transparent px-space-sm py-space-sm text-body-md text-on-surface outline-none cursor-pointer" aria-label="Pilih paket">
          <option value="">Pilih paket umroh…</option>
          <?php foreach ($paketList as $plOpt): ?>
          <option value="<?= e($plOpt['slug']) ?>"><?= e($plOpt['nama']) ?> · <?= (int) $plOpt['durasi_hari'] ?> hari</option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="inline-flex items-center justify-center gap-2 px-space-lg py-space-sm bg-primary text-white font-label-md text-label-md rounded-xl hover:bg-primary/90 transition-colors">
          <span class="material-symbols-outlined text-[18px]">search</span> Lihat Paket &amp; Jadwal
        </button>
      </form>

      <div class="anim-hero grid grid-cols-3 gap-space-sm max-w-lg" style="animation-delay:.45s">
        <div class="bg-surface-container/70 rounded-xl p-space-sm border border-primary-container/40 text-center">
          <span class="block font-display-md text-headline-md text-primary font-bold"><?= $tahunLayanan ?>+</span>
          <span class="font-label-sm text-label-sm text-on-surface-variant">Tahun Berkhidmat</span>
        </div>
        <div class="bg-surface-container/70 rounded-xl p-space-sm border border-primary-container/40 text-center">
          <span class="block font-display-md text-headline-md text-primary font-bold"><?= (int) ($profil['stat_grup_maks'] ?? 24) ?></span>
          <span class="font-label-sm text-label-sm text-on-surface-variant">Jamaah Maks / Grup</span>
        </div>
        <div class="bg-surface-container/70 rounded-xl p-space-sm border border-primary-container/40 text-center">
          <span class="block font-display-md text-headline-md text-primary font-bold">1:<?= (int) ($profil['stat_rasio_pembimbing'] ?? 15) ?></span>
          <span class="font-label-sm text-label-sm text-on-surface-variant">Rasio Pembimbing</span>
        </div>
      </div>

      <div class="anim-hero mt-space-lg flex flex-wrap items-center gap-space-sm text-on-surface-variant font-body-sm text-body-sm" style="animation-delay:.55s">
        <span class="flex items-center gap-1 text-secondary">
          <span class="material-symbols-outlined text-[16px]">star</span>
          <span class="font-semibold">4,9/5</span>
        </span>
        <span class="w-1 h-1 rounded-full bg-outline"></span>
        <span>Dipercaya keluarga dari Jakarta, Surabaya, Medan, Makassar &amp; 20+ kota lain</span>
      </div>
    </div>

    <!-- Kanan: kolase foto -->
    <div class="lg:col-span-5 relative mt-space-lg lg:mt-0">
      <div class="relative">
        <img class="anim-hero w-full aspect-[4/5] object-cover rounded-2xl border border-outline-variant shadow-xl" style="animation-delay:.2s" loading="eager" decoding="async" alt="Pendampingan jamaah keluarga di pelataran Masjid Nabawi" src="<?= upload_url('hero-utama.jpg', 'profil') ?>"/>
        <div class="anim-hero absolute -bottom-5 -left-4 sm:-left-8 w-32 sm:w-40 rounded-xl overflow-hidden border-2 border-white shadow-lg" style="animation-delay:.5s">
          <img class="w-full aspect-square object-cover" loading="lazy" decoding="async" alt="Kebersamaan keluarga jamaah" src="<?= upload_url('galeri-05.jpg', 'galeri') ?>"/>
        </div>
        <div class="anim-hero absolute -top-4 -right-3 sm:-right-6 hidden sm:block w-40 rounded-xl overflow-hidden border-2 border-white shadow-lg" style="animation-delay:.65s">
          <img class="w-full aspect-[4/3] object-cover" loading="lazy" decoding="async" alt="Suasana pelataran Masjid Nabawi" src="<?= upload_url('galeri-11.jpg', 'galeri') ?>"/>
        </div>
        <div class="anim-hero absolute top-6 left-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/90 backdrop-blur-sm border border-outline-variant shadow-sm" style="animation-delay:.8s">
          <span class="material-symbols-outlined text-secondary text-[16px]">workspace_premium</span>
          <span class="font-label-sm text-label-sm font-semibold text-on-surface">Pembimbing &amp; Tim Medis</span>
        </div>
      </div>
    </div>
  </div>
</section>

<?php /* ==================== TRUSTBAR (marquee) ==================== */ ?>
<div aria-hidden="true" class="w-full bg-primary text-white py-3 overflow-hidden">
  <div class="marquee-track">
    <?php $trustLoop = array_merge($trustItems, $trustItems); foreach ($trustLoop as $ti): ?>
    <span class="inline-flex items-center gap-2 mx-6 font-label-md text-label-md whitespace-nowrap">
      <span class="material-symbols-outlined text-[18px] text-secondary-fixed"><?= e($ti['ikon']) ?></span>
      <?= e($ti['label']) ?>
    </span>
    <?php endforeach; ?>
  </div>
</div>

<?php /* ==================== PAKET UMROH (slider) ==================== */ ?>
<section class="w-full bg-surface-container py-space-xl text-on-surface">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="flex flex-wrap items-end justify-between gap-space-md mb-space-lg">
      <div data-reveal class="max-w-xl">
        <span class="font-label-md text-label-md text-primary font-semibold block mb-space-xs">Paket Umroh</span>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-space-xs">Pilih tempo ibadah yang pas untuk keluarga Anda</h2>
        <p class="font-body-md text-body-md text-on-surface-variant">Semua harga sudah termasuk visa, hotel dekat Masjid, makan, dan pendampingan penuh.</p>
      </div>
      <div data-reveal class="flex items-center gap-2">
        <button type="button" data-slider-prev="paket" class="w-10 h-10 rounded-full bg-white border border-outline-variant text-primary inline-flex items-center justify-center hover:bg-primary hover:text-white transition-colors" aria-label="Paket sebelumnya">
          <span class="material-symbols-outlined text-[20px]">chevron_left</span>
        </button>
        <button type="button" data-slider-next="paket" class="w-10 h-10 rounded-full bg-white border border-outline-variant text-primary inline-flex items-center justify-center hover:bg-primary hover:text-white transition-colors" aria-label="Paket berikutnya">
          <span class="material-symbols-outlined text-[20px]">chevron_right</span>
        </button>
      </div>
    </div>

    <?php if ($paketList): ?>
    <div data-slider="paket" class="flex gap-space-md overflow-x-auto no-scrollbar snap-x snap-mandatory scroll-smooth pb-2">
      <?php foreach ($paketList as $pk): ?>
      <?php $pkHarga = $pk['harga_min'] !== null ? 'Mulai ' . rupiah($pk['harga_min']) : 'Hubungi kami'; ?>
      <a href="<?= url('paket/' . $pk['slug']) ?>" class="group shrink-0 w-[300px] snap-start bg-white rounded-2xl border border-outline-variant overflow-hidden shadow-sm hover:shadow-md transition-all hover:-translate-y-1 flex flex-col" data-reveal>
        <div class="relative overflow-hidden aspect-[4/3]">
          <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async" alt="<?= e($pk['nama']) ?>" src="<?= upload_url($pk['thumbnail'], 'paket') ?>"/>
          <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-white/90 backdrop-blur-sm text-primary font-label-sm text-label-sm font-semibold border border-primary-container"><?= (int) $pk['durasi_hari'] ?> Hari</span>
        </div>
        <div class="flex flex-col flex-1 p-space-md">
          <h3 class="font-headline-sm text-headline-sm text-on-surface mb-space-xs"><?= e($pk['nama']) ?></h3>
          <p class="font-body-sm text-body-sm text-on-surface-variant mb-space-md line-clamp-2"><?= e(excerpt($pk['deskripsi'], 130)) ?></p>
          <div class="mt-auto pt-space-sm border-t border-outline-variant flex items-end justify-between gap-space-xs">
            <div>
              <span class="font-label-sm text-label-sm text-on-surface-variant block"><?= $pkHarga ?></span>
              <span class="font-label-sm text-label-sm font-semibold text-secondary">/ jamaah</span>
            </div>
            <span class="inline-flex items-center gap-1 font-label-sm text-label-sm font-semibold text-primary">
              Detail <span class="material-symbols-outlined text-[16px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
            </span>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <div data-reveal class="mt-space-md text-center">
      <a class="inline-flex items-center gap-2 px-space-md py-space-sm bg-primary text-white font-label-md text-label-md rounded-xl hover:bg-primary/90 transition-colors" href="<?= url('paket') ?>">
        Semua Paket Umroh <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
      </a>
    </div>
    <?php else: ?>
    <p class="font-body-md text-body-md text-on-surface-variant bg-white border border-outline-variant rounded-2xl p-space-md">Paket umroh sedang disusun. Silakan konsultasikan rencana perjalanan Anda lebih dulu.</p>
    <?php endif; ?>
  </div>
</section>

<?php /* ==================== MENGAPA SAKINAH (4 pilar) ==================== */ ?>
<section class="w-full bg-surface py-space-xl text-on-surface">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="text-center max-w-xl mx-auto mb-space-lg" data-reveal>
      <span class="font-label-md text-label-md text-primary font-semibold block mb-space-xs">Kenapa Sakinah Journeys</span>
      <h2 class="font-headline-lg text-headline-lg text-on-surface">Kebutuhan keluarga diurus, Anda tinggal beribadah</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md">
      <?php foreach ($pilar as $pi): ?>
      <div class="bg-surface-container/70 rounded-2xl p-space-md border border-primary-container/40 text-center flex flex-col items-center" data-reveal>
        <span class="w-14 h-14 rounded-full bg-primary-container text-primary flex items-center justify-center mb-space-sm">
          <span class="material-symbols-outlined text-[28px]"><?= e($pi['ikon']) ?></span>
        </span>
        <h3 class="font-headline-sm text-headline-sm text-on-surface mb-1"><?= e($pi['judul']) ?></h3>
        <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed"><?= e($pi['teks']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <div data-reveal class="mt-space-lg bg-secondary-fixed/60 rounded-2xl px-space-md py-space-md text-center flex flex-wrap items-center justify-center gap-x-space-lg gap-y-2 text-on-secondary-fixed border border-secondary-fixed-dim/50">
      <span class="inline-flex items-center gap-2 font-label-md text-label-md"><span class="material-symbols-outlined text-[18px] text-on-secondary-fixed-variant">schedule</span> 3 sesi manasik sebelum berangkat</span>
      <span class="inline-flex items-center gap-2 font-label-md text-label-md"><span class="material-symbols-outlined text-[18px] text-on-secondary-fixed-variant">wheelchair_pickup</span> Pendampingan khusus jamaah lansia</span>
      <span class="inline-flex items-center gap-2 font-label-md text-label-md"><span class="material-symbols-outlined text-[18px] text-on-secondary-fixed-variant">support_agent</span> Konsultasi 1×1 bersama pembimbing</span>
    </div>
  </div>
</section>

<?php /* ==================== JADWAL KEBERANGKATAN ==================== */ ?>
<section class="w-full bg-surface-container py-space-xl text-on-surface">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="flex flex-wrap items-end justify-between gap-space-md mb-space-lg">
      <div class="max-w-xl" data-reveal>
        <span class="font-label-md text-label-md text-primary font-semibold block mb-space-xs">Jadwal Keberangkatan</span>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-space-xs">Keberangkatan terdekat yang masih dibuka</h2>
        <p class="font-body-md text-body-md text-on-surface-variant">Klik jadwal di bawah untuk melihat rincian dan sisa kursi tiap paket.</p>
      </div>
      <a data-reveal class="inline-flex items-center gap-1 font-label-md text-label-md font-semibold text-primary" href="<?= url('paket') ?>">Lihat semua paket <span class="material-symbols-outlined text-[16px]">arrow_forward</span></a>
    </div>

    <?php if ($jadwalList): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-space-md">
      <?php foreach ($jadwalList as $j): ?>
      <?php
        $sisa = (int) $j['kuota'];
        $pct = (int) round($sisa / max(1, $kuotaMax) * 100);
        $warna = $pct >= 40 ? 'bg-primary' : ($pct >= 20 ? 'bg-secondary' : 'bg-error');
        $bijiWarna = $pct >= 40 ? 'text-primary border-primary-container bg-primary-container/40' : ($pct >= 20 ? 'text-secondary border-secondary-fixed-dim bg-secondary-fixed/50' : 'text-error border-error-container bg-error-container');
      ?>
      <a href="<?= url('paket/' . $j['slug_paket']) ?>" class="bg-white rounded-2xl border border-outline-variant p-space-md shadow-sm hover:shadow-md hover:border-primary transition-all flex flex-col gap-space-sm group" data-reveal>
        <div class="flex items-center justify-between gap-2">
          <span class="inline-flex items-center gap-1.5 font-label-sm text-label-sm text-on-surface-variant">
            <span class="material-symbols-outlined text-[16px] text-primary">event</span>
            <?= tanggal($j['tanggal_berangkat'], 'long') ?>
          </span>
          <?= badge(ucfirst($j['status']), $j['status'] === 'dibuka' ? 'teal' : 'gray') ?>
        </div>
        <h3 class="font-headline-sm text-headline-sm text-primary"><?= e($j['nama_paket']) ?></h3>
        <div>
          <div class="flex items-center justify-between mb-1">
            <span class="font-label-sm text-label-sm text-on-surface-variant">Sisa kursi</span>
            <span class="font-label-sm text-label-sm font-bold px-2 py-0.5 rounded-full border <?= $bijiWarna ?>"><?= $sisa ?> kursi</span>
          </div>
          <div class="h-2 rounded-full bg-surface-container-high overflow-hidden">
            <div class="h-full <?= $warna ?> rounded-full" style="width:<?= $pct ?>%"></div>
          </div>
        </div>
        <span class="inline-flex items-center gap-1 font-label-sm text-label-sm font-semibold text-primary mt-auto">
          Rincian paket <span class="material-symbols-outlined text-[16px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
        </span>
      </a>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="font-body-md text-body-md text-on-surface-variant bg-white border border-outline-variant rounded-2xl p-space-md">Jadwal keberangkatan terdekat sedang disusun. Kirim konsultasi agar kami kabari lebih dulu.</p>
    <?php endif; ?>
  </div>
</section>

<?php /* ==================== TESTIMONI (karusel + video) ==================== */ ?>
<section class="w-full bg-surface py-space-xl text-on-surface overflow-hidden">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="text-center max-w-xl mx-auto mb-space-lg" data-reveal>
      <span class="font-label-md text-label-md text-primary font-semibold block mb-space-xs">Kesaksian Jamaah</span>
      <h2 class="font-headline-lg text-headline-lg text-on-surface">Cerita senang keluarga yang sudah berangkat</h2>
    </div>

    <!-- Kartu video -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md mb-space-lg" data-reveal>
      <?php foreach ($videoContoh as $vc): ?>
      <button type="button" data-video-open data-video-src="<?= e($vc['sumber']) ?>" class="group relative rounded-2xl overflow-hidden aspect-video border border-outline-variant shadow-sm text-left">
        <img class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async" alt="Video: <?= e($vc['judul']) ?>" src="<?= upload_url($vc['poster'], 'galeri') ?>"/>
        <span class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></span>
        <span class="absolute inset-0 flex items-center justify-center">
          <span class="w-14 h-14 rounded-full bg-white/90 backdrop-blur-sm text-primary flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-[30px]">play_arrow</span>
          </span>
        </span>
        <span class="absolute bottom-3 left-4 right-4 text-white">
          <span class="block font-headline-sm text-headline-sm"><?= e($vc['judul']) ?></span>
          <span class="font-label-sm text-label-sm text-white/80">Contoh video · <?= e(trim((string)($profil['nama_perusahaan'] ?? '')) ?: 'Sakinah Journeys') ?></span>
        </span>
      </button>
      <?php endforeach; ?>
    </div>

    <!-- Karusel ulasan -->
    <?php if ($testimoniList): ?>
    <div class="flex items-center gap-2 mb-space-md justify-center" data-reveal>
      <button type="button" data-slider-prev="testimoni" class="w-10 h-10 rounded-full bg-white border border-outline-variant text-primary inline-flex items-center justify-center hover:bg-primary hover:text-white transition-colors" aria-label="Ulasan sebelumnya">
        <span class="material-symbols-outlined text-[20px]">chevron_left</span>
      </button>
      <button type="button" data-slider-next="testimoni" class="w-10 h-10 rounded-full bg-white border border-outline-variant text-primary inline-flex items-center justify-center hover:bg-primary hover:text-white transition-colors" aria-label="Ulasan berikutnya">
        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
      </button>
    </div>
    <div data-slider="testimoni" class="flex gap-space-md overflow-x-auto no-scrollbar snap-x snap-mandatory scroll-smooth">
      <?php foreach ($testimoniList as $ts): ?>
      <?php $foto = !empty($ts['foto']) ? upload_url($ts['foto'], 'galeri') : ''; ?>
      <figure class="shrink-0 snap-start w-full sm:w-[420px] bg-surface-container/70 rounded-2xl border border-primary-container/40 p-space-md flex flex-col gap-space-sm shadow-sm" data-reveal>
        <div class="flex items-center justify-between gap-2">
          <span class="inline-flex items-center gap-0.5 text-secondary">
            <?php for ($b = 1; $b <= 5; $b++): ?><span class="material-symbols-outlined text-[18px]">star</span><?php endfor; ?>
          </span>
          <?php if (!empty($ts['contoh'])): ?>
          <span class="font-label-sm text-label-sm text-on-surface-variant bg-white border border-outline-variant rounded-full px-2 py-0.5">Contoh ulasan</span>
          <?php endif; ?>
        </div>
        <blockquote class="font-body-md text-body-md text-on-surface italic leading-relaxed">&ldquo;<?= e(excerpt($ts['teks'], 220)) ?>&rdquo;</blockquote>
        <figcaption class="flex items-center gap-space-sm mt-auto pt-space-sm border-t border-primary-container/40">
          <span class="w-10 h-10 rounded-full overflow-hidden bg-primary-container text-primary flex items-center justify-center font-label-md font-bold shrink-0">
            <?php if ($foto !== ''): ?><img class="w-full h-full object-cover" loading="lazy" decoding="async" alt="<?= e($ts['nama']) ?>" src="<?= $foto ?>"/><?php else: ?><?= e($initials($ts)) ?><?php endif; ?>
          </span>
          <div>
            <span class="block font-label-md text-label-md font-semibold text-on-surface"><?= e($ts['nama']) ?></span>
            <span class="font-body-sm text-body-sm text-on-surface-variant"><?= !empty($ts['asal_kota']) ? e($ts['asal_kota']) : 'Jamaah Sakinah Journeys' ?></span>
          </div>
        </figcaption>
      </figure>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php /* ==================== GALERI (slider kompak) ==================== */ ?>
<section class="w-full bg-surface-container py-space-xl text-on-surface overflow-hidden">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="flex flex-wrap items-end justify-between gap-space-md mb-space-lg">
      <div class="max-w-xl" data-reveal>
        <span class="font-label-md text-label-md text-primary font-semibold block mb-space-xs">Galeri Perjalanan</span>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-space-xs">Momen tenang yang membekas</h2>
        <p class="font-body-md text-body-md text-on-surface-variant">Cuplikan suasana pendampingan dan kebersamaan di Tanah Suci.</p>
      </div>
      <div class="flex items-center gap-2" data-reveal>
        <button type="button" data-slider-prev="galeri" class="w-10 h-10 rounded-full bg-white border border-outline-variant text-primary inline-flex items-center justify-center hover:bg-primary hover:text-white transition-colors" aria-label="Galeri sebelumnya">
          <span class="material-symbols-outlined text-[20px]">chevron_left</span>
        </button>
        <button type="button" data-slider-next="galeri" class="w-10 h-10 rounded-full bg-white border border-outline-variant text-primary inline-flex items-center justify-center hover:bg-primary hover:text-white transition-colors" aria-label="Galeri berikutnya">
          <span class="material-symbols-outlined text-[20px]">chevron_right</span>
        </button>
      </div>
    </div>

    <?php if ($galeriList): ?>
    <div data-slider="galeri" class="flex gap-space-md overflow-x-auto no-scrollbar snap-x snap-mandatory scroll-smooth pb-2">
      <?php foreach ($galeriList as $gl): ?>
      <button type="button" data-lightbox-open data-lightbox-img="<?= upload_url($gl['gambar'], 'galeri') ?>" data-lightbox-caption="<?= e($gl['judul']) ?>" class="group shrink-0 snap-start w-[260px] rounded-2xl overflow-hidden border border-outline-variant shadow-sm relative aspect-[4/3] text-left" data-reveal>
        <img class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async" alt="<?= e($gl['judul']) ?>" src="<?= upload_url($gl['gambar'], 'galeri') ?>"/>
        <span class="absolute inset-0 bg-gradient-to-t from-black/55 via-transparent to-transparent opacity-90"></span>
        <span class="absolute bottom-3 left-3 right-3 text-white font-body-sm text-body-sm leading-snug"><?= e($gl['judul']) ?></span>
      </button>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="font-body-md text-body-md text-on-surface-variant bg-white border border-outline-variant rounded-2xl p-space-md">Galeri perjalanan sedang dikumpulkan.</p>
    <?php endif; ?>
  </div>
</section>

<?php /* ==================== ARTIKEL (inspirasi) ==================== */ ?>
<section class="w-full bg-surface py-space-xl text-on-surface">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="flex flex-wrap items-end justify-between gap-space-md mb-space-lg">
      <div class="max-w-xl" data-reveal>
        <span class="font-label-md text-label-md text-primary font-semibold block mb-space-xs">Inspirasi &amp; Panduan</span>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-space-xs">Bekal batin sebelum berangkat</h2>
      </div>
      <a data-reveal class="inline-flex items-center gap-1 font-label-md text-label-md font-semibold text-primary" href="<?= url('artikel') ?>">Semua artikel <span class="material-symbols-outlined text-[16px]">arrow_forward</span></a>
    </div>

    <?php if ($artikelList): ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
      <?php foreach ($artikelList as $ar): ?>
      <a href="<?= url('artikel/' . $ar['slug']) ?>" class="group bg-surface-container/70 rounded-2xl border border-primary-container/40 overflow-hidden shadow-sm hover:shadow-md transition-all flex flex-col" data-reveal>
        <div class="aspect-[16/9] overflow-hidden">
          <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async" alt="<?= e($ar['judul']) ?>" src="<?= upload_url($ar['thumbnail'], 'artikel') ?>"/>
        </div>
        <div class="p-space-md flex flex-col flex-1">
          <span class="font-label-sm text-label-sm text-on-surface-variant mb-1"><?= tanggal($ar['created_at']) ?></span>
          <h3 class="font-headline-sm text-headline-sm text-on-surface leading-snug mb-space-xs line-clamp-2"><?= e($ar['judul']) ?></h3>
          <span class="inline-flex items-center gap-1 font-label-sm text-label-sm font-semibold text-primary mt-auto">Baca <span class="material-symbols-outlined text-[16px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span></span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php /* ==================== FAQ ==================== */ ?>
<section class="w-full bg-surface-container py-space-xl text-on-surface">
  <div class="max-w-4xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="text-center max-w-xl mx-auto mb-space-lg" data-reveal>
      <span class="font-label-md text-label-md text-primary font-semibold block mb-space-xs">Tanya Jawab</span>
      <h2 class="font-headline-lg text-headline-lg text-on-surface">Yang sering ditanyakan keluarga</h2>
    </div>

    <?php if ($faqList): ?>
    <div class="flex flex-col gap-space-sm">
      <?php foreach ($faqList as $fi => $fq): ?>
      <div class="faq-item <?= $fi === 0 ? 'open' : '' ?> bg-white rounded-xl border border-outline-variant overflow-hidden" data-faq-item data-reveal>
        <button type="button" data-faq-toggle class="w-full flex items-center justify-between gap-4 px-space-md py-space-sm text-left" aria-expanded="<?= $fi === 0 ? 'true' : 'false' ?>">
          <span class="font-headline-sm text-headline-sm text-on-surface"><?= e($fq['pertanyaan']) ?></span>
          <span class="shrink-0 w-8 h-8 rounded-full bg-surface-container text-primary flex items-center justify-center">
            <span class="material-symbols-outlined text-[20px] transition-transform"><?= $fi === 0 ? 'remove' : 'add' ?></span>
          </span>
        </button>
        <div class="faq-body">
          <p class="px-space-md pb-space-md font-body-md text-body-md text-on-surface-variant leading-relaxed"><?= e($fq['jawaban']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div data-reveal class="mt-space-md text-center">
      <a class="inline-flex items-center gap-1 font-label-md text-label-md font-semibold text-primary" href="<?= url('faq') ?>">Pertanyaan lainnya <span class="material-symbols-outlined text-[16px]">arrow_forward</span></a>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php /* ==================== CTA KONSULTASI ==================== */ ?>
<section class="w-full bg-surface pb-space-xl pt-space-sm" id="konsultasi">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="bg-primary text-white rounded-2xl p-space-lg lg:p-space-xl shadow-xl relative overflow-hidden border border-primary/60">
      <div aria-hidden="true" class="absolute -right-20 -bottom-20 w-96 h-96 rounded-full bg-white/5 pointer-events-none blur-3xl"></div>
      <div aria-hidden="true" class="absolute -left-20 -top-20 w-80 h-80 rounded-full bg-secondary-fixed/20 pointer-events-none blur-2xl"></div>
      <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-space-lg lg:gap-gutter-desktop items-center">
        <div class="lg:col-span-6 flex flex-col gap-space-sm">
          <span class="font-label-md text-label-md text-secondary-fixed font-semibold">Konsultasi Gratis &amp; Tanpa Komitmen</span>
          <h3 class="font-headline-lg text-headline-lg text-white leading-snug">Rencanakan umroh keluarga dengan bantuan kami</h3>
          <p class="font-body-md text-body-md text-white/80 leading-relaxed max-w-lg">Sampaikan jumlah keluarga, kondisi kesehatan, dan rencana bulan berangkat. Tim kami akan menyarankan paket yang paling nyaman.</p>
          <div class="flex flex-col gap-space-xs text-white/90 font-body-sm text-body-sm">
            <div class="flex items-center gap-2"><span class="material-symbols-outlined text-secondary-fixed text-[18px]">schedule</span><span><?= e($profil['jam_operasional'] ?? 'Senin – Sabtu, 08.00 – 17.00 WIB') ?></span></div>
            <div class="flex items-center gap-2"><span class="material-symbols-outlined text-secondary-fixed text-[18px]">call</span><span><?= e(trim((string)($profil['telepon'] ?? '')) ?: '+62 811 8892 011') ?></span></div>
            <div class="flex items-center gap-2"><span class="material-symbols-outlined text-secondary-fixed text-[18px]">email</span><span><?= e($profil['email'] ?? '') ?></span></div>
          </div>
        </div>

        <div class="lg:col-span-6 bg-white text-on-surface p-space-md lg:p-space-lg rounded-2xl shadow-md border border-outline-variant">
          <form class="flex flex-col gap-space-sm" method="post" action="<?= url('kontak') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" value="kirim_inquiry"/>
            <div>
              <label class="block font-label-md text-label-md text-on-surface font-semibold mb-1" for="nama_lengkap">Nama Lengkap</label>
              <input class="w-full px-space-md py-space-sm bg-surface-container border border-outline-variant rounded-xl text-body-sm text-on-surface placeholder-on-surface-variant focus:outline-none focus:border-primary transition-colors" id="nama_lengkap" name="nama" placeholder="Contoh: Hendra Pratama" required="" type="text"/>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface font-semibold mb-1" for="nomor_wa">Nomor WhatsApp / Surel</label>
              <input class="w-full px-space-md py-space-sm bg-surface-container border border-outline-variant rounded-xl text-body-sm text-on-surface placeholder-on-surface-variant focus:outline-none focus:border-primary transition-colors" id="nomor_wa" name="kontak" placeholder="Contoh: 0812 3456 7890" required="" type="text"/>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface font-semibold mb-1" for="pilihan_paket">Paket yang Diminati</label>
              <select class="w-full px-space-md py-space-sm bg-surface-container border border-outline-variant rounded-xl text-body-sm text-on-surface focus:outline-none focus:border-primary transition-colors" id="pilihan_paket" name="paket_id">
                <option value="">Belum menentukan — minta rekomendasi</option>
                <?php foreach (model('paket')->getAllAktif() as $pl): ?>
                <option value="<?= (int) $pl['id'] ?>"><?= e($pl['nama']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface font-semibold mb-1" for="pesan_konsultasi">Catatan Kebutuhan</label>
              <textarea class="w-full px-space-md py-space-sm bg-surface-container border border-outline-variant rounded-xl text-body-sm text-on-surface placeholder-on-surface-variant focus:outline-none focus:border-primary transition-colors" id="pesan_konsultasi" name="pesan" rows="3" placeholder="Contoh: rencana berangkat bersama ibu usia 73 tahun, ingin kamar triple."></textarea>
            </div>
            <button class="w-full mt-space-xs py-space-sm px-space-lg bg-primary text-white font-label-md text-label-md rounded-xl shadow-sm hover:bg-primary/90 transition-colors" type="submit">
              Jadwalkan Konsultasi Keluarga
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?php /* ==================== MODAL & TOMBOL WA ==================== */ ?>

<div id="video-modal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/70" role="dialog" aria-modal="true" aria-label="Pemutar video">
  <button type="button" data-modal-close="video-modal" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/15 text-white flex items-center justify-center hover:bg-white/25 transition-colors" aria-label="Tutup video">
    <span class="material-symbols-outlined">close</span>
  </button>
  <video id="video-player" class="max-h-[80vh] max-w-full rounded-xl bg-black shadow-2xl" controls="" playsinline=""></video>
</div>

<div id="lightbox-modal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/80" role="dialog" aria-modal="true" aria-label="Lihat galeri">
  <button type="button" data-modal-close="lightbox-modal" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/15 text-white flex items-center justify-center hover:bg-white/25 transition-colors" aria-label="Tutup galeri">
    <span class="material-symbols-outlined">close</span>
  </button>
  <figure class="max-w-4xl w-full">
    <img id="lightbox-img" class="max-h-[75vh] w-full object-contain rounded-xl shadow-2xl" alt="Galeri"/>
    <figcaption id="lightbox-caption" class="mt-3 text-center text-white font-body-md text-body-md"></figcaption>
  </figure>
</div>

<a href="<?= e($waLink) ?>" target="_blank" rel="noopener" aria-label="Konsultasi via WhatsApp" class="fixed bottom-5 right-5 z-50 w-14 h-14 rounded-full bg-[#25D366] text-white shadow-lg flex items-center justify-center hover:scale-105 transition-transform">
  <svg viewBox="0 0 32 32" class="w-7 h-7 fill-current" aria-hidden="true"><path d="M16 3C9.4 3 4 8.4 4 15c0 2.1.6 4.2 1.6 6L4 29l8.2-1.6c1.7.9 3.6 1.4 5.6 1.4C24.6 28.8 28 23.4 28 17 28 9.4 22.6 3 16 3zm0 23c-1.8 0-3.5-.5-5-1.4l-.4-.2-4.8 1 1-4.7-.2-.4c-1-1.5-1.6-3.3-1.6-5.3C5 9.9 9.9 5 16 5c6 0 11 4.9 11 11-.1 6-5.1 10-11 10zm5-7.5c-.3-.1-1.7-.8-1.9-.9-.3-.1-.5-.1-.7.1-.2.3-.7.9-.8 1.1-.2.2-.3.2-.6.1-1.7-.8-2.8-1.4-3.9-3.2-.3-.5.3-.5.8-1.6.1-.2 0-.4 0-.5-.1-.2-.8-1.8-.9-2-.3-.7-.6-.6-.8-.6h-.7c-.2 0-.6.1-.9.4-.3.3-1.1 1.1-1.1 2.7s1.1 3.1 1.3 3.3c.2.2 2.2 3.4 5.4 4.8 2.6 1.1 3.2.9 3.8.8 1.1-.2 1.7-.8 2-1.6.2-.7.3-1.4.2-1.6-.1-.1-.3-.2-.6-.4z"/></svg>
</a>

<script>
(function () {
  'use strict';
  var t = document.documentElement;
  t.style.opacity = '1';

  /* Reveal saat scroll */
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) {
          en.target.classList.add('is-visible');
          io.unobserve(en.target);
        }
      });
    }, { threshold: 0.12 });
    document.querySelectorAll('[data-reveal]').forEach(function (el) { io.observe(el); });
  } else {
    document.querySelectorAll('[data-reveal]').forEach(function (el) { el.classList.add('is-visible'); });
  }

  /* Slider horizontal: dua tombol per grup */
  var sliders = {};
  document.querySelectorAll('[data-slider]').forEach(function (wrap) {
    var id = wrap.getAttribute('data-slider');
    sliders[id] = wrap;
    function scrollBy(dir) {
      var card = wrap.querySelector(':scope > *');
      var step = card ? card.getBoundingClientRect().width + 16 : 300;
      wrap.scrollBy({ left: dir * step, behavior: 'smooth' });
    }
    document.querySelectorAll('[data-slider-prev="' + id + '"]').forEach(function (b) { b.addEventListener('click', function () { scrollBy(-1); }); });
    document.querySelectorAll('[data-slider-next="' + id + '"]').forEach(function (b) { b.addEventListener('click', function () { scrollBy(1); }); });
  });

  /* Pencarian hero: arahkan ke detail paket */
  var heroSearch = document.getElementById('hero-search');
  if (heroSearch) {
    heroSearch.addEventListener('submit', function (ev) {
      ev.preventDefault();
      var sel = document.getElementById('hero-paket');
      if (sel && sel.value) {
        window.location.href = '<?= url('') ?>' + 'paket/' + encodeURIComponent(sel.value);
      } else {
        window.location.hash = 'konsultasi';
      }
    });
  }

  /* Modal video */
  var videoModal = document.getElementById('video-modal');
  var videoPlayer = document.getElementById('video-player');
  document.querySelectorAll('[data-video-open]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      videoPlayer.src = btn.getAttribute('data-video-src');
      videoModal.classList.remove('hidden');
      videoModal.classList.add('flex');
      videoPlayer.play();
    });
  });

  /* Lightbox galeri */
  var lbModal = document.getElementById('lightbox-modal');
  var lbImg = document.getElementById('lightbox-img');
  var lbCap = document.getElementById('lightbox-caption');
  document.querySelectorAll('[data-lightbox-open]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      lbImg.src = btn.getAttribute('data-lightbox-img');
      lbImg.alt = btn.getAttribute('data-lightbox-caption') || '';
      lbCap.textContent = btn.getAttribute('data-lightbox-caption') || '';
      lbModal.classList.remove('hidden');
      lbModal.classList.add('flex');
    });
  });

  /* Tutup modal */
  document.querySelectorAll('[data-modal-close]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var m = document.getElementById(btn.getAttribute('data-modal-close'));
      if (!m) return;
      m.classList.add('hidden');
      m.classList.remove('flex');
      if (m === videoModal) { videoPlayer.pause(); videoPlayer.removeAttribute('src'); videoPlayer.load(); }
    });
  });
  [videoModal, lbModal].forEach(function (m) {
    if (!m) return;
    m.addEventListener('click', function (ev) {
      if (ev.target === m) {
        m.classList.add('hidden');
        m.classList.remove('flex');
        if (m === videoModal) { videoPlayer.pause(); videoPlayer.removeAttribute('src'); videoPlayer.load(); }
      }
    });
  });
  document.addEventListener('keydown', function (ev) {
    if (ev.key !== 'Escape') return;
    [videoModal, lbModal].forEach(function (m) {
      if (!m) return;
      m.classList.add('hidden');
      m.classList.remove('flex');
      if (m === videoModal) { videoPlayer.pause(); videoPlayer.removeAttribute('src'); videoPlayer.load(); }
    });
  });

  /* Accordion FAQ */
  document.querySelectorAll('[data-faq-toggle]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var item = btn.closest('[data-faq-item]');
      var isOpen = item.classList.contains('open');
      document.querySelectorAll('[data-faq-item].open').forEach(function (o) {
        if (o !== item) {
          o.classList.remove('open');
          o.querySelector('[data-faq-toggle]').setAttribute('aria-expanded', 'false');
          o.querySelector('.material-symbols-outlined').textContent = 'add';
        }
      });
      item.classList.toggle('open', !isOpen);
      btn.setAttribute('aria-expanded', String(!isOpen));
      item.querySelector('.material-symbols-outlined').textContent = !isOpen ? 'remove' : 'add';
    });
  });
})();
</script>
<?php require PUBLIC_PATH . '/partials/footer.php'; ?>