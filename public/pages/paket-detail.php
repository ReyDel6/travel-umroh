<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/** Halaman Detail Paket — design: paket-detail.html */
$slug = isset($_GET['slug']) ? (string) $_GET['slug'] : '';
$paket = $slug !== '' ? model('paket')->getBySlug($slug) : null;

if (!$paket || $paket['status'] !== 'aktif') {
    http_response_code(404);
    $page = '404';
    $metaTitle = 'Paket Tidak Ditemukan — ' . APP_NAME;
    $metaDescription = 'Paket umroh yang Anda cari tidak tersedia atau sudah tidak aktif.';
    require PUBLIC_PATH . '/pages/404.php';
    return;
}

$id = (int) $paket['id'];
$metaTitle = ($paket['meta_title'] ?? '') ?: ($paket['nama'] . ' — ' . APP_NAME);
$metaDescription = ($paket['meta_description'] ?? '') ?: excerpt($paket['deskripsi'], 160);

$canonicalPath = 'paket/' . $paket['slug'];
$modelPaket = model('paket');
$hargaList = $modelPaket->getHarga($id);
$jadwalList = array_values(array_filter(
    model('jadwal')->getByPaket($id),
    static fn (array $j): bool => (string) $j['tanggal_berangkat'] >= date('Y-m-d')
));
$fasilitasList = $modelPaket->getFasilitas($id);
$hotelList = $modelPaket->getHotels($id);
$maskapaiList = $modelPaket->getMaskapai($id);
$faqList = array_slice(model('faq')->getPublic(), 0, 3);
$galeriList = model('galeri')->getPublic(null, 3, 0);

$durasi = (int) $paket['durasi_hari'];
$hargaMap = [];
foreach ($hargaList as $h) {
    $hargaMap[$h['tipe_kamar']] = $h;
}

$tipeInfo = [
    'quad' => [
        'label'    => 'Quad',
        'sub'      => 'Sekamar Berempat',
        'desc'     => 'Kenyamanan optimal untuk keluarga besar dengan ruang gerak yang tetap leluasa.',
        'icon'     => 'single_bed',
        'detail'   => '4 Ranjang Mandiri (Single Bed Premium)',
        'rekom'    => 'Rekomendasi: keluarga besar atau rombongan kerabat',
        'cta'      => 'Ajukan Kamar Quad',
        'featured' => false,
    ],
    'triple' => [
        'label'    => 'Triple',
        'sub'      => 'Sekamar Bertiga',
        'desc'     => 'Pilihan ideal bagi orang tua bersama satu orang pendamping khusus keluarga.',
        'icon'     => 'bed',
        'detail'   => '3 Ranjang Mandiri Berjarak Nyaman',
        'rekom'    => 'Akses lorong kamar lapang, memudahkan kursi roda masuk langsung',
        'cta'      => 'Ajukan Kamar Triple',
        'featured' => true,
    ],
    'double' => [
        'label'    => 'Double',
        'sub'      => 'Sekamar Berdua',
        'desc'     => 'Privasi mutlak dan ketenangan beristirahat bagi pasangan suami-istri atau orang tua.',
        'icon'     => 'king_bed',
        'detail'   => '1 King Bed Ekstra Luas atau 2 Twin Bed',
        'rekom'    => 'Suasana sunyi hening demi kenyamanan waktu zikir mandiri',
        'cta'      => 'Ajukan Kamar Double',
        'featured' => false,
    ],
];

$mosaic = [];
$mosaic[] = [
    'src'   => upload_url($paket['thumbnail'], 'paket'),
    'label' => 'Pelataran Masjid Nabawi Madinah',
    'title' => $paket['nama'],
];
if ($hotelList) {
    $mosaic[] = [
        'src'   => upload_url($hotelList[0]['gambar'] ?? null, 'hotel'),
        'label' => ($hotelList[0]['kota'] ?? 'Madinah') . ' • ' . ($hotelList[0]['kelas'] ?? 'Bintang 5'),
        'title' => $hotelList[0]['nama'],
    ];
}
if (is_file(UPLOAD_PATH . '/galeri/haramain-express.jpg')) {
    $mosaic[] = [
        'src'   => upload_url('haramain-express.jpg', 'galeri'),
        'label' => 'Kereta Cepat Haramain Express',
        'title' => 'Perjalanan Antar Kota yang Nyaman',
    ];
}
$mosaic[] = [
    'src'   => upload_url('interlude-hotel.jpg', 'galeri'),
    'label' => 'Waktu Kebersamaan & Pemulihan',
    'title' => 'Jeda Istirahat yang Lapang',
];

