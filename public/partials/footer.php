<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/**
 * Footer public — seluruh isi diambil dari CMS (profil_perusahaan), bukan hardcode.
 * Variabel: $profil
 */
$navFooter = [
    ['label' => 'Beranda Utama', 'href' => url('')],
    ['label' => 'Paket Umroh Reguler & Eksekutif', 'href' => url('paket')],
    ['label' => 'Jurnal & Panduan Ibadah', 'href' => url('artikel')],
    ['label' => 'Galeri Dokumentasi Jamaah', 'href' => url('galeri')],
    ['label' => 'Tentang Kami & Asatidzah', 'href' => url('tentang')],
    ['label' => 'Pertanyaan yang Sering Diajukan', 'href' => url('faq')],
];
?>
</div><!-- /.flex-col -->
</main>
<footer class="w-full bg-surface-container-low text-on-surface pt-space-xl pb-space-lg">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-gutter-desktop pb-space-xl">
      <div class="lg:col-span-4 flex flex-col gap-space-sm">
        <div class="flex items-center gap-space-sm mb-space-xs">
          <img alt="<?= e($profil['nama_perusahaan'] ?? APP_NAME) ?> Logo" class="h-7 w-auto object-contain" src="<?= asset('img/logo.png') ?>"/>
          <span class="font-headline-sm text-headline-sm text-primary"><?= e($profil['nama_perusahaan'] ?? APP_NAME) ?></span>
        </div>
        <p class="font-body-sm text-body-sm text-on-surface-variant max-w-sm"><?= e($profil['tagline'] ?? '') ?> — menghadirkan perjalanan ibadah ke Tanah Suci yang penuh kesantunan, kedalaman bimbingan spiritual, serta kenyamanan yang menenteramkan keluarga.</p>
        <div class="mt-space-sm p-space-sm rounded bg-surface-container border border-outline-variant/40">
          <p class="font-label-sm text-label-sm text-primary font-semibold mb-1">Izin Resmi Kemenag RI</p>
          <p class="font-body-sm text-body-sm text-on-surface-variant"><?= trim((string) ($profil['izin_ppiu'] ?? '')) !== '' ? e($profil['izin_ppiu']) : 'PPIU berizin resmi' ?></p>
        </div>
      </div>

      <div class="lg:col-span-3 flex flex-col gap-space-sm">
        <h3 class="font-headline-sm text-headline-sm text-tertiary mb-space-xs">Peta Situs</h3>
        <ul class="flex flex-col gap-space-xs">
          <?php foreach ($navFooter as $item): ?>
            <li class="py-0.5"><a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="<?= $item['href'] ?>"><?= e($item['label']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="lg:col-span-5 flex flex-col gap-space-sm">
        <h3 class="font-headline-sm text-headline-sm text-tertiary mb-space-xs">Kantor Pelayanan &amp; Griya Konsultasi</h3>
        <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed"><?= e($profil['alamat'] ?? '-') ?></p>
        <div class="flex flex-col gap-1 text-on-surface-variant font-body-sm text-body-sm mt-space-xs">
          <p><span class="font-semibold text-tertiary">Surel:</span> <?= e($profil['email'] ?? '-') ?></p>
          <p><span class="font-semibold text-tertiary">Telepon:</span> <?= e($profil['telepon'] ?? '-') ?></p>
          <p><span class="font-semibold text-tertiary">Jam Layanan:</span> <?= e($profil['jam_operasional'] ?? '-') ?></p>
        </div>
        <div class="mt-space-sm pt-space-sm border-t border-outline-variant/40">
          <p class="font-label-sm text-label-sm text-tertiary font-semibold mb-1">Komitmen Pendampingan Keluarga</p>
          <p class="font-body-sm text-body-sm text-on-surface-variant">Rasio pembimbing <?= !empty($profil['stat_rasio_pembimbing']) ? '1:' . (int) $profil['stat_rasio_pembimbing'] : '1:15' ?> jamaah demi memastikan kekhusyukan doa, perhatian lansia, dan kenyamanan ibadah setiap anggota keluarga.</p>
        </div>
      </div>
    </div>

    <div class="pt-space-md border-t border-outline-variant/40 flex flex-col sm:flex-row items-center justify-between gap-space-sm text-on-surface-variant font-label-sm text-label-sm">
      <p>&copy; <?= date('Y') ?> <?= e($profil['nama_perusahaan'] ?? APP_NAME) ?>. Hak Cipta Dilindungi Undang-Undang.</p>
      <div class="flex items-center gap-space-md">
        <a class="hover:text-primary transition-colors" href="<?= url('tentang') ?>">Tentang Kami</a>
        <span class="text-outline-variant">&middot;</span>
        <a class="hover:text-primary transition-colors" href="<?= url('faq') ?>">FAQ</a>
        <span class="text-outline-variant">&middot;</span>
        <a class="hover:text-primary transition-colors" href="<?= url('kontak') ?>">Kontak</a>
      </div>
    </div>
  </div>
</footer>
<?php $flashNow = flash_get(); ?>
<?php if ($flashNow): ?>
<div id="flashBox" class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-4 z-50 sm:max-w-sm">
  <div class="bg-primary-container text-on-primary font-body-sm text-body-sm px-space-md py-space-sm rounded shadow-lg flex items-start gap-2">
    <span class="material-symbols-outlined text-[18px] mt-0.5"><?= ($flashNow['type'] ?? 'success') === 'error' ? 'error' : 'check_circle' ?></span>
    <span><?= e($flashNow['message']) ?></span>
  </div>
</div>
<script>setTimeout(function(){var b=document.getElementById('flashBox');if(b){b.style.display='none';}},5000);</script>
<?php endif; ?>
<script>
  // M19: loading state saat submit — cegah klik ganda, hanya untuk form POST
  document.addEventListener('submit', function (e) {
    if (e.defaultPrevented) return;
    var form = e.target;
    if (!form || form.tagName !== 'FORM') return;
    if ((form.getAttribute('method') || 'get').toLowerCase() === 'get') return;
    var btn = form.querySelector('button[type="submit"], button:not([type])');
    if (!btn || btn.disabled) return;
    btn.disabled = true;
    btn.classList.add('opacity-70', 'cursor-wait');
    if (!btn.dataset.label) btn.dataset.label = btn.textContent;
    btn.textContent = 'Mengirim...';
  });
</script>
</body>
</html>
