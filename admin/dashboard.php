<?php
/** Dashboard CMS — design: admin/dashboard.html */
require_once dirname(__DIR__) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'ringkasan';
$metaTitle = 'Ringkasan Operasional — CMS ' . APP_NAME;

$paketAktif = model('paket')->countAktif();
$paketTotal = model('paket')->countAll();
$jadwalMendatang = model('jadwal')->countMendatang();
$artikelPublish = model('artikel')->countByStatus('publish');
$artikelDraft = model('artikel')->countByStatus('draft');
$inquiryBaru = model('inquiry')->countBaru();

$pakets = model('paket')->getAll();
$jadwalPerPaket = [];
foreach ($pakets as $p) {
    $jadwalPerPaket[$p['id']] = model('jadwal')->getByPaket((int) $p['id']);
}
$inquiryTerbaru = array_slice(model('inquiry')->getAll('baru'), 0, 5);
$jadwalTerdekat = model('jadwal')->getMendatang(5);

require ADMIN_PATH . '/partials/head.php';
require ADMIN_PATH . '/partials/sidebar.php';
?>
<div class="flex flex-col w-full">
  <div class="flex flex-col gap-5 pt-4">
    <!-- Breadcrumb & Top Utility Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div class="flex items-center gap-2 font-label-md text-label-md text-on-surface-variant">
        <span class="text-on-surface-variant">CMS Operasional</span>
        <span class="text-outline text-xs">/</span>
        <span class="text-on-surface font-semibold">Ringkasan</span>
      </div>
      <div class="flex items-center gap-2 self-start sm:self-auto font-label-sm text-label-sm text-on-surface-variant">
        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-surface-container text-on-surface-variant">
          <span class="w-1.5 h-1.5 rounded-full bg-surface-tint"></span>
          Sinkronisasi otomatis aktif
        </span>
        <span class="text-outline">·</span>
        <span>Diperbarui <?= e(date('d M Y')) ?></span>
      </div>
    </div>

    <!-- Section 1: Stat Strip -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 bg-surface-container-lowest rounded shadow-sm overflow-hidden">
      <div class="p-4 flex flex-col justify-between hover:bg-surface-container-low transition-colors">
        <div class="flex items-center justify-between text-on-surface-variant">
          <span class="font-label-sm text-label-sm">Jumlah Paket Aktif</span>
          <span class="font-label-sm text-label-sm px-1.5 py-0.5 rounded bg-surface-container-high text-primary-container font-medium">Aktif</span>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight tabular-nums"><?= (int) $paketAktif ?></span>
          <span class="font-label-sm text-label-sm text-secondary font-medium"><?= (int) $paketTotal ?> total terdaftar</span>
        </div>
      </div>
      <div class="p-4 flex flex-col justify-between hover:bg-surface-container-low transition-colors bg-surface-container-low/40">
        <div class="flex items-center justify-between text-on-surface-variant">
          <span class="font-label-sm text-label-sm">Jadwal Mendatang</span>
          <span class="material-symbols-outlined text-[18px] text-outline">calendar_today</span>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight tabular-nums"><?= (int) $jadwalMendatang ?></span>
          <span class="font-label-sm text-label-sm text-on-surface-variant">Jadwal keberangkatan terbuka</span>
        </div>
      </div>
      <div class="p-4 flex flex-col justify-between hover:bg-surface-container-low transition-colors">
        <div class="flex items-center justify-between text-on-surface-variant">
          <span class="font-label-sm text-label-sm">Artikel Jurnal</span>
          <span class="material-symbols-outlined text-[18px] text-outline">description</span>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight tabular-nums"><?= (int) ($artikelPublish + $artikelDraft) ?></span>
          <span class="font-label-sm text-label-sm text-on-surface-variant"><?= (int) $artikelPublish ?> terbit · <?= (int) $artikelDraft ?> draf</span>
        </div>
      </div>
      <div class="p-4 flex flex-col justify-between hover:bg-surface-container-low transition-colors bg-surface-container-low/40">
        <div class="flex items-center justify-between text-on-surface-variant">
          <span class="font-label-sm text-label-sm">Inquiry Baru</span>
          <span class="w-2 h-2 rounded-full <?= $inquiryBaru > 0 ? 'bg-error' : 'bg-surface-tint' ?>"></span>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight tabular-nums"><?= (int) $inquiryBaru ?></span>
          <span class="font-label-sm text-label-sm <?= $inquiryBaru > 0 ? 'text-error font-medium' : 'text-on-surface-variant' ?>">
            <?= $inquiryBaru > 0 ? 'Perlu tindak lanjut hari ini' : 'Semua terlayani' ?>
          </span>
        </div>
      </div>
    </section>

    <!-- Section 2: Fast Actions -->
    <section class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 bg-surface-container-lowest p-3 rounded shadow-sm">
      <div class="flex flex-wrap items-center gap-2.5 flex-1 min-w-0">
        <span class="font-label-md text-label-md text-on-surface-variant">Akses Cepat</span>
        <a class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors" href="<?= url('admin/inquiry/index.php') ?>">
          <span class="material-symbols-outlined text-[17px] text-outline">support_agent</span>
          Tanggapi Inquiry
        </a>
        <a class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors" href="<?= url('admin/jadwal/index.php') ?>">
          <span class="material-symbols-outlined text-[17px] text-outline">flight_takeoff</span>
          Atur Jadwal
        </a>
        <a class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors" href="<?= url('admin/artikel/index.php') ?>">
          <span class="material-symbols-outlined text-[17px] text-outline">edit_note</span>
          Tulis Artikel
        </a>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <a class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm" href="<?= url('admin/paket/create.php') ?>">
          <span class="material-symbols-outlined text-[17px]">add</span>
          Tambah Paket Baru
        </a>
      </div>
    </section>

    <!-- Section 3: Tabel Paket -->
    <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
      <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
        <span class="font-headline-sm text-headline-sm text-primary">Katalog Paket Umroh</span>
        <a class="font-label-sm text-label-sm text-primary-container font-semibold hover:underline" href="<?= url('admin/paket/index.php') ?>">Lihat semua</a>
      </div>
      <div class="w-full overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm tracking-normal select-none">
              <th class="py-2.5 px-3 font-semibold" scope="col">Kode</th>
              <th class="py-2.5 px-3 font-semibold min-w-[220px]" scope="col">Nama Paket &amp; Durasi</th>
              <th class="py-2.5 px-3 font-semibold" scope="col">Jadwal Terdekat</th>
              <th class="py-2.5 px-3 font-semibold" scope="col">Kuota Tersedia</th>
              <th class="py-2.5 px-3 font-semibold text-right tabular-nums" scope="col">Harga Mulai</th>
              <th class="py-2.5 px-3 font-semibold text-center" scope="col">Status</th>
              <th class="py-2.5 px-3 font-semibold text-right" scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y-0 text-on-surface font-body-sm text-body-sm">
            <?php if (!$pakets): ?>
            <tr><td class="py-6 px-3 text-center text-on-surface-variant" colspan="7">Belum ada paket terdaftar.</td></tr>
            <?php endif; ?>
            <?php foreach ($pakets as $i => $p):
                $jadwals = $jadwalPerPaket[$p['id']] ?? [];
                $kuotaTotal = 0;
                $kuotaTerpakai = 0;
                $tanggalTerdekat = '';
                foreach ($jadwals as $j) {
                    $kuotaTotal += (int) $j['kuota'];
                    if ($j['status'] === 'dibuka') {
                        $kuotaTerpakai += (int) $j['kuota'];
                    }
                    if ($j['status'] === 'dibuka' && ($tanggalTerdekat === '' || $j['tanggal_berangkat'] < $tanggalTerdekat)) {
                        $tanggalTerdekat = $j['tanggal_berangkat'];
                    }
                }
                $hargaRows = model('paket')->getHarga((int) $p['id']);
                $hargaTerendah = $hargaRows ? min(array_map(fn($h) => (float) $h['harga'], $hargaRows)) : null;
                $kode = 'SKN-' . strtoupper(substr($p['slug'], 0, 3)) . (int) $p['durasi_hari'];
                $persen = $kuotaTotal > 0 ? (int) round($kuotaTerpakai / $kuotaTotal * 100) : 0;
            ?>
            <tr class="<?= $i % 2 ? 'bg-surface-container-low/20 ' : '' ?>hover:bg-surface-container-low/60 transition-colors">
              <td class="py-2.5 px-3 font-label-md text-label-md font-semibold text-primary-container tabular-nums"><?= e($kode) ?></td>
              <td class="py-2.5 px-3">
                <div class="flex flex-col">
                  <span class="font-medium text-on-surface"><?= e($p['nama']) ?></span>
                  <span class="font-label-sm text-label-sm text-on-surface-variant"><?= (int) $p['durasi_hari'] ?> Hari · <?= count($jadwals) ?> jadwal</span>
                </div>
              </td>
              <td class="py-2.5 px-3">
                <?php if ($tanggalTerdekat): ?>
                <span class="font-label-md text-label-md text-on-surface"><?= tanggal($tanggalTerdekat) ?></span>
                <?php else: ?>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Belum dibuka</span>
                <?php endif; ?>
              </td>
              <td class="py-2.5 px-3">
                <div class="flex flex-col gap-1 w-full max-w-[130px]">
                  <div class="flex items-center justify-between font-label-sm text-label-sm tabular-nums">
                    <span class="font-semibold text-on-surface"><?= $kuotaTotal ?> pax</span>
                    <span class="<?= $persen >= 80 ? 'text-secondary font-medium' : 'text-on-surface-variant' ?>"><?= $persen ?>%</span>
                  </div>
                  <div class="w-full bg-surface-container h-1.5 rounded-full overflow-hidden">
                    <div class="<?= $persen >= 80 ? 'bg-secondary' : 'bg-primary-container' ?> h-full rounded-full" style="width: <?= $persen ?>%"></div>
                  </div>
                </div>
              </td>
              <td class="py-2.5 px-3 text-right font-medium text-on-surface tabular-nums">
                <?= $hargaTerendah !== null ? e(rupiah($hargaTerendah)) : '<span class="text-on-surface-variant">—</span>' ?>
              </td>
              <td class="py-2.5 px-3 text-center"><?= badge($p['status'] === 'aktif' ? 'Aktif' : 'Nonaktif', $p['status'] === 'aktif' ? 'teal' : 'gray') ?></td>
              <td class="py-2.5 px-3 text-right">
                <div class="inline-flex items-center gap-2.5 font-label-md text-label-md">
                  <a class="text-primary-container font-semibold hover:underline" href="<?= url('admin/paket/edit.php?id=' . (int) $p['id']) ?>">Edit</a>
                  <a class="text-outline hover:text-on-surface transition-colors" href="<?= url('paket-detail?slug=' . e($p['slug'])) ?>" rel="noopener" target="_blank">Lihat</a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Section 4: Jadwal Mendatang + Inquiry Baru -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <div class="bg-surface-container-lowest rounded shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
          <span class="font-headline-sm text-headline-sm text-primary">Jadwal Keberangkatan Terdekat</span>
          <a class="font-label-sm text-label-sm text-primary-container font-semibold hover:underline" href="<?= url('admin/jadwal/index.php') ?>">Kelola</a>
        </div>
        <ul class="divide-y divide-surface-container">
          <?php if (!$jadwalTerdekat): ?>
          <li class="px-3 py-4 font-body-sm text-body-sm text-on-surface-variant">Belum ada jadwal keberangkatan.</li>
          <?php endif; ?>
          <?php foreach ($jadwalTerdekat as $j): ?>
          <li class="px-3 py-2.5 flex items-center justify-between gap-3">
            <div class="flex flex-col">
              <span class="font-label-md text-label-md font-medium text-on-surface"><?= e($j['nama_paket']) ?></span>
              <span class="font-label-sm text-label-sm text-on-surface-variant"><?= tanggal($j['tanggal_berangkat'], 'long') ?> · <?= (int) $j['kuota'] ?> pax</span>
            </div>
            <?= badge(ucfirst($j['status']), $j['status'] === 'dibuka' ? 'teal' : ($j['status'] === 'penuh' ? 'gold' : 'gray')) ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="bg-surface-container-lowest rounded shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
          <span class="font-headline-sm text-headline-sm text-primary">Inquiry Konsultasi Terbaru</span>
          <a class="font-label-sm text-label-sm text-primary-container font-semibold hover:underline" href="<?= url('admin/inquiry/index.php') ?>">Tanggapi semua</a>
        </div>
        <ul class="divide-y divide-surface-container">
          <?php if (!$inquiryTerbaru): ?>
          <li class="px-3 py-4 font-body-sm text-body-sm text-on-surface-variant">Tidak ada inquiry baru.</li>
          <?php endif; ?>
          <?php foreach ($inquiryTerbaru as $inq): ?>
          <li class="px-3 py-2.5 flex items-center justify-between gap-3">
            <div class="flex flex-col min-w-0">
              <span class="font-label-md text-label-md font-medium text-on-surface"><?= e($inq['nama']) ?> <span class="text-outline">·</span> <?= e($inq['kontak']) ?></span>
              <span class="font-label-sm text-label-sm text-on-surface-variant truncate"><?= e(excerpt($inq['pesan'], 70)) ?></span>
            </div>
            <a class="shrink-0 font-label-sm text-label-sm text-primary-container font-semibold hover:underline" href="<?= url('admin/inquiry/detail.php?id=' . (int) $inq['id']) ?>">Buka</a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