require PUBLIC_PATH . '/partials/head.php';
require PUBLIC_PATH . '/partials/header.php';
?>
<!-- Breadcrumb & Status Bar -->
<section class="w-full bg-surface-container-low/70 py-space-sm">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop flex flex-wrap items-center justify-between gap-space-sm">
    <nav class="flex items-center gap-space-xs font-label-md text-label-md text-on-surface-variant">
      <a class="hover:text-primary transition-colors" href="<?= url('') ?>">Beranda</a>
      <span class="text-outline-variant">/</span>
      <a class="hover:text-primary transition-colors" href="<?= url('paket') ?>">Paket Ibadah</a>
      <span class="text-outline-variant">/</span>
      <span class="text-tertiary font-medium"><?= e($paket['nama']) ?></span>
    </nav>
    <div class="flex items-center gap-space-xs">
      <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
      <span class="font-label-sm text-label-sm text-tertiary">
        <?= $jadwalList ? count($jadwalList) . ' jadwal keberangkatan terbuka' : 'Jadwal menyusul — konsultasi dulu' ?>
        &bull; Rasio Pendamping <?= !empty($profil['stat_rasio_pembimbing']) ? '1:' . (int) $profil['stat_rasio_pembimbing'] : '1:15' ?>
      </span>
    </div>
  </div>
</section>

<!-- Editorial Title & Intro Header -->
<header class="w-full py-space-lg lg:py-space-xl">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="max-w-4xl flex flex-col gap-space-sm">
      <div class="inline-flex items-center gap-space-xs self-start px-space-sm py-1 rounded-full bg-primary/10 text-primary font-label-sm text-label-sm">
        <span class="material-symbols-outlined text-[15px]">elderly</span>
        <span>Program Khusus Ramah Lansia &amp; Keluarga</span>
      </div>
      <h1 class="font-display-lg text-display-lg text-on-surface tracking-tight leading-tight"><?= e($paket['nama']) ?></h1>
      <p class="font-headline-sm text-headline-sm text-secondary font-normal italic">
        Ritme ibadah yang teduh, jeda istirahat leluasa, dan langkah yang terawat dengan santun.
      </p>
      <p class="font-body-md text-body-md text-on-surface-variant max-w-3xl pt-space-xs">
        <?= $durasi ?> Hari <?= max(0, $durasi - 1) ?> Malam &bull; <?= e($maskapaiList ? implode(' &amp; ', array_column($maskapaiList, 'nama')) : 'Penerbangan langsung PP') ?>
        &bull; Didampingi dokter kontingen serta pembimbing ibadah berpengalaman untuk kenyamanan optimal keluarga terkasih.
      </p>
    </div>

    <!-- Asymmetric Editorial Gallery Mosaic -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-space-md mt-space-lg">
      <div class="md:col-span-7 h-[360px] md:h-[480px] rounded-2xl overflow-hidden relative shadow-sm group">
        <img alt="<?= e($mosaic[0]['label']) ?>" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" src="<?= e($mosaic[0]['src']) ?>"/>
        <div class="absolute inset-0 bg-gradient-to-t from-inverse-surface/60 via-transparent to-transparent"></div>
        <div class="absolute bottom-space-md left-space-md right-space-md flex justify-between items-end text-surface gap-2">
          <div>
            <p class="font-label-sm text-label-sm tracking-widest uppercase opacity-80"><?= e($mosaic[0]['label']) ?></p>
            <p class="font-headline-sm text-headline-sm font-medium"><?= e($mosaic[0]['title']) ?></p>
          </div>
          <?php if ($hotelList): ?>
          <span class="px-space-sm py-0.5 rounded-full bg-surface-container-lowest/20 backdrop-blur-md font-label-sm text-label-sm text-surface text-right">
            <?= e($hotelList[0]['kelas'] ?? 'Bintang 5') ?> dekat pelataran
          </span>
          <?php endif; ?>
        </div>
      </div>
      <div class="md:col-span-5 grid grid-cols-2 md:grid-cols-1 gap-space-md h-auto md:h-[480px]">
        <?php foreach (array_slice($mosaic, 1, 3) as $m): ?>
        <div class="h-[170px] md:h-[148px] rounded-2xl overflow-hidden relative shadow-sm group">
          <img alt="<?= e($m['label']) ?>" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" src="<?= e($m['src']) ?>"/>
          <div class="absolute inset-0 bg-gradient-to-t from-inverse-surface/60 via-transparent to-transparent"></div>
          <div class="absolute bottom-space-xs left-space-sm right-space-sm text-surface">
            <span class="font-label-sm text-label-sm font-medium"><?= e($m['label']) ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</header>
