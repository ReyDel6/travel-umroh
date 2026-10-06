<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/** Halaman Beranda — design: beranda_perjalanan_umroh_tenang_terpercaya */
$metaTitle = ($profil['meta_title'] ?? '') ?: (APP_NAME . ' — Beranda');
$metaDescription = ($profil['meta_description'] ?? '') ?: 'Travel umroh resmi dengan pendampingan manasik, dokter, dan muthawwif.';

$paketList = model('paket')->getFeatured(3);
$paketHero = $paketList[0] ?? null;
$paketSekunder = array_slice($paketList, 1, 2);

meta_halaman_apply('home', $metaTitle, $metaDescription);

require PUBLIC_PATH . '/partials/head.php';
require PUBLIC_PATH . '/partials/header.php';
?>
<!-- Hero Section (Left-Aligned, Asymmetric 2-Column) -->
<section class="w-full bg-[#F2EEE6] text-[#2B2520] pt-space-lg pb-space-xl">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg lg:gap-gutter-desktop items-center">
      <!-- Left Column: Editorial Narrative -->
      <div class="lg:col-span-7 flex flex-col justify-center">
        <div class="inline-flex items-center gap-space-xs mb-space-sm">
          <span class="w-2 h-2 rounded-full bg-[#1F5C56]"></span>
          <span class="font-label-md text-label-md text-[#5C524A]">Pendampingan Ibadah Penuh Khidmat &amp; Kehangatan Keluarga</span>
        </div>
        <h1 class="font-display-lg text-display-md lg:text-display-lg text-[#2B2520] font-normal tracking-tight leading-[1.15] mb-space-md">
          Menunaikan panggilan suci<br/>dalam ketenangan yang terjaga.
        </h1>
        <p class="font-body-lg text-body-lg text-[#5C524A] max-w-xl mb-space-lg leading-relaxed">
          Kami merancang setiap etape perjalanan di Tanah Suci dengan ritme yang teduh, bimbingan manasik berjarak dekat, serta akomodasi terbaik agar Anda dan keluarga dapat beribadah tanpa tergesa.
        </p>

        <div class="flex flex-wrap items-center gap-space-md mb-space-xl">
          <a class="inline-flex items-center justify-center px-space-lg py-space-sm bg-[#B8923F] text-white font-label-md text-label-md rounded shadow-sm hover:opacity-95 transition-opacity" href="<?= url('kontak') ?>#konsultasi">Konsultasi Rencana Ibadah</a>
          <a class="inline-flex items-center justify-center px-space-lg py-space-sm bg-transparent border border-[#1F5C56] text-[#1F5C56] font-label-md text-label-md rounded hover:bg-[#1F5C56]/5 transition-colors" href="<?= url('tentang') ?>">Pelajari Nilai Bimbingan</a>
        </div>

        <div class="pt-space-md border-t border-[#DCD5C9] grid grid-cols-1 sm:grid-cols-3 gap-space-md text-[#5C524A]">
          <div class="flex items-start gap-space-xs">
            <span class="material-symbols-outlined text-[#1F5C56] text-[18px] shrink-0 mt-0.5">verified_user</span>
            <div class="flex flex-col">
              <span class="font-label-sm text-label-sm font-semibold text-[#2B2520]">Izin Resmi Kemenag</span>
              <span class="font-body-sm text-body-sm text-[#5C524A]"><?= trim((string) ($profil['izin_ppiu'] ?? '')) !== '' ? e($profil['izin_ppiu']) : 'PPIU berizin resmi' ?></span>
            </div>
          </div>
          <div class="flex items-start gap-space-xs">
            <span class="material-symbols-outlined text-[#1F5C56] text-[18px] shrink-0 mt-0.5">medical_services</span>
            <div class="flex flex-col">
              <span class="font-label-sm text-label-sm font-semibold text-[#2B2520]">Dokter &amp; Muthawwif</span>
              <span class="font-body-sm text-body-sm text-[#5C524A]">Pendamping Khusus</span>
            </div>
          </div>
          <div class="flex items-start gap-space-xs">
            <span class="material-symbols-outlined text-[#1F5C56] text-[18px] shrink-0 mt-0.5">groups_2</span>
            <div class="flex flex-col">
              <span class="font-label-sm text-label-sm font-semibold text-[#2B2520]">Grup Eksklusif</span>
              <span class="font-body-sm text-body-sm text-[#5C524A]"><?= !empty($profil['stat_grup_maks']) ? 'Maksimal ' . (int) $profil['stat_grup_maks'] . ' Jamaah' : 'Grup kecil eksklusif' ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Visual Frame -->
      <div class="lg:col-span-5 relative mt-space-md lg:mt-0">
        <div class="relative p-2 bg-[#FAF7F2] rounded-xl shadow-md border border-[#E4D8D0]">
          <div class="relative overflow-hidden rounded-lg aspect-[4/5] w-full">
            <img class="w-full h-full object-cover" alt="Pendampingan jamaah keluarga di pelataran Masjid Nabawi" src="<?= upload_url('hero-utama.jpg', 'profil') ?>"/>
            <div class="absolute inset-0 bg-gradient-to-t from-[#2B2520]/80 via-transparent to-transparent"></div>
            <div class="absolute bottom-4 left-4 right-4 text-white">
              <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-black/40 backdrop-blur-sm border border-white/10">
                <span class="w-1.5 h-1.5 rounded-full bg-[#B8923F]"></span>
                <p class="font-label-sm text-label-sm italic text-[#FAF7F2]">Kehangatan pendampingan lansia di pelataran Madinah</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Islamic Tilework Geometric Divider -->
