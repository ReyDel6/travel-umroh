<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/** Halaman Tentang Kami — konten dari profil_perusahaan (CMS) */
$metaTitle = 'Tentang Kami — ' . APP_NAME;
$metaDescription = 'Mengenal Sakinah Journeys: izin PPIU Kemenag RI, nilai bimbingan, dan tim pendamping jamaah.';

$tentangParas = preg_split('/\r?\n\r?\n/', trim((string) ($profil['tentang_kami'] ?? '')));
$tentangParas = array_values(array_filter(array_map('trim', $tentangParas)));

$nilai = [
    ['icon' => 'route', 'title' => 'Tempo yang Lapang', 'text' => 'Rangkaian harian disusun tanpa agenda beruntun, agar ibadah dan istirahat berimbang bagi jamaah sepuh maupun keluarga muda.'],
    ['icon' => 'favorite', 'title' => 'Pendampingan yang Hadir', 'text' => 'Dokter kontingen, muthawwif bersertifikat, dan pembimbing manasik menemani sejak persiapan di Jakarta hingga kembali ke tanah air.'],
    ['icon' => 'verified_user', 'title' => 'Akad yang Jelas', 'text' => 'Biaya all-in disampaikan terbuka sejak awal, tanpa pungutan tersembunyi, sesuai izin PPIU Kemenag RI Akreditasi A.'],
];

meta_halaman_apply('tentang', $metaTitle, $metaDescription);

require PUBLIC_PATH . '/partials/head.php';
require PUBLIC_PATH . '/partials/header.php';
?>
<!-- Header Editorial -->
<section class="w-full bg-surface py-space-lg lg:py-space-xl">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-on-surface-variant font-label-md text-label-md mb-space-sm">
      <a class="hover:text-primary transition-colors" href="<?= url('') ?>">Beranda</a>
      <span class="text-outline-variant text-[11px]">/</span>
      <span class="text-primary font-medium">Tentang Kami</span>
    </nav>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-end">
      <div class="lg:col-span-7">
        <p class="font-label-md text-label-md text-secondary tracking-wide mb-space-xs font-semibold">Sakinah Journeys</p>
        <h1 class="font-headline-lg lg:font-display-md text-headline-lg lg:text-display-md text-primary leading-tight font-normal">
          Menemani keluarga Indonesia menuju Tanah Suci dengan tenang
        </h1>
      </div>
      <div class="lg:col-span-5 lg:pb-1">
        <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
          <?= e($profil['tagline'] ?? 'Perjalanan Spiritual yang Teduh') ?> &mdash; <?= e($profil['nama_perusahaan'] ?? '') ?>
        </p>
      </div>
    </div>
    <div class="w-full h-px bg-outline-variant/30 mt-space-lg"></div>
  </div>
</section>