<!-- Room Types & Transparent Pricing -->
<section class="w-full py-space-xl bg-surface-container-low">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-sm mb-space-lg">
      <div>
        <span class="font-label-sm text-label-sm text-primary tracking-widest uppercase">Transparansi Investasi Spiritual</span>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mt-1">Pilihan Konfigurasi Kamar</h2>
      </div>
      <p class="font-body-sm text-body-sm text-on-surface-variant max-w-md">
        Seluruh paket telah mencakup visa umroh reguler, tiket penerbangan langsung PP, hotel pelataran bintang lima,
        konsumsi harian lengkap, serta bimbingan muthawwif terdedikasi.
      </p>
    </div>

    <div class="flex flex-col gap-space-sm">
      <?php if ($hargaMap): ?>
      <?php foreach ($tipeInfo as $tipe => $info): if (!isset($hargaMap[$tipe])) { continue; } $h = $hargaMap[$tipe]; ?>
      <div class="bg-surface rounded-2xl p-space-md lg:p-space-lg shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-space-md transition-all hover:shadow-md<?= $info['featured'] ? ' ring-1 ring-secondary/20' : '' ?>">
        <div class="lg:w-1/4">
          <div class="flex items-center gap-space-xs mb-1 flex-wrap">
            <span class="font-headline-md text-headline-md text-on-surface font-normal"><?= e($info['label']) ?></span>
            <?php if ($info['featured']): ?>
            <span class="px-2 py-0.5 rounded bg-secondary-fixed/40 font-label-sm text-label-sm text-on-secondary-fixed font-semibold">Paling Diminati</span>
            <?php else: ?>
            <span class="px-2 py-0.5 rounded bg-surface-container-high font-label-sm text-label-sm text-on-surface-variant"><?= e($info['sub']) ?></span>
            <?php endif; ?>
          </div>
          <p class="font-body-sm text-body-sm text-on-surface-variant"><?= e($info['desc']) ?></p>
        </div>
        <div class="lg:w-1/3 flex flex-col gap-1">
          <div class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm">
            <span class="material-symbols-outlined text-[18px] text-primary"><?= e($info['icon']) ?></span>
            <span><?= e($info['detail']) ?></span>
          </div>
          <div class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm">
            <span class="material-symbols-outlined text-[18px] text-primary">group</span>
            <span><?= e($info['rekom']) ?></span>
          </div>
        </div>
        <div class="lg:w-1/4 flex flex-col items-start lg:items-end">
          <span class="font-label-sm text-label-sm text-on-surface-variant">Biaya per Jamaah</span>
          <div class="font-display-md text-display-md text-secondary font-normal"><?= rupiah($h['harga']) ?></div>
          <span class="font-label-sm text-label-sm text-on-surface-variant">All-in tanpa pungutan tersembunyi</span>
        </div>
        <div class="lg:w-auto">
          <a class="w-full lg:w-auto inline-flex items-center justify-center px-space-md py-space-sm rounded <?= $info['featured'] ? 'bg-secondary-fixed text-on-secondary-fixed hover:bg-secondary-fixed-dim font-semibold' : 'bg-primary text-on-primary hover:bg-primary-container' ?> font-label-md text-label-md transition-colors shadow-sm" href="#konsultasi">
            <?= e($info['cta']) ?>
          </a>
        </div>
      </div>
      <?php endforeach; ?>
      <?php else: ?>
      <div class="bg-surface rounded-2xl p-space-md lg:p-space-lg shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div>
          <span class="font-headline-md text-headline-md text-on-surface font-normal">Tarif belum dipublikasikan</span>
          <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Silakan hubungi konsultan kami untuk rincian biaya tiap konfigurasi kamar.</p>
        </div>
        <a class="inline-flex items-center justify-center px-space-md py-space-sm rounded bg-primary text-on-primary font-label-md text-label-md hover:bg-primary-container transition-colors shadow-sm" href="<?= url('kontak') ?>#konsultasi">Minta Rincian Biaya</a>
      </div>
      <?php endif; ?>
    </div>

    <div class="mt-space-md p-space-md rounded-xl bg-surface-container flex flex-col sm:flex-row items-start sm:items-center justify-between gap-space-sm">
      <div class="flex items-center gap-space-sm">
        <span class="material-symbols-outlined text-primary text-[24px]">verified_user</span>
        <span class="font-body-sm text-body-sm text-on-surface-variant">
          Uang muka (DP) pendaftaran sebesar 10% dari total biaya per jamaah. Pelunasan diselesaikan 30 hari sebelum
          tanggal keberangkatan melalui Virtual Account resmi bank syariah berizin OJK.
        </span>
      </div>
      <a class="shrink-0 font-label-md text-label-md text-primary hover:underline underline-offset-4" href="<?= url('faq') ?>">Rincian Kebijakan Pembatalan</a>
    </div>
  </div>