<div aria-hidden="true" class="w-full bg-[#F2EEE6] flex items-center justify-center py-4 overflow-hidden select-none">
  <div class="w-full max-w-7xl mx-auto px-margin lg:px-gutter-desktop flex items-center justify-center gap-4">
    <span class="h-[1px] flex-1 bg-gradient-to-r from-transparent via-[#DCD5C9] to-[#1F5C56]/40"></span>
    <svg class="w-auto h-6 text-[#1F5C56]" fill="none" viewbox="0 0 160 24" xmlns="http://www.w3.org/2000/svg">
      <path d="M80 2L83.5 8.5L90.5 8.5L85 13L87.5 19.5L80 15.5L72.5 19.5L75 13L69.5 8.5L76.5 8.5L80 2Z" fill="#FAF7F2" stroke="#1F5C56" stroke-width="1"></path>
      <circle cx="80" cy="12" fill="#B8923F" r="2"></circle>
      <path d="M48 12H68" stroke="#1F5C56" stroke-dasharray="2 3" stroke-opacity="0.4" stroke-width="1"></path>
      <path d="M92 12H112" stroke="#1F5C56" stroke-dasharray="2 3" stroke-opacity="0.4" stroke-width="1"></path>
      <rect fill="#B8923F" fill-opacity="0.7" height="4" transform="rotate(45 44 12)" width="4" x="42" y="10"></rect>
      <rect fill="#B8923F" fill-opacity="0.7" height="4" transform="rotate(45 116 12)" width="4" x="114" y="10"></rect>
    </svg>
    <span class="h-[1px] flex-1 bg-gradient-to-l from-transparent via-[#DCD5C9] to-[#1F5C56]/40"></span>
  </div>
</div>

