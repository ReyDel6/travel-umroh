<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/** Halaman Kontak & Form Konsultasi — design: kontak.html */
$metaTitle = 'Kontak & Griya Konsultasi — ' . APP_NAME;
$metaDescription = 'Sampaikan rencana ibadah keluarga Anda kepada concierge Sakinah Journeys. Respons rata-rata dalam 24 jam kerja.';

$errors = [];
$old = [];
$submitted = false;

$teleponProfil = trim((string) ($profil['telepon'] ?? ''));
$bagianTelepon = array_values(array_filter(array_map('trim', explode('/', $teleponProfil))));
$teleponUtama = $bagianTelepon[0] ?? '';
// nomor WhatsApp: bagian kedua bila tersedia, kalau tidak pakai nomor pertama
$digitWa = preg_replace('/\D+/', '', $bagianTelepon[1] ?? ($bagianTelepon[0] ?? '')) ?: '';
if (str_starts_with($digitWa, '0')) {
    $digitWa = '62' . substr($digitWa, 1);
}
$waNumber = $digitWa !== '' ? substr($digitWa, 0, 12) : '';

$waktuPilihan = [
    'awal_musim' => 'Awal Musim (Nov - Des)',
    'musim_sejuk' => 'Musim Sejuk (Jan - Feb)',
    'ramadhan'   => 'Ramadhan',
    'syawal'     => 'Syawal Pasca Lebaran',
    'lainnya'    => 'Lainnya / Menyesuaikan Tanggal Khusus',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'kirim_inquiry') {
    verify_csrf();

    $old = [
        'nama'     => trim((string) ($_POST['nama'] ?? '')),
        'kontak'   => trim((string) ($_POST['kontak'] ?? '')),
        'email'    => trim((string) ($_POST['email'] ?? '')),
        'paket_id' => trim((string) ($_POST['paket_id'] ?? '')),
        'waktu'    => trim((string) ($_POST['waktu'] ?? '')),
        'pesan'    => trim((string) ($_POST['pesan'] ?? '')),
        'pesan_asli' => trim((string) ($_POST['pesan'] ?? '')),
    ];

    $errors = validate_inquiry($old);

    $paketId = null;
    if ($old['paket_id'] !== '') {
        $paketTerpilih = model('paket')->find((int) $old['paket_id']);
        if (!$paketTerpilih || $paketTerpilih['status'] !== 'aktif') {
            $errors['paket_id'] = 'Paket yang dipilih tidak tersedia.';
        } else {
            $paketId = (int) $paketTerpilih['id'];
        }
    }

    $waktuLabel = $waktuPilihan[$old['waktu']] ?? '';
    $pesanGabungan = trim(
        ($waktuLabel !== '' ? 'Perkiraan keberangkatan: ' . $waktuLabel . "\n" : '')
        . $old['pesan']
    );
    $old['pesan'] = $pesanGabungan;

    if ($pesanGabungan === '') {
        $errors['pesan'] = 'Catatan kebutuhan khusus wajib diisi.';
    } elseif (mb_strlen($pesanGabungan) > 2000) {
        $errors['pesan'] = 'Catatan terlalu panjang (maksimal 2000 karakter).';
    }

    if (!$errors) {
        model('inquiry')->create([
            'nama'     => mb_substr($old['nama'], 0, 100),
            'kontak'   => mb_substr($old['kontak'], 0, 100),
            'email'    => mb_substr($old['email'], 0, 100),
            'paket_id' => $paketId,
            'pesan'    => $pesanGabungan,
        ]);
        flash_set('success', 'Terima kasih. Permohonan konsultasi kami terima — concierge akan menghubungi Anda dalam 24 jam kerja.');
        redirect('kontak');
    }
    $submitted = true;
}

$paketOptions = model('paket')->getAllAktif();

meta_halaman_apply('kontak', $metaTitle, $metaDescription);