</section>

<!-- Departure Schedule Timeline -->
<section class="w-full py-space-xl bg-surface">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="max-w-3xl mb-space-lg">
      <span class="font-label-sm text-label-sm text-primary tracking-widest uppercase">Jadwal Keberangkatan Terencana</span>
      <h2 class="font-headline-lg text-headline-lg text-on-surface mt-1">Pilihan Milad &amp; Musim Keberangkatan</h2>
      <p class="font-body-md text-body-md text-on-surface-variant mt-2">
        Kami membatasi setiap kelompok keberangkatan agar rasio pendampingan dokter dan keintiman talaqqi manasik
        tetap terjaga.
      </p>
    </div>

    <?php if ($jadwalList): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-space-md relative">
      <?php foreach ($jadwalList as $i => $j): $ts = strtotime($j['tanggal_berangkat']); ?>
      <div class="<?= $j['status'] === 'dibuka' ? 'bg-surface' : 'bg-surface-container-low' ?> rounded-2xl p-space-md flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow">
        <div>
          <div class="flex items-center justify-between mb-space-sm gap-2 flex-wrap">
            <?php if ($j['status'] === 'dibuka'): ?>
            <span class="px-2.5 py-1 rounded bg-primary/10 text-primary font-label-sm text-label-sm font-medium">Pendaftaran Dibuka</span>
            <?php else: ?>
            <span class="px-2.5 py-1 rounded bg-tertiary-container text-on-tertiary font-label-sm text-label-sm font-medium"><?= e(ucfirst($j['status'])) ?></span>
            <?php endif; ?>
            <span class="font-label-sm text-label-sm <?= $j['status'] === 'dibuka' ? 'text-on-surface-variant' : 'text-tertiary' ?> font-medium">
              Sisa <?= (int) $j['kuota'] ?> Kursi
            </span>
          </div>
          <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Keberangkatan <?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></p>
          <h3 class="font-headline-md text-headline-md text-on-surface mt-1"><?= tanggal($j['tanggal_berangkat'], 'long') ?></h3>
          <div class="mt-space-md pt-space-sm border-t border-outline-variant/30 flex flex-col gap-1.5 font-body-sm text-body-sm text-on-surface-variant">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[16px] text-primary">flight_takeoff</span>
              <span><?= e($maskapaiList ? $maskapaiList[0]['nama'] : 'Penerbangan langsung PP') ?></span>
            </div>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[16px] text-primary">groups</span>
              <span>Kuota rombongan <?= (int) $j['kuota'] ?> jamaah</span>
            </div>
          </div>
        </div>
        <?php if ($j['status'] === 'dibuka'): ?>
        <a href="<?= url('kontak') ?>#konsultasi" class="mt-space-md w-full py-space-xs px-space-sm rounded bg-surface-container-high text-primary hover:bg-primary hover:text-on-primary transition-colors font-label-md text-label-md text-center block">
          Pilih Tanggal Ini
        </a>
        <?php else: ?>
        <span class="mt-space-md w-full py-space-xs px-space-sm rounded bg-surface-container-highest text-tertiary transition-colors font-label-md text-label-md text-center block">
          Belum Dibuka
        </span>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="bg-surface-container-low rounded-2xl p-space-lg text-center">
      <span class="material-symbols-outlined text-outline text-[40px] mb-2">event_busy</span>
      <h3 class="font-headline-sm text-headline-sm text-tertiary">Jadwal Berikutnya Sedang Disusun</h3>
      <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 max-w-md mx-auto">
        Kirim permintaan konsultasi agar kami mengabari Anda begitu tanggal keberangkatan paket ini dibuka.
      </p>
      <a class="inline-flex items-center justify-center px-space-md py-space-sm mt-space-md rounded bg-primary text-on-primary font-label-md text-label-md hover:bg-primary-container transition-colors" href="<?= url('kontak') ?>#konsultasi">Kirim Konsultasi</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- Fasilitas & Layanan Khidmat -->
