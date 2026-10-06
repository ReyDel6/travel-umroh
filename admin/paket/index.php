<?php
/** Daftar Paket Umroh — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'paket';
$metaTitle = 'Kelola Paket Umroh — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'hapus') {
        $id = (int) ($_POST['id'] ?? 0);
        $row = model('paket')->find($id);
        if (!$row) {
            flash_set('error', 'Data tidak ditemukan.');
            redirect('admin/paket/index.php');
        }
        model('paket')->delete($id);
        delete_upload($row['thumbnail'] ?? null, 'paket');
        flash_set('success', 'Paket "' . $row['nama'] . '" berhasil dihapus.');
        redirect('admin/paket/index.php');
    }
}

$cari = trim((string) ($_GET['cari'] ?? ''));
$semua = model('paket')->getAll();
if ($cari !== '') {
    $semua = array_values(array_filter($semua, static function (array $p) use ($cari): bool {
        return stripos((string) $p['nama'], $cari) !== false;
    }));
}
$total = count($semua);
$halaman = max(1, (int) ($_GET['halaman'] ?? 1));
$totalHalaman = (int) ceil($total / PER_PAGE_ADMIN);
if ($totalHalaman > 0 && $halaman > $totalHalaman) {
    $halaman = $totalHalaman;
}
$list = array_slice($semua, ($halaman - 1) * PER_PAGE_ADMIN, PER_PAGE_ADMIN);

$paketTotal = model('paket')->countAll();
$paketAktif = model('paket')->countAktif();
$paketNonaktif = $paketTotal - $paketAktif;

require ADMIN_PATH . '/partials/head.php';
require ADMIN_PATH . '/partials/sidebar.php';
?>
<div class="flex flex-col w-full">
  <div class="flex flex-col gap-5 pt-4">
    <!-- Breadcrumb & Judul Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div class="flex flex-col gap-1">
        <div class="flex items-center gap-2 font-label-md text-label-md text-on-surface-variant">
          <span class="text-on-surface-variant">CMS Operasional</span>
          <span class="text-outline text-xs">/</span>
          <span class="text-on-surface font-semibold">Paket Umroh</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Kelola Paket Umroh</h1>
      </div>
      <a class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3.5 py-1.5 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm" href="<?= url('admin/paket/create.php') ?>">
        <span class="material-symbols-outlined text-[17px]">add</span>
        Tambah Paket
      </a>
    </div>

    <!-- Kartu Ringkasan -->
    <section class="grid grid-cols-1 sm:grid-cols-3 bg-surface-container-lowest rounded shadow-sm overflow-hidden">
      <div class="p-4 flex flex-col justify-between hover:bg-surface-container-low transition-colors">
        <div class="flex items-center justify-between text-on-surface-variant">
          <span class="font-label-sm text-label-sm">Jumlah Paket</span>
          <span class="material-symbols-outlined text-[18px] text-outline">inventory_2</span>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight tabular-nums"><?= (int) $paketTotal ?></span>
          <span class="font-label-sm text-label-sm text-on-surface-variant">total terdaftar</span>
        </div>
      </div>
      <div class="p-4 flex flex-col justify-between hover:bg-surface-container-low transition-colors border-t sm:border-t-0 sm:border-l border-surface-container">
        <div class="flex items-center justify-between text-on-surface-variant">
          <span class="font-label-sm text-label-sm">Paket Aktif</span>
          <span class="font-label-sm text-label-sm px-1.5 py-0.5 rounded bg-surface-container-high text-primary-container font-medium">Aktif</span>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight tabular-nums"><?= (int) $paketAktif ?></span>
          <span class="font-label-sm text-label-sm text-secondary font-medium">tayang di katalog</span>
        </div>
      </div>
      <div class="p-4 flex flex-col justify-between hover:bg-surface-container-low transition-colors border-t sm:border-t-0 sm:border-l border-surface-container">
        <div class="flex items-center justify-between text-on-surface-variant">
          <span class="font-label-sm text-label-sm">Paket Nonaktif</span>
          <span class="font-label-sm text-label-sm px-1.5 py-0.5 rounded bg-surface-container text-on-surface-variant font-medium">Nonaktif</span>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight tabular-nums"><?= (int) $paketNonaktif ?></span>
          <span class="font-label-sm text-label-sm text-on-surface-variant">disembunyikan dari publik</span>
        </div>
      </div>
    </section>

    <!-- Tabel Paket -->
    <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 px-3 py-2.5 bg-surface-container-low">
        <div class="flex items-center gap-2">
          <span class="font-headline-sm text-headline-sm text-primary">Daftar Paket Umroh</span>
          <span class="font-label-sm text-label-sm text-on-surface-variant"><?= $total ?> data</span>
        </div>
        <form class="flex items-center gap-2" method="get" action="<?= url('admin/paket/index.php') ?>">
          <input class="w-full md:w-64 bg-surface-container-lowest rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest placeholder:text-outline"
                 type="text" name="cari" value="<?= e($cari) ?>" placeholder="Cari nama paket..."/>
          <button class="shrink-0 bg-surface-container hover:bg-surface-container-high text-on-surface rounded px-3 py-2 font-label-md text-label-md shadow-sm transition-colors cursor-pointer" type="submit">Cari</button>
          <?php if ($cari !== ''): ?>
          <a class="shrink-0 font-label-md text-label-md text-outline hover:text-error transition-colors" href="<?= url('admin/paket/index.php') ?>">Reset</a>
          <?php endif; ?>
        </form>
      </div>
      <div class="w-full overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm tracking-normal select-none">
              <th class="py-2.5 px-3 font-semibold" scope="col">Kode</th>
              <th class="py-2.5 px-3 font-semibold min-w-[220px]" scope="col">Nama Paket &amp; Durasi</th>
              <th class="py-2.5 px-3 font-semibold" scope="col">Jumlah Jadwal</th>
              <th class="py-2.5 px-3 font-semibold text-right tabular-nums" scope="col">Harga Mulai</th>
              <th class="py-2.5 px-3 font-semibold text-center" scope="col">Status</th>
              <th class="py-2.5 px-3 font-semibold text-right" scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y-0 text-on-surface font-body-sm text-body-sm">
            <?php if (!$list): ?>
            <tr><td class="py-6 px-3 text-center text-on-surface-variant" colspan="6"><?= $cari !== '' ? 'Tidak ada paket yang cocok dengan pencarian.' : 'Belum ada paket terdaftar.' ?></td></tr>
            <?php endif; ?>
            <?php foreach ($list as $i => $p):
                $kode = 'SKN-' . strtoupper(substr((string) $p['slug'], 0, 3)) . (int) $p['durasi_hari'];
                $hargaRows = model('paket')->getHarga((int) $p['id']);
                $hargaMulai = $hargaRows ? min(array_map(static fn(array $h): float => (float) $h['harga'], $hargaRows)) : null;
            ?>
            <tr class="<?= $i % 2 ? 'bg-surface-container-low/20 ' : '' ?>hover:bg-surface-container-low/60 transition-colors">
              <td class="py-2.5 px-3 font-label-md text-label-md font-semibold text-primary-container tabular-nums"><?= e($kode) ?></td>
              <td class="py-2.5 px-3">
                <div class="flex flex-col">
                  <span class="font-medium text-on-surface"><?= e($p['nama']) ?></span>
                  <span class="font-label-sm text-label-sm text-on-surface-variant"><?= (int) $p['durasi_hari'] ?> Hari · <?= e($p['slug']) ?></span>
                </div>
              </td>
              <td class="py-2.5 px-3 tabular-nums"><?= (int) ($p['jumlah_jadwal'] ?? 0) ?> jadwal</td>
              <td class="py-2.5 px-3 text-right font-medium text-on-surface tabular-nums">
                <?= $hargaMulai !== null ? e(rupiah($hargaMulai)) : '<span class="text-on-surface-variant">Belum diisi</span>' ?>
              </td>
              <td class="py-2.5 px-3 text-center"><?= badge($p['status'] === 'aktif' ? 'Aktif' : 'Nonaktif', $p['status'] === 'aktif' ? 'teal' : 'gray') ?></td>
              <td class="py-2.5 px-3 text-right">
                <div class="inline-flex items-center gap-2.5 font-label-md text-label-md">
                  <a class="text-primary-container font-semibold hover:underline" href="<?= url('admin/paket/edit.php?id=' . (int) $p['id']) ?>">Edit</a>
                  <form method="post" action="<?= url('admin/paket/index.php') ?>" data-confirm="Yakin hapus paket &quot;<?= e($p['nama']) ?>&quot;? Seluruh jadwal dan relasi ikut terhapus.">
                    <?= csrf_field() ?>
                    <input type="hidden" name="aksi" value="hapus"/>
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>"/>
                    <button class="font-label-md text-outline hover:text-error transition-colors cursor-pointer" type="submit">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php if ($total): ?>
      <div class="px-3 pb-4">
        <?= pagination($total, PER_PAGE_ADMIN, $halaman, 'admin/paket/index.php', $cari !== '' ? ['cari' => $cari] : []) ?>
      </div>
      <?php endif; ?>
    </section>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