require PUBLIC_PATH . '/partials/head.php';
require PUBLIC_PATH . '/partials/header.php';
?>
<!-- Subtle Architectural Atmospheric Band -->
<div class="w-full bg-surface-container-low/70 py-space-lg">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <nav aria-label="Jejak Navigasi" class="flex items-center gap-space-xs text-on-surface-variant font-label-md text-label-md mb-space-md">
      <a class="hover:text-primary transition-colors" href="<?= url('') ?>">Beranda</a>
      <span class="text-outline-variant font-normal">/</span>
      <span class="text-primary font-medium">Kontak &amp; Griya Konsultasi</span>
    </nav>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-md items-end">
      <div class="lg:col-span-8 flex flex-col gap-space-xs">
        <div class="inline-flex items-center gap-2 self-start px-3 py-1 rounded bg-surface-container-high text-primary font-label-sm text-label-sm">
          <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
          Pintu Silaturahmi &amp; Konsultasi Jamaah
        </div>
        <h1 class="font-display-lg text-display-lg-mobile lg:text-display-lg text-primary tracking-tight leading-tight">
          Kami Menyambut Rencana Ibadah Anda Sekeluarga
        </h1>
      </div>
      <div class="lg:col-span-4 lg:pb-2">
        <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
          Silakan berkonsultasi mengenai kesiapan fisik lansia, rincian akomodasi pelataran terdekat, hingga penyesuaian
          jadwal manasik privat bersama tim asatidzah dan concierge kami.
        </p>
      </div>
    </div>
  </div>