<section class="w-full py-space-xl bg-surface-container-low/50">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="max-w-3xl mb-space-lg">
      <span class="font-label-sm text-label-sm text-primary tracking-widest uppercase">Dedikasi Layanan</span>
      <h2 class="font-headline-lg text-headline-lg text-on-surface mt-1">Kenyamanan yang Direncanakan dengan Hati</h2>
      <p class="font-body-md text-body-md text-on-surface-variant mt-2">
        Setiap detail dirancang untuk meminimalkan kelelahan fisik, sehingga setiap hembusan napas di Tanah Suci
        tertuju seutuhnya pada kekhusyukan munajat.
      </p>
    </div>
    <?php if ($fasilitasList): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-md">
      <?php foreach ($fasilitasList as $f): ?>
      <div class="p-space-md rounded-2xl bg-surface shadow-sm flex flex-col gap-space-xs">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary mb-1">
          <span class="material-symbols-outlined text-[22px]"><?= e($f['ikon'] ?: 'verified') ?></span>
        </div>
        <h3 class="font-headline-sm text-headline-sm text-on-surface"><?= e($f['nama']) ?></h3>
        <p class="font-body-sm text-body-sm text-on-surface-variant"><?= e($f['deskripsi']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="font-body-md text-body-md text-on-surface-variant">Rincian fasilitas sedang kami lengkapi. Silakan hubungi konsultan untuk daftar lengkap.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Hotel & Airline Curated Dossier -->
<section class="w-full py-space-xl bg-surface">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="max-w-3xl mb-space-lg">
      <span class="font-label-sm text-label-sm text-primary tracking-widest uppercase">Kediaman Sementara &amp; Armada</span>
      <h2 class="font-headline-lg text-headline-lg text-on-surface mt-1">Standar Kemuliaan Bertamu</h2>
    </div>

    <?php if ($hotelList): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-space-lg">
      <?php foreach ($hotelList as $h): ?>
      <div class="bg-surface-container-low rounded-2xl overflow-hidden shadow-sm flex flex-col">
        <div class="h-64 relative overflow-hidden">
          <img alt="<?= e($h['nama']) ?>" class="w-full h-full object-cover" src="<?= upload_url($h['gambar'] ?? null, 'hotel') ?>"/>
          <div class="absolute top-space-sm left-space-sm px-space-sm py-1 rounded-full bg-surface-container-lowest/80 backdrop-blur-md font-label-sm text-label-sm text-tertiary">
            <?= e($h['kota'] ?? 'Tanah Suci') ?> &bull; <?= e($h['kelas'] ?? 'Bintang 5') ?>
          </div>
        </div>
        <div class="p-space-md lg:p-space-lg flex flex-col flex-grow justify-between gap-space-sm">
          <div>
            <div class="flex items-center gap-1 text-secondary mb-1">
              <?php for ($i = 0; $i < 5; $i++): ?>
              <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">star</span>
              <?php endfor; ?>
              <span class="font-label-sm text-label-sm text-on-surface-variant ml-2"><?= e($h['kelas'] ?? 'Bintang 5') ?></span>
            </div>
            <h3 class="font-headline-md text-headline-md text-on-surface"><?= e($h['nama']) ?></h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-2 leading-relaxed">
              Terletak dekat pelataran masjid utama, memungkinkan jamaah melangkah dengan amat teduh tanpa
              terburu-buru, serta kembali ke kamar untuk berwudhu atau istirahat di antara waktu sholat fardhu.
            </p>
          </div>
          <div class="pt-space-sm border-t border-outline-variant/30 grid grid-cols-2 gap-2 text-on-surface-variant font-label-md text-label-md">
            <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-primary">directions_walk</span> Langkah Kaki Singkat</span>
            <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-primary">restaurant</span> Restoran Jamaah</span>
            <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-primary">accessible</span> Ramah Kursi Roda</span>
            <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-primary">wifi</span> Koneksi Cepat</span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="p-space-lg rounded-2xl bg-surface-container-low text-center flex flex-col items-center gap-space-xs">
      <span class="material-symbols-outlined text-[36px] text-on-surface-variant/60">hotel</span>
      <p class="font-body-sm text-body-sm text-on-surface-variant max-w-md">Akomodasi resmi paket ini sedang kami konfirmasikan bersama mitra hotel. Detail kamar akan diumumkan bersama jadwal keberangkatan.</p>
    </div>
    <?php endif; ?>

    <?php if ($maskapaiList): foreach ($maskapaiList as $m): ?>
    <div class="mt-space-md p-space-md lg:p-space-lg rounded-2xl bg-surface-container flex flex-col lg:flex-row items-center justify-between gap-space-md">
      <div class="flex items-center gap-space-md">
        <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center shrink-0 text-primary">
          <span class="material-symbols-outlined text-[28px]">flight_takeoff</span>
        </div>
        <div>
          <span class="font-label-sm text-label-sm text-primary uppercase tracking-wider">Maskapai Resmi Keberangkatan</span>
          <h4 class="font-headline-sm text-headline-sm text-on-surface mt-0.5"><?= e($m['nama']) ?></h4>
          <p class="font-body-sm text-body-sm text-on-surface-variant">Penerbangan pulang-pergi langsung menuju Madinah (MED) dan Jeddah (JED).</p>
        </div>
      </div>
      <div class="flex flex-wrap items-center gap-space-md text-on-surface font-label-md text-label-md border-t lg:border-t-0 pt-space-sm lg:pt-0 w-full lg:w-auto">
        <div class="flex flex-col">
          <span class="text-on-surface-variant font-label-sm text-label-sm">Jatah Bagasi Utama</span>
          <span class="font-semibold text-tertiary">30 Kg Bagasi Check-in</span>
        </div>
        <div class="hidden sm:block w-px h-8 bg-outline-variant/40"></div>
        <div class="flex flex-col">
          <span class="text-on-surface-variant font-label-sm text-label-sm">Bonus Air Suci</span>
          <span class="font-semibold text-tertiary">5 Liter Zamzam Resmi</span>
        </div>
        <div class="hidden sm:block w-px h-8 bg-outline-variant/40"></div>
        <div class="flex flex-col">
          <span class="text-on-surface-variant font-label-sm text-label-sm">Layanan Khusus</span>
          <span class="font-semibold text-tertiary">Wheelchair Transit &amp; Meet-Assist</span>
        </div>
      </div>
    </div>
    <?php endforeach; endif; ?>
    <?php if (!$maskapaiList): ?>
    <div class="mt-space-md p-space-md lg:p-space-lg rounded-2xl bg-surface-container text-center flex flex-col items-center gap-space-xs">
      <span class="material-symbols-outlined text-[36px] text-on-surface-variant/60">flight</span>
      <p class="font-body-sm text-body-sm text-on-surface-variant max-w-md">Maskapai penerbangan akan ditentukan mengikuti jadwal keberangkatan. Informasi rute dan jatah bagasi menyusul bersama konfirmasi tiket.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- Daily Itinerary Overview -->
<section class="w-full py-space-xl bg-surface-container-low">
  <div class="max-w-5xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="text-center max-w-2xl mx-auto mb-space-xl">
      <span class="font-label-sm text-label-sm text-primary tracking-widest uppercase">Perjalanan Batin Bertahap</span>
      <h2 class="font-headline-lg text-headline-lg text-on-surface mt-1">Rangkaian Khidmat <?= $durasi ?> Hari</h2>
      <p class="font-body-md text-body-md text-on-surface-variant mt-2">
        Disusun dengan ritme lapang (unhurried pacing), tanpa agenda beruntun yang membebani fisik jamaah.
      </p>
    </div>

    <?php $itineraryList = model('paket')->getItinerary((int) $paket['id']); ?>
    <?php if ($itineraryList): ?>
    <div class="flex flex-col gap-space-md relative">
      <div class="hidden md:block absolute left-8 top-6 bottom-6 w-0.5 bg-outline-variant/50"></div>
      <?php foreach ($itineraryList as $i => $s): ?>
      <div class="flex flex-col md:flex-row gap-space-md items-start relative">
        <div class="w-16 h-16 rounded-2xl bg-surface flex items-center justify-center shrink-0 shadow-sm border border-outline-variant/30 z-10">
          <span class="font-headline-sm text-headline-sm text-primary"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
        </div>
        <div class="flex-grow bg-surface rounded-2xl p-space-md shadow-sm">
          <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
            <h3 class="font-headline-sm text-headline-sm text-on-surface"><?= e($s['hari']) ?><?= trim((string) ($s['judul'] ?? '')) !== '' ? ': ' . e($s['judul']) : '' ?></h3>
            <?php if (trim((string) ($s['tag'] ?? '')) !== ''): ?>
            <span class="px-2 py-0.5 rounded <?= !empty($s['is_highlight']) ? 'bg-secondary-fixed/50 text-on-secondary-fixed font-semibold' : 'bg-surface-container text-on-surface-variant' ?> font-label-sm text-label-sm"><?= e($s['tag']) ?></span>
            <?php endif; ?>
          </div>
          <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed"><?= e($s['deskripsi']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="flex flex-col items-center justify-center gap-space-xs text-center rounded-2xl bg-surface p-space-lg">
      <span class="material-symbols-outlined text-[36px] text-on-surface-variant/60">map</span>
      <span class="font-headline-sm text-headline-sm text-on-surface">Rangkaian harian sedang disusun</span>
      <span class="font-body-sm text-body-sm text-on-surface-variant max-w-md">Detail itinerary akan dipublikasikan setelah tim menyempurnakan ritme perjalanan. Silakan konsultasi untuk memperoleh gambaran lengkap.</span>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- FAQ Micro-Drawer -->
<section class="w-full py-space-xl bg-surface">
  <div class="max-w-4xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="text-center mb-space-lg">
      <span class="font-label-sm text-label-sm text-primary tracking-widest uppercase">Kesiapan Ibadah</span>
      <h2 class="font-headline-lg text-headline-lg text-on-surface mt-1">Pertanyaan yang Sering Diajukan Keluarga</h2>
    </div>
    <div class="flex flex-col gap-space-sm" id="faq-accordion">
      <?php foreach ($faqList as $f): ?>
      <div class="bg-surface-container-low rounded-2xl p-space-md shadow-sm cursor-pointer faq-item">
        <div class="flex items-center justify-between gap-space-sm">
          <h4 class="font-headline-sm text-headline-sm text-on-surface"><?= e($f['pertanyaan']) ?></h4>
          <span class="material-symbols-outlined text-primary transition-transform duration-300">expand_more</span>
        </div>
        <div class="faq-content hidden mt-space-sm pt-space-xs border-t border-outline-variant/30 text-on-surface-variant font-body-sm text-body-sm leading-relaxed">
          <?= nl2br(e($f['jawaban'])) ?>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (!$faqList): ?>
      <p class="font-body-md text-body-md text-on-surface-variant text-center">Pertanyaan umum sedang kami lengkapi. Silakan hubungi konsultan kami.</p>
      <?php endif; ?>
    </div>
    <div class="text-center mt-space-md">
      <a class="font-label-md text-label-md text-primary hover:underline underline-offset-4" href="<?= url('faq') ?>">Lihat seluruh pertanyaan umum</a>
    </div>
  </div>
</section>

<!-- Prominent Consultation CTA -->
<section class="w-full py-space-xl lg:py-24 bg-primary text-on-primary relative overflow-hidden" id="konsultasi">
  <div class="absolute inset-0 opacity-5 pointer-events-none flex items-center justify-center">
    <svg fill="currentColor" height="800" viewbox="0 0 100 100" width="800">
      <polygon points="50,0 61,35 98,35 68,57 79,91 50,70 21,91 32,57 2,35 39,35"></polygon>
    </svg>
  </div>
  <div class="max-w-4xl mx-auto px-margin lg:px-gutter-desktop relative z-10 text-center flex flex-col items-center">
    <div class="inline-flex items-center gap-space-xs px-space-sm py-1 rounded-full bg-surface/10 text-on-primary-container font-label-sm text-label-sm mb-space-sm backdrop-blur-sm">
      <span class="material-symbols-outlined text-[16px]">support_agent</span>
      <span>Layanan Concierge Ramah Keluarga</span>
    </div>
    <h2 class="font-display-md text-display-md font-normal tracking-tight text-surface max-w-2xl leading-snug">
      Konsultasikan Kesiapan Ibadah Keluarga Anda
    </h2>
    <p class="font-body-lg text-body-lg text-inverse-on-surface/90 mt-space-sm max-w-2xl">
      Setiap keluarga memiliki kebutuhan berbeda. Diskusikan kamar terhubung, pendampingan lansia, atau opsi pembayaran
      bertahap bersama tim concierge kami.
    </p>
    <div class="mt-space-lg flex flex-col sm:flex-row items-center gap-space-md w-full sm:w-auto">
      <a class="w-full sm:w-auto px-space-lg py-space-sm rounded bg-secondary-fixed text-on-secondary-fixed font-label-md text-label-md font-semibold hover:bg-secondary-fixed-dim transition-colors shadow-md text-center" href="<?= url('kontak') ?>#konsultasi">
        Jadwalkan Konsultasi Privat
      </a>
      <a class="w-full sm:w-auto px-space-lg py-space-sm rounded bg-surface-container-lowest/15 hover:bg-surface-container-lowest/25 text-surface font-label-md text-label-md transition-colors border border-surface/20 flex items-center justify-center gap-2" href="<?= url('kontak') ?>">
        <span class="material-symbols-outlined text-[18px]">chat</span>
        <span>Hubungi Tim Sakinah</span>
      </a>
    </div>
    <div class="mt-space-xl pt-space-md border-t border-surface/15 grid grid-cols-1 sm:grid-cols-3 gap-space-md w-full text-center">
      <div class="flex flex-col items-center">
        <span class="material-symbols-outlined text-secondary-fixed text-[24px] mb-1">verified</span>
        <span class="font-label-md text-label-md font-semibold text-surface">Izin Resmi PPIU Kemenag RI</span>
        <span class="font-label-sm text-label-sm text-surface/75"><?= trim((string) ($profil['izin_ppiu'] ?? '')) !== '' ? e($profil['izin_ppiu']) : 'PPIU berizin resmi Kemenag' ?></span>
      </div>
      <div class="flex flex-col items-center">
        <span class="material-symbols-outlined text-secondary-fixed text-[24px] mb-1">health_and_safety</span>
        <span class="font-label-md text-label-md font-semibold text-surface">Pendampingan Medis Siaga</span>
        <span class="font-label-sm text-label-sm text-surface/75">Dokter Kontingen Standby 24/7</span>
      </div>
      <div class="flex flex-col items-center">
        <span class="material-symbols-outlined text-secondary-fixed text-[24px] mb-1">lock</span>
        <span class="font-label-md text-label-md font-semibold text-surface">Tanpa Biaya Tersembunyi</span>
        <span class="font-label-sm text-label-sm text-surface/75">Harga All-in Sesuai Akad Syariah</span>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('click', function (event) {
  var item = event.target.closest ? event.target.closest('.faq-item') : null;
  if (!item) { return; }
  var content = item.querySelector('.faq-content');
  var icon = item.querySelector('.material-symbols-outlined');
  if (!content) { return; }
  content.classList.toggle('hidden');
  if (icon) { icon.classList.toggle('rotate-180'); }
});
</script>
<?php require PUBLIC_PATH . '/partials/footer.php'; ?>