<!-- Foto & Narasi -->
<section class="w-full bg-surface pb-space-xl">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
      <div class="lg:col-span-5">
        <div class="rounded-xl overflow-hidden border border-[#E4D8D0] shadow-sm aspect-[4/5] relative">
          <img class="w-full h-full object-cover" alt="Suasana pendampingan jamaah" src="<?= upload_url('hero-utama.jpg', 'profil') ?>"/>
          <div class="absolute inset-0 bg-gradient-to-t from-[#2B2520]/70 via-transparent to-transparent"></div>
          <div class="absolute bottom-4 left-4 right-4 text-white font-body-sm text-body-sm">
            Griya Sakinah &mdash; ruang konsultasi jamaah sebelum keberangkatan.
          </div>
        </div>
      </div>
      <div class="lg:col-span-7 space-y-space-md">
        <?php if ($tentangParas): ?>
        <?php foreach ($tentangParas as $para): ?>
        <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed"><?= nl2br(e($para)) ?></p>
        <?php endforeach; ?>
        <?php else: ?>
        <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
          Sakinah Journeys mendampingi keluarga Indonesia menuju Tanah Suci dengan tempo yang lapang, pendampingan dokter dan muthawwif bersertifikat, serta biaya yang disampaikan terbuka sejak awal.
        </p>
        <?php endif; ?>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-space-sm pt-space-sm border-t border-[#E4D8D0]">
          <div>
            <span class="font-headline-md text-headline-md text-primary block"><?= !empty($profil['tahun_berdiri']) ? (int) $profil['tahun_berdiri'] : '—' ?></span>
            <span class="font-label-sm text-label-sm text-on-surface-variant">Berdiri &amp; berizin resmi</span>
          </div>
          <div>
            <span class="font-headline-md text-headline-md text-primary block"><?= !empty($profil['stat_grup_maks']) ? (int) $profil['stat_grup_maks'] : '—' ?></span>
            <span class="font-label-sm text-label-sm text-on-surface-variant">Maksimal jamaah/grup</span>
          </div>
          <div>
            <span class="font-headline-md text-headline-md text-primary block"><?= !empty($profil['stat_rasio_pembimbing']) ? '1:' . (int) $profil['stat_rasio_pembimbing'] : '—' ?></span>
            <span class="font-label-sm text-label-sm text-on-surface-variant">Rasio pembimbing</span>
          </div>
          <div>
            <span class="font-headline-md text-headline-md text-primary block"><?= !empty($profil['stat_sesi_manasik']) ? (int) $profil['stat_sesi_manasik'] . '&times;' : '—' ?></span>
            <span class="font-label-sm text-label-sm text-on-surface-variant">Sesi manasik wajib</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Nilai Bimbingan -->
<section class="w-full bg-[#F2EEE6] py-space-xl">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="text-center max-w-2xl mx-auto mb-space-lg">
      <span class="font-label-md text-label-md text-[#1F5C56] font-medium block mb-1">Nilai yang Kami Pegang</span>
      <h2 class="font-headline-lg text-headline-lg text-[#2B2520]">Tiga pilar pelayanan Sakinah</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter-desktop">
      <?php foreach ($nilai as $n): ?>
      <div class="p-space-md rounded-xl bg-white shadow-sm flex flex-col gap-space-xs">
        <div class="w-12 h-12 rounded-lg bg-[#F2EEE6] flex items-center justify-center text-[#1F5C56]">
          <span class="material-symbols-outlined text-[26px]"><?= e($n['icon']) ?></span>
        </div>
        <h3 class="font-headline-sm text-headline-sm text-[#2B2520] font-medium"><?= e($n['title']) ?></h3>
        <p class="font-body-sm text-body-sm text-[#5C524A] leading-relaxed"><?= e($n['text']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Kontak & Peta -->
<section class="w-full bg-white py-space-xl">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
    <div class="lg:col-span-5">
      <span class="font-label-md text-label-md text-[#1F5C56] font-medium block mb-1">Griya Konsultasi</span>
      <h2 class="font-headline-lg text-headline-lg text-[#2B2520] mb-space-sm">Kunjungi kami</h2>
      <div class="flex flex-col gap-space-sm font-body-md text-body-md text-[#5C524A]">
        <div class="flex items-start gap-2">
          <span class="material-symbols-outlined text-[#1F5C56] text-[20px]">location_on</span>
          <span><?= trim((string) ($profil['alamat'] ?? '')) !== '' ? e($profil['alamat']) : 'Alamat kantor belum tersedia' ?></span>
        </div>
        <div class="flex items-start gap-2">
          <span class="material-symbols-outlined text-[#1F5C56] text-[20px]">call</span>
          <span><?= trim((string) ($profil['telepon'] ?? '')) !== '' ? e($profil['telepon']) : 'Nomor telepon belum tersedia' ?></span>
        </div>
        <?php $emailTentang = trim((string) ($profil['email'] ?? '')); ?>
        <?php if ($emailTentang !== ''): ?>
        <div class="flex items-start gap-2">
          <span class="material-symbols-outlined text-[#1F5C56] text-[20px]">mail</span>
          <a class="hover:text-[#1F5C56] underline underline-offset-4" href="mailto:<?= e($emailTentang) ?>"><?= e($emailTentang) ?></a>
        </div>
        <?php endif; ?>
        <div class="flex items-start gap-2">
          <span class="material-symbols-outlined text-[#1F5C56] text-[20px]">schedule</span>
          <span><?= trim((string) ($profil['jam_operasional'] ?? '')) !== '' ? e($profil['jam_operasional']) : 'Jam operasional belum tersedia' ?></span>
        </div>
      </div>
      <a class="inline-flex items-center justify-center px-space-md py-space-sm mt-space-md bg-[#B8923F] text-white font-label-md text-label-md rounded hover:opacity-95 transition-opacity" href="<?= url('kontak') ?>#konsultasi">Jadwalkan Konsultasi</a>
    </div>
    <div class="lg:col-span-7">
      <?php if (!empty($profil['maps_embed'])): ?>
      <div class="rounded-xl overflow-hidden border border-[#E4D8D0] shadow-sm h-80 lg:h-full min-h-[320px]">
        <iframe class="w-full h-full" style="border:0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                src="<?= e(maps_embed_src((string) $profil['maps_embed'])) ?>" title="Peta lokasi"></iframe>
      </div>
      <?php else: ?>
      <div class="rounded-xl border border-[#E4D8D0] bg-[#F2EEE6] h-80 lg:h-full min-h-[320px] flex flex-col items-center justify-center gap-space-xs text-center p-space-md">
        <span class="material-symbols-outlined text-[#1F5C56] text-[40px]">map</span>
        <span class="font-headline-sm text-headline-sm text-[#2B2520]">Peta Belum Tersedia</span>
        <span class="font-body-sm text-body-sm text-[#5C524A] max-w-sm">Petunjuk arah Google Maps akan tampil setelah lokasi Griya Sakinah dikonfirmasi lewat kanal konsultasi.</span>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php require PUBLIC_PATH . '/partials/footer.php'; ?>