</div>
<!-- Main Content: Asymmetric 2-Column -->
<div class="w-full py-space-xl" id="konsultasi">
  <div class="max-w-7xl mx-auto px-margin lg:px-gutter-desktop">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter-desktop items-start">
      <!-- KOLOM KIRI -->
      <div class="lg:col-span-5 flex flex-col gap-space-lg">
        <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col gap-space-md">
          <div class="flex items-center justify-between pb-space-sm border-b border-surface-container-highest">
            <span class="font-label-md text-label-md text-secondary font-semibold">Griya Layanan Tamu</span>
            <span class="font-label-sm text-label-sm text-on-surface-variant">Wilayah Jakarta Selatan</span>
          </div>

          <div class="flex gap-space-sm">
            <div class="w-9 h-9 rounded bg-surface-container-high flex items-center justify-center shrink-0 text-primary">
              <span class="material-symbols-outlined text-[20px]">location_on</span>
            </div>
            <div class="flex flex-col gap-0.5">
              <span class="font-label-md text-label-md font-semibold text-tertiary">Alamat Kantor Griya Konsultasi</span>
              <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed"><?= e($profil['alamat'] ?? '-') ?></p>
            </div>
          </div>

          <div class="flex gap-space-sm">
            <div class="w-9 h-9 rounded bg-surface-container-high flex items-center justify-center shrink-0 text-primary">
              <span class="material-symbols-outlined text-[20px]">schedule</span>
            </div>
            <div class="flex flex-col gap-0.5">
              <span class="font-label-md text-label-md font-semibold text-tertiary">Waktu Pelayanan Tamu</span>
              <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed"><?= e($profil['jam_operasional'] ?? '-') ?></p>
              <span class="font-label-sm text-label-sm text-secondary font-normal mt-0.5">Kunjungan tatap muka disarankan membuat janji temu terlebih dahulu</span>
            </div>
          </div>

          <div class="h-px bg-surface-container-highest my-1"></div>

          <div class="flex flex-col gap-space-sm">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider font-semibold">Jalur Komunikasi Resmi</span>
            <?php if ($teleponUtama !== ''): ?>
            <a class="flex items-center justify-between p-space-sm rounded-lg bg-surface-container-low hover:bg-surface-container transition-colors group" href="tel:<?= e(preg_replace('/\s+/', '', $teleponUtama)) ?>">
              <div class="flex items-center gap-space-sm">
                <span class="material-symbols-outlined text-primary text-[20px]">call</span>
                <div class="flex flex-col">
                  <span class="font-label-sm text-label-sm text-on-surface-variant">Telepon Griya</span>
                  <span class="font-body-sm text-body-sm font-semibold text-tertiary group-hover:text-primary transition-colors"><?= e($teleponUtama) ?></span>
                </div>
              </div>
              <span class="font-label-sm text-label-sm text-primary-container font-medium px-2 py-0.5 bg-primary-fixed/40 rounded">Suara</span>
            </a>
            <?php else: ?>
            <div class="p-space-sm rounded-lg bg-surface-container-low flex items-center justify-between">
              <span class="font-label-sm text-label-sm text-on-surface-variant">Telepon Griya</span>
              <span class="font-body-sm text-body-sm text-on-surface-variant">-</span>
            </div>
            <?php endif; ?>
            <?php if ($waNumber !== ''): ?>
            <a class="flex items-center justify-between p-space-sm rounded-lg bg-surface-container-low hover:bg-surface-container transition-colors group" href="https://wa.me/<?= e($waNumber) ?>" rel="noopener noreferrer" target="_blank">
              <div class="flex items-center gap-space-sm">
                <span class="material-symbols-outlined text-primary text-[20px]">chat</span>
                <div class="flex flex-col">
                  <span class="font-label-sm text-label-sm text-on-surface-variant">WhatsApp Concierge Keluarga</span>
                  <span class="font-body-sm text-body-sm font-semibold text-tertiary group-hover:text-primary transition-colors"><?= e($teleponProfil) ?></span>
                </div>
              </div>
              <span class="font-label-sm text-label-sm text-primary font-medium px-2 py-0.5 bg-primary-fixed/40 rounded">Pesan Cepat</span>
            </a>
            <?php else: ?>
            <div class="p-space-sm rounded-lg bg-surface-container-low flex items-center justify-between">
              <span class="font-label-sm text-label-sm text-on-surface-variant">WhatsApp Concierge Keluarga</span>
              <span class="font-body-sm text-body-sm text-on-surface-variant">-</span>
            </div>
            <?php endif; ?>
            <?php $emailProfil = trim((string) ($profil['email'] ?? '')); ?>
            <?php if ($emailProfil !== ''): ?>
            <a class="flex items-center justify-between p-space-sm rounded-lg bg-surface-container-low hover:bg-surface-container transition-colors group" href="mailto:<?= e($emailProfil) ?>">
              <div class="flex items-center gap-space-sm">
                <span class="material-symbols-outlined text-primary text-[20px]">mail</span>
                <div class="flex flex-col">
                  <span class="font-label-sm text-label-sm text-on-surface-variant">Surat Elektronik Resmi</span>
                  <span class="font-body-sm text-body-sm font-semibold text-tertiary group-hover:text-primary transition-colors"><?= e($emailProfil) ?></span>
                </div>
              </div>
              <span class="font-label-sm text-label-sm text-secondary font-medium px-2 py-0.5 bg-secondary-fixed/50 rounded">Surel</span>
            </a>
            <?php else: ?>
            <div class="p-space-sm rounded-lg bg-surface-container-low flex items-center justify-between">
              <span class="font-label-sm text-label-sm text-on-surface-variant">Surat Elektronik Resmi</span>
              <span class="font-body-sm text-body-sm text-on-surface-variant">-</span>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Peta -->
        <div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm flex flex-col gap-space-sm">
          <div class="flex items-center justify-between">
            <span class="font-headline-sm text-headline-sm text-primary">Lokasi &amp; Aksesibilitas</span>
            <span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
              <span class="w-2 h-2 rounded-full bg-secondary"></span> Area Tenang &amp; Asri
            </span>
          </div>
          <?php if (!empty($profil['maps_embed'])): ?>
          <div class="relative w-full h-48 rounded-lg overflow-hidden">
            <iframe class="w-full h-full" style="border:0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                    src="<?= e($profil['maps_embed']) ?>" title="Peta lokasi Griya Sakinah"></iframe>
          </div>
          <?php else: ?>
          <div class="relative w-full h-48 rounded-lg bg-surface-container-high overflow-hidden flex items-center justify-center">
            <span class="font-label-md text-label-md text-tertiary">Peta belum tersedia</span>
          </div>
          <?php endif; ?>
          <div class="flex items-center gap-2 pt-1 text-on-surface-variant">
            <span class="material-symbols-outlined text-[18px] text-primary">accessible</span>
            <span class="font-body-sm text-body-sm">Dilengkapi fasilitas drop-off mobil dan parkir ramah kursi roda</span>
          </div>
          <?php if (!empty($profil['maps_embed'])): ?>
          <a class="mt-1 w-full py-2.5 px-4 bg-surface-container-high hover:bg-surface-container text-tertiary hover:text-primary font-label-md text-label-md rounded text-center transition-colors" href="<?= e($profil['maps_embed']) ?>" rel="noopener noreferrer" target="_blank">
            Buka Petunjuk Arah Google Maps
          </a>
          <?php endif; ?>
        </div>

        <div class="rounded-xl p-space-md bg-surface-container flex gap-space-sm items-start">
          <div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center shrink-0 text-on-primary-fixed">
            <span class="material-symbols-outlined text-[18px]">verified_user</span>
          </div>
          <div class="flex flex-col gap-1">
            <h2 class="font-headline-sm text-headline-sm text-primary">Kenyamanan Konsultasi Tatap Muka</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
              Tersedia ruang privat santai untuk diskusi keluarga bersama asatidzah dan dokter kontingen kami,
              dilengkapi akses tanpa tangga ramah lansia.
            </p>
          </div>
        </div>
      </div>

      <!-- KOLOM KANAN: Formulir -->
      <div class="lg:col-span-7">
        <div class="bg-surface-container-lowest rounded-xl p-space-lg lg:p-space-xl shadow-sm flex flex-col gap-space-md">
          <div class="flex flex-col gap-1 pb-space-sm border-b border-surface-container-highest">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-primary text-[22px]">edit_calendar</span>
              <h2 class="font-headline-lg text-headline-lg text-primary">Formulir Permohonan Konsultasi &amp; Janji Temu</h2>
            </div>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1 leading-relaxed">
              Sampaikan kebutuhan atau pertanyaan spesifik Anda. Kami akan menghubungi kembali dalam waktu maksimal 24 jam kerja.
            </p>
          </div>

          <?php if ($errors && $submitted): ?>
          <div class="p-space-sm rounded bg-error/10 text-error font-body-sm text-body-sm flex items-start gap-2" role="alert">
            <span class="material-symbols-outlined text-[18px] mt-0.5">error</span>
            <span>Beberapa isian belum lengkap. Mohon periksa kembali kolom yang ditandai merah.</span>
          </div>
          <?php endif; ?>

          <form class="flex flex-col gap-space-md pt-2" method="post" action="<?= url('kontak') ?>#konsultasi">
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" value="kirim_inquiry"/>

            <div class="flex flex-col gap-1.5">
              <label class="font-label-md text-label-md font-semibold text-tertiary flex items-center gap-1" for="full-name">
                <span>Nama Lengkap Penanggung Jawab / Jamaah</span>
                <span aria-hidden="true" class="text-error text-sm font-bold">*</span>
              </label>
              <input class="w-full px-space-md py-space-sm rounded bg-surface font-body-md text-body-md text-on-surface placeholder:text-outline-variant focus:outline-none focus:bg-surface-container-low transition-colors <?= isset($errors['nama']) ? 'border border-error' : '' ?>"
                     id="full-name" name="nama" type="text" required=""
                     placeholder="Contoh: H. Ahmad Subagio &amp; Keluarga"
                     value="<?= old_value($old, 'nama') ?>"/>
              <?= field_error($errors, 'nama') ?>
              <span class="font-label-sm text-label-sm text-on-surface-variant">Nama koordinator rombongan atau anggota keluarga yang mendaftar</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
              <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md font-semibold text-tertiary flex items-center gap-1" for="wa-number">
                  <span>Nomor WhatsApp Aktif</span>
                  <span aria-hidden="true" class="text-error text-sm font-bold">*</span>
                </label>
                <input class="w-full px-space-md py-space-sm rounded bg-surface font-body-md text-body-md text-on-surface placeholder:text-outline-variant focus:outline-none focus:bg-surface-container-low transition-colors <?= isset($errors['kontak']) ? 'border border-error' : '' ?>"
                       id="wa-number" name="kontak" type="tel" required="" placeholder="+62 812 3456 7890"
                       value="<?= old_value($old, 'kontak') ?>"/>
                <?= field_error($errors, 'kontak') ?>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Digunakan untuk konfirmasi jadwal janji temu</span>
              </div>
              <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md font-semibold text-tertiary" for="email-address">Alamat Surel (Email)</label>
                <input class="w-full px-space-md py-space-sm rounded bg-surface font-body-md text-body-md text-on-surface placeholder:text-outline-variant focus:outline-none focus:bg-surface-container-low transition-colors <?= isset($errors['email']) ? 'border border-error' : '' ?>"
                       id="email-address" name="email" type="email" placeholder="nama@domain.id"
                       value="<?= old_value($old, 'email') ?>"/>
                <?= field_error($errors, 'email') ?>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Untuk pengiriman penawaran resmi dan berkas perjalanan</span>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
              <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md font-semibold text-tertiary" for="package-choice">Pilihan Rencana Paket &amp; Durasi</label>
                <div class="relative">
                  <select class="w-full px-space-md py-space-sm rounded bg-surface font-body-md text-body-md text-on-surface focus:outline-none focus:bg-surface-container-low transition-colors cursor-pointer appearance-none pr-10 <?= isset($errors['paket_id']) ? 'border border-error' : '' ?>"
                          id="package-choice" name="paket_id">
                    <option value="">Belum Menentukan (Ingin Berdiskusi Dulu)</option>
                    <?php foreach ($paketOptions as $po): ?>
                    <option value="<?= (int) $po['id'] ?>"<?= old_value($old, 'paket_id') === (string) $po['id'] ? ' selected' : '' ?>>
                      <?= e($po['nama']) ?> (<?= (int) $po['durasi_hari'] ?> Hari)
                    </option>
                    <?php endforeach; ?>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-on-surface-variant">
                    <span class="material-symbols-outlined text-[20px]">expand_more</span>
                  </div>
                </div>
                <?= field_error($errors, 'paket_id') ?>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Dapat disesuaikan kembali saat sesi dialog</span>
              </div>
              <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md font-semibold text-tertiary" for="departure-timeline">Perkiraan Waktu Keberangkatan yang Diharapkan</label>
                <div class="relative">
                  <select class="w-full px-space-md py-space-sm rounded bg-surface font-body-md text-body-md text-on-surface focus:outline-none focus:bg-surface-container-low transition-colors cursor-pointer appearance-none pr-10"
                          id="departure-timeline" name="waktu">
                    <?php foreach ($waktuPilihan as $nilai => $label): ?>
                    <option value="<?= e($nilai) ?>"<?= old_value($old, 'waktu', 'awal_musim') === $nilai ? ' selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-on-surface-variant">
                    <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                  </div>
                </div>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Membantu kami memeriksa kuota hotel pelataran</span>
              </div>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="font-label-md text-label-md font-semibold text-tertiary" for="special-requirements">
                Catatan Kebutuhan Khusus (Kesehatan, Kursi Roda, Kamar Keluarga)
                <span aria-hidden="true" class="text-error text-sm font-bold">*</span>
              </label>
              <textarea class="w-full px-space-md py-space-sm rounded bg-surface font-body-md text-body-md text-on-surface placeholder:text-outline-variant focus:outline-none focus:bg-surface-container-low transition-colors resize-y <?= isset($errors['pesan']) ? 'border border-error' : '' ?>"
                        id="special-requirements" name="pesan" rows="4" required=""
                        placeholder="Ceritakan kondisi kesehatan jamaah sepuh, permintaan kursi dorong, kebutuhan menu diet pantangan, atau preferensi kamar keluarga berdampingan..."><?= old_value($old, 'pesan_asli') ?></textarea>
              <?= field_error($errors, 'pesan') ?>
              <span class="font-label-sm text-label-sm text-on-surface-variant">Keterbukaan informasi membantu dokter dan asatidzah menyiapkan protokol terbaik</span>
            </div>

            <div class="p-3 bg-surface-container-low rounded flex items-center gap-2 text-on-surface-variant">
              <span class="material-symbols-outlined text-[18px] text-tertiary shrink-0">shield</span>
              <p class="font-label-sm text-label-sm leading-normal">
                Data keluarga Anda dijaga dengan amanat kerahasiaan penuh semata-mata demi kenyamanan dan ketenteraman ibadah.
              </p>
            </div>

            <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-space-sm">
              <button class="w-full sm:w-auto px-space-lg py-space-sm rounded bg-primary-container hover:bg-primary text-on-primary font-label-md text-label-md transition-colors shadow-sm focus:outline-none active:translate-y-0.5" type="submit">
                Kirim Konsultasi
              </button>
              <span class="font-label-sm text-label-sm text-on-surface-variant">Respons rata-rata dalam 24 jam kerja</span>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Editorial Quiet Closing Quotation -->
<div class="w-full bg-surface-container-high py-space-lg">
  <div class="max-w-5xl mx-auto px-margin text-center flex flex-col items-center gap-space-xs">
    <span class="font-label-sm text-label-sm text-secondary font-medium tracking-wide">Prinsip Layanan Sakinah Journeys</span>
    <p class="font-headline-md text-headline-md text-primary max-w-2xl italic leading-relaxed">
      &ldquo;Ibadah yang tenang bermula dari persiapan yang lapang, jujur, dan terencana dengan khidmat.&rdquo;
    </p>
    <span class="font-label-md text-label-md text-on-surface-variant mt-1">Dewan Pembimbing Ibadah &amp; Manajemen Tamu Baitullah</span>
  </div>
</div>
<?php require PUBLIC_PATH . '/partials/footer.php'; ?>