<!-- Kurasi Perjalanan Ibadah (Asymmetric Grid Section) -->
<section class="w-full bg-[#FAF7F2] py-space-xl text-[#2B2520]">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="max-w-2xl mb-space-xl">
      <span class="font-label-md text-label-md text-[#1F5C56] font-medium block mb-space-xs">Rencana Perjalanan Istimewa</span>
      <h2 class="font-headline-lg text-headline-lg text-[#2B2520] mb-space-xs">Kurasi Perjalanan Ibadah</h2>
      <p class="font-body-md text-body-md text-[#5C524A]">Pilihan keberangkatan dengan alokasi waktu yang lapang dan fasilitas yang menjamin kenyamanan fisik serta batin.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-stretch">
      <?php if ($paketHero): ?>
      <?php
        $heroHarga = $paketHero['harga_min'] !== null ? rupiah($paketHero['harga_min']) : 'Hubungi kami';
        $heroHotel = model('paket')->getHotels((int) $paketHero['id']);
        $heroMaskapai = model('paket')->getMaskapai((int) $paketHero['id']);
        $heroFasilitas = model('paket')->getFasilitas((int) $paketHero['id']);
        $heroJadwal = model('jadwal')->getByPaket((int) $paketHero['id'], true);
      ?>
      <!-- Hero Package -->
      <div class="lg:col-span-7 bg-[#FFFFFF] rounded-xl p-space-lg border border-[#E4D8D0] shadow-sm flex flex-col justify-between relative overflow-hidden">
        <div>
          <div class="flex flex-wrap items-center justify-between gap-space-xs mb-space-md">
            <span class="px-space-sm py-1 bg-[#1F5C56]/10 text-[#1F5C56] font-label-sm text-label-sm rounded-full font-medium">
              <?= e($heroFasilitas[0]['nama'] ?? 'Paket Unggulan') ?>
            </span>
            <span class="font-label-sm text-label-sm text-[#5C524A]">
              <?= $heroJadwal ? 'Keberangkatan: ' . tanggal($heroJadwal[0]['tanggal_berangkat']) : 'Jadwal menyusul' ?>
            </span>
          </div>
          <h3 class="font-headline-md text-headline-md text-[#2B2520] mb-space-xs"><?= e($paketHero['nama']) ?></h3>
          <p class="font-body-sm text-body-sm text-[#5C524A] mb-space-lg leading-relaxed"><?= e(excerpt($paketHero['deskripsi'], 240)) ?></p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md mb-space-lg bg-[#FAF7F2] p-space-md rounded-lg border border-[#E4D8D0]/60">
            <div class="flex items-start gap-space-xs">
              <span class="material-symbols-outlined text-[#1F5C56] text-[20px] shrink-0 mt-0.5">hotel</span>
              <div>
                <span class="font-label-sm text-label-sm font-semibold text-[#2B2520] block">Hotel Pelataran Depan</span>
                <span class="font-body-sm text-body-sm text-[#5C524A]">
                  <?= e($heroHotel ? implode(' &amp; ', array_slice(array_column($heroHotel, 'nama'), 0, 2)) : 'Akomodasi terbaik di kedua kota') ?>
                </span>
              </div>
            </div>
            <div class="flex items-start gap-space-xs">
              <span class="material-symbols-outlined text-[#1F5C56] text-[20px] shrink-0 mt-0.5">flight</span>
              <div>
                <span class="font-label-sm text-label-sm font-semibold text-[#2B2520] block">Maskapai</span>
                <span class="font-body-sm text-body-sm text-[#5C524A]">
                  <?= e($heroMaskapai ? implode(' &amp; ', array_column($heroMaskapai, 'nama')) : 'Sesuai jadwal keberangkatan') ?>
                </span>
              </div>
            </div>
            <div class="flex items-start gap-space-xs">
              <span class="material-symbols-outlined text-[#1F5C56] text-[20px] shrink-0 mt-0.5">assist_walker</span>
              <div>
                <span class="font-label-sm text-label-sm font-semibold text-[#2B2520] block">Durasi Perjalanan</span>
                <span class="font-body-sm text-body-sm text-[#5C524A]"><?= (int) $paketHero['durasi_hari'] ?> hari penuh di Tanah Suci</span>
              </div>
            </div>
            <div class="flex items-start gap-space-xs">
              <span class="material-symbols-outlined text-[#1F5C56] text-[20px] shrink-0 mt-0.5">menu_book</span>
              <div>
                <span class="font-label-sm text-label-sm font-semibold text-[#2B2520] block">Fasilitas Lengkap</span>
                <span class="font-body-sm text-body-sm text-[#5C524A]"><?= count($heroFasilitas) ?> item fasilitas tercantum</span>
              </div>
            </div>
          </div>
        </div>

        <div class="pt-space-md border-t border-[#E4D8D0] flex flex-col sm:flex-row sm:items-center justify-between gap-space-md mt-space-md">
          <div>
            <span class="font-label-sm text-label-sm text-[#5C524A] block">Investasi Ibadah</span>
            <div class="inline-flex items-baseline gap-1.5 px-3 py-1 bg-[#FAF7F2] rounded border border-[#B8923F]">
              <span class="font-display-md text-headline-sm text-[#B8923F] font-bold">Mulai dari <?= $heroHarga ?></span>
              <span class="font-label-sm text-label-sm text-[#5C524A]">/ jamaah</span>
            </div>
          </div>
          <a class="inline-flex items-center justify-center px-space-md py-space-sm bg-[#1F5C56] text-white font-label-md text-label-md rounded hover:bg-[#1F5C56]/90 transition-colors" href="<?= url('paket/' . $paketHero['slug']) ?>">
            Rincian &amp; Jadwal Keberangkatan
          </a>
        </div>
      </div>
      <?php else: ?>
      <div class="lg:col-span-7 bg-[#FFFFFF] rounded-xl p-space-lg border border-[#E4D8D0] shadow-sm flex flex-col items-center justify-center text-center gap-space-sm min-h-[280px]">
        <span class="material-symbols-outlined text-[#1F5C56] text-[40px]">luggage</span>
        <h3 class="font-headline-md text-headline-md text-[#2B2520]">Paket Unggulan Sedang Disiapkan</h3>
        <p class="font-body-sm text-body-sm text-[#5C524A] max-w-md">Tim kami sedang menyusun pilihan keberangkatan terbaik. Silakan konsultasikan rencana perjalanan Anda lebih dulu.</p>
        <a class="inline-flex items-center justify-center px-space-md py-space-sm mt-space-xs bg-[#1F5C56] text-white font-label-md text-label-md rounded hover:bg-[#1F5C56]/90 transition-colors" href="<?= url('kontak') ?>">
          Konsultasi Perjalanan
        </a>
      </div>
      <?php endif; ?>

      <!-- Secondary Packages -->
      <div class="lg:col-span-5 flex flex-col gap-space-lg">
        <?php foreach ($paketSekunder as $p): ?>
        <div class="bg-[#FFFFFF] rounded-xl p-space-md lg:p-space-lg border border-[#E4D8D0] shadow-sm flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between gap-space-xs mb-space-xs">
              <span class="px-2.5 py-0.5 bg-[#FAF7F2] text-[#1F5C56] border border-[#1F5C56]/20 font-label-sm text-label-sm rounded-full">
                <?= (int) $p['durasi_hari'] ?> Hari
              </span>
              <span class="font-label-sm text-label-sm text-[#5C524A]">
                <?= $p['jadwal_terdekat'] ? 'Berangkat ' . tanggal($p['jadwal_terdekat']) : 'Jadwal menyusul' ?>
              </span>
            </div>
            <h4 class="font-headline-sm text-headline-sm text-[#2B2520] mb-1"><?= e($p['nama']) ?></h4>
            <p class="font-body-sm text-body-sm text-[#5C524A] mb-space-md"><?= e(excerpt($p['deskripsi'], 150)) ?></p>
          </div>
          <div class="pt-space-sm border-t border-[#E4D8D0]/60 flex items-center justify-between gap-space-sm">
            <div>
              <span class="font-label-sm text-label-sm text-[#5C524A] block">Tarif Paket</span>
              <span class="font-headline-sm text-headline-sm text-[#B8923F] font-medium">
                <?= $p['harga_min'] !== null ? 'Mulai dari ' . rupiah($p['harga_min']) : 'Hubungi kami' ?>
              </span>
            </div>
            <a class="inline-flex items-center justify-center px-space-sm py-1.5 border border-[#1F5C56] text-[#1F5C56] font-label-sm text-label-sm rounded hover:bg-[#1F5C56]/5 transition-colors" href="<?= url('paket/' . $p['slug']) ?>">
              Lihat Jadwal
            </a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="mt-space-lg flex justify-center">
      <a class="inline-flex items-center justify-center px-space-md py-space-sm border border-[#1F5C56] text-[#1F5C56] font-label-md text-label-md rounded hover:bg-[#1F5C56] hover:text-white transition-colors" href="<?= url('paket') ?>">
        Lihat Semua Paket Ibadah
      </a>
    </div>
  </div>
</section>

<!-- Editorial Atmospheric Interlude Photo -->
<section class="w-full bg-[#FAF7F2] pb-space-xl">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-center">
      <div class="lg:col-span-4 flex flex-col justify-center">
        <span class="font-label-sm text-label-sm text-[#1F5C56] mb-1">Pijakan Kebijaksanaan</span>
        <h3 class="font-headline-md text-headline-md text-[#2B2520] mb-space-xs">Ibadah yang berjarak dekat dengan keteduhan hati.</h3>
        <p class="font-body-sm text-body-sm text-[#5C524A] leading-relaxed mb-space-sm">Setiap pemilihan hotel didasarkan pada aksesibilitas jalan datar menuju shaf utama, meminimalkan eskalator padat dan waktu antre yang melelahkan.</p>
        <div class="flex items-center gap-space-xs text-[#1F5C56] font-label-sm text-label-sm">
          <span class="material-symbols-outlined text-[18px]">near_me</span>
          <span>Jarak tempuh rata-rata di bawah 150 meter ke pintu masjid</span>
        </div>
      </div>
      <div class="lg:col-span-8">
        <div class="rounded-xl overflow-hidden border border-[#E4D8D0] shadow-sm aspect-[16/8] relative">
          <img class="w-full h-full object-cover" alt="Pemandangan pelataran suci dari griya penginapan Madinah" src="<?= upload_url('interlude-hotel.jpg', 'galeri') ?>"/>
          <div class="absolute bottom-3 left-4 bg-[#FAF7F2]/90 backdrop-blur-sm px-3 py-1.5 rounded text-[#2B2520] text-label-sm font-label-sm border border-[#E4D8D0]">
            Pemandangan pelataran suci dari griya penginapan Madinah
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Jadwal Keberangkatan Terdekat -->
<section class="w-full bg-[#F2EEE6] py-space-xl text-[#2B2520]">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="max-w-2xl mb-space-lg">
      <span class="font-label-md text-label-md text-[#1F5C56] font-medium block mb-space-xs">Ketersediaan Kursi</span>
      <h2 class="font-headline-lg text-headline-lg text-[#2B2520] mb-space-xs">Jadwal Keberangkatan Terdekat</h2>
      <p class="font-body-md text-body-md text-[#5C524A]">Semua jadwal di bawah ini masih dibuka dan terhubung langsung ke halaman paketnya masing-masing.</p>
    </div>

    <?php $jadwalList = model('jadwal')->getMendatang(6); ?>
    <?php if ($jadwalList): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-space-md">
      <?php foreach ($jadwalList as $j): ?>
      <a href="<?= url('paket/' . $j['slug_paket']) ?>" class="bg-white rounded-lg border border-[#E4D8D0] p-space-md hover:border-[#1F5C56] transition-colors flex flex-col gap-2">
        <div class="flex items-center justify-between">
          <span class="font-label-sm text-label-sm font-semibold text-[#2B2520]"><?= tanggal($j['tanggal_berangkat'], 'long') ?></span>
          <?= badge(ucfirst($j['status']), $j['status'] === 'dibuka' ? 'teal' : 'gray') ?>
        </div>
        <span class="font-headline-sm text-headline-sm text-[#1F5C56]"><?= e($j['nama_paket']) ?></span>
        <span class="font-body-sm text-body-sm text-[#5C524A]">Kuota tersisa <?= (int) $j['kuota'] ?> jamaah</span>
      </a>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="font-body-md text-body-md text-[#5C524A] bg-white border border-[#E4D8D0] rounded-lg p-space-md">Jadwal keberangkatan berikutnya sedang kami susun. Silakan kirim konsultasi agar kami kabari lebih dulu.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Testimoni -->
<section class="w-full bg-[#FAF7F2] py-space-xl text-[#2B2520]">
  <?php $testimoniList = model('testimoni')->getAktif(2); ?>
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="text-center max-w-xl mx-auto mb-space-xl">
      <span class="font-label-sm text-label-sm text-[#1F5C56] block mb-space-xs">Kesaksian Jamaah</span>
      <h2 class="font-headline-lg text-headline-lg text-[#2B2520]">Ketenangan dalam Kenangan Mereka</h2>
    </div>
    <?php if ($testimoniList): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-space-lg lg:gap-gutter-desktop">
      <?php foreach ($testimoniList as $t): ?>
      <?php
        $kata = array_values(array_filter(
            preg_split('/[\s&]+/', (string) $t['nama']) ?: [],
            static fn($w) => !in_array(mb_strtolower(trim($w)), ['dan', 'ibu', 'bapak', 'dr.', 'dr', 'h.', 'h', 'hj.', 'hajjah', 'ust.', 'ustadz'], true) && $w !== ''
        ));
        $inT = isset($kata[0]) ? mb_strtoupper(mb_substr($kata[0], 0, 1)) : 'J';
        $inAkhir = count($kata) > 1 ? mb_strtoupper(mb_substr(end($kata), 0, 1)) : '';
        $inisial = $inT . ($inAkhir !== '' && $inAkhir !== $inT ? $inAkhir : '');
      ?>
      <div class="bg-[#FAF7F2] p-space-lg rounded-xl border border-[#E4D8D0] flex flex-col justify-between shadow-sm relative">
        <span class="font-display-lg text-display-md text-[#1F5C56]/20 leading-none absolute top-4 right-6 select-none font-serif">&ldquo;</span>
        <p class="font-body-lg text-body-lg text-[#2B2520] italic mb-space-lg leading-relaxed relative z-10">&ldquo;<?= e($t['teks']) ?>&rdquo;</p>
        <div class="flex items-center gap-space-sm pt-space-sm border-t border-[#E4D8D0]">
          <div class="w-10 h-10 rounded-full bg-[#1F5C56]/10 flex items-center justify-center text-[#1F5C56] font-semibold text-label-md"><?= e($inisial) ?></div>
          <div class="flex flex-col">
            <span class="font-label-md text-label-md font-semibold text-[#2B2520]"><?= e($t['nama']) ?></span>
            <span class="font-body-sm text-body-sm text-[#5C524A]"><?= trim((string) ($t['asal_kota'] ?? '')) !== '' ? 'Jamaah dari ' . e($t['asal_kota']) : 'Jamaah Sakinah Journeys' ?></span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="bg-[#FAF7F2] p-space-lg rounded-xl border border-dashed border-[#DCD5C9] flex flex-col items-center justify-center text-center gap-space-xs min-h-[180px]">
      <span class="material-symbols-outlined text-[#1F5C56] text-[36px]">forum</span>
      <span class="font-headline-sm text-headline-sm text-[#2B2520]">Kesaksian Segera Hadir</span>
      <span class="font-body-sm text-body-sm text-[#5C524A] max-w-md">Cerita pengalaman jamaah akan kami tampilkan setelah data testimoni tersedia.</span>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- CTA Konsultasi -->
<section class="w-full bg-[#F2EEE6] pb-space-xl pt-space-sm" id="konsultasi">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="bg-[#1F5C56] text-white rounded-2xl p-space-lg lg:p-space-xl shadow-xl relative overflow-hidden border border-[#2d6861]">
      <div class="absolute -right-20 -bottom-20 w-96 h-96 rounded-full bg-white/5 pointer-events-none blur-3xl"></div>
      <div class="absolute -left-20 -top-20 w-80 h-80 rounded-full bg-[#B8923F]/10 pointer-events-none blur-2xl"></div>
      <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-space-lg lg:gap-gutter-desktop items-center">
        <div class="lg:col-span-6 flex flex-col">
          <span class="font-label-md text-label-md text-[#ebc168] mb-space-xs font-medium">Konsultasi Hangat &amp; Privat</span>
          <h3 class="font-headline-lg text-headline-lg text-white font-normal mb-space-sm leading-snug">Setiap niat suci berhak dirawat dengan seksama</h3>
          <p class="font-body-md text-body-md text-[#d2c4ba] leading-relaxed mb-space-md max-w-lg">Diskusikan kesiapan kesehatan, preferensi kamar keluarga, atau jadwal keberangkatan bersama pembimbing kami dalam sesi privat tanpa komitmen.</p>
          <div class="flex flex-col gap-space-xs text-[#FAF7F2] font-body-sm text-body-sm">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[#ebc168] text-[18px]">lock</span>
              <span>Informasi keluarga Anda terjaga dengan amanah.</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[#ebc168] text-[18px]">support_agent</span>
              <span>Tatap muka di Griya Sakinah atau melalui sambungan video daring.</span>
            </div>
          </div>
        </div>

        <div class="lg:col-span-6 bg-[#FAF7F2] text-[#2B2520] p-space-md lg:p-space-lg rounded-xl shadow-md border border-[#DCD5C9]">
          <form class="flex flex-col gap-space-sm" method="post" action="<?= url('kontak') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" value="kirim_inquiry"/>
            <div>
              <label class="block font-label-md text-label-md text-[#2B2520] font-medium mb-1" for="nama_lengkap">Nama Lengkap</label>
              <input class="w-full px-space-md py-space-sm bg-white border border-[#DCD5C9] rounded text-body-sm text-[#2B2520] placeholder-[#707977] focus:outline-none focus:border-[#1F5C56] transition-colors" id="nama_lengkap" name="nama" placeholder="Contoh: Hendra Pratama" required="" type="text"/>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-[#2B2520] font-medium mb-1" for="nomor_wa">Nomor WhatsApp / Surel</label>
              <input class="w-full px-space-md py-space-sm bg-white border border-[#DCD5C9] rounded text-body-sm text-[#2B2520] placeholder-[#707977] focus:outline-none focus:border-[#1F5C56] transition-colors" id="nomor_wa" name="kontak" placeholder="Contoh: 0812 3456 7890" required="" type="text"/>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-[#2B2520] font-medium mb-1" for="pilihan_paket">Paket yang Diminati</label>
              <select class="w-full px-space-md py-space-sm bg-white border border-[#DCD5C9] rounded text-body-sm text-[#2B2520] focus:outline-none focus:border-[#1F5C56] transition-colors" id="pilihan_paket" name="paket_id">
                <option value="">Belum menentukan — minta rekomendasi</option>
                <?php foreach (model('paket')->getAllAktif() as $pl): ?>
                <option value="<?= (int) $pl['id'] ?>"><?= e($pl['nama']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-[#2B2520] font-medium mb-1" for="pesan_konsultasi">Catatan Kebutuhan</label>
              <textarea class="w-full px-space-md py-space-sm bg-white border border-[#DCD5C9] rounded text-body-sm text-[#2B2520] placeholder-[#707977] focus:outline-none focus:border-[#1F5C56] transition-colors" id="pesan_konsultasi" name="pesan" rows="3" placeholder="Contoh: rencana berangkat bersama ibu usia 73 tahun, ingin kamar triple."></textarea>
            </div>
            <button class="w-full mt-space-xs py-space-sm px-space-lg bg-[#B8923F] text-white font-label-md text-label-md rounded shadow-sm hover:opacity-95 transition-opacity" type="submit">
              Jadwalkan Konsultasi Keluarga
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<?php require PUBLIC_PATH . '/partials/footer.php'; ?>
