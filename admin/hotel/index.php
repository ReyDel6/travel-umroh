<?php
/** Daftar Hotel — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'hotel';
$metaTitle = 'Kelola Hotel — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'hapus') {
        $id = (int) ($_POST['id'] ?? 0);
        $row = model('hotel')->find($id);
        if (!$row) {
            flash_set('error', 'Data tidak ditemukan.');
            redirect('admin/hotel/index.php');
        }
        model('hotel')->delete($id);
        delete_upload($row['gambar'], 'hotel');
        flash_set('success', 'Hotel "' . $row['nama'] . '" berhasil dihapus.');
        redirect('admin/hotel/index.php');
    }
}

$cari = trim((string) ($_GET['cari'] ?? ''));
$items = model('hotel')->getAll();
if ($cari !== '') {
    $items = array_values(array_filter($items, static function (array $r) use ($cari): bool {
        return stripos((string) $r['nama'], $cari) !== false
            || stripos((string) ($r['kota'] ?? ''), $cari) !== false
            || stripos((string) ($r['kelas'] ?? ''), $cari) !== false;
    }));
}
$total = count($items);

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
          <span class="text-on-surface font-semibold">Hotel &amp; Akomodasi</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Kelola Hotel</h1>
      </div>
      <a class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3.5 py-1.5 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm" href="<?= url('admin/hotel/create.php') ?>">
        <span class="material-symbols-outlined text-[17px]">add</span>
        Tambah Hotel
      </a>
    </div>

    <!-- Tabel Hotel -->
    <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 px-3 py-2.5 bg-surface-container-low">
        <div class="flex items-center gap-2">
          <span class="font-headline-sm text-headline-sm text-primary">Daftar Hotel &amp; Akomodasi</span>
          <span class="font-label-sm text-label-sm text-on-surface-variant"><?= $total ?> data</span>
        </div>
        <form class="flex items-center gap-2" method="get" action="<?= url('admin/hotel/index.php') ?>">
          <input class="w-full md:w-64 bg-surface-container-lowest rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest placeholder:text-outline"
                 type="text" name="cari" value="<?= e($cari) ?>" placeholder="Cari nama, kota, atau kelas..."/>
          <button class="shrink-0 bg-surface-container hover:bg-surface-container-high text-on-surface rounded px-3 py-2 font-label-md text-label-md shadow-sm transition-colors" type="submit">Cari</button>
          <?php if ($cari !== ''): ?>
          <a class="shrink-0 font-label-md text-label-md text-outline hover:text-error transition-colors" href="<?= url('admin/hotel/index.php') ?>">Reset</a>
          <?php endif; ?>
        </form>
      </div>
      <div class="w-full overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm tracking-normal select-none">
              <th class="py-2.5 px-3 font-semibold w-16" scope="col">Foto</th>
              <th class="py-2.5 px-3 font-semibold min-w-[220px]" scope="col">Nama Hotel</th>
              <th class="py-2.5 px-3 font-semibold" scope="col">Kota</th>
              <th class="py-2.5 px-3 font-semibold" scope="col">Kelas</th>
              <th class="py-2.5 px-3 font-semibold text-right" scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y-0 text-on-surface font-body-sm text-body-sm">
            <?php if (!$items): ?>
            <tr><td class="py-6 px-3 text-center text-on-surface-variant" colspan="5"><?= $cari !== '' ? 'Tidak ada hotel yang cocok dengan pencarian.' : 'Belum ada hotel terdaftar.' ?></td></tr>
            <?php endif; ?>
            <?php foreach ($items as $i => $r): ?>
            <tr class="<?= $i % 2 ? 'bg-surface-container-low/20 ' : '' ?>hover:bg-surface-container-low/60 transition-colors">
              <td class="py-2.5 px-3">
                <img class="w-10 h-10 rounded bg-surface-container-low object-cover" src="<?= e(upload_url($r['gambar'] ?? null, 'hotel')) ?>" alt=""/>
              </td>
              <td class="py-2.5 px-3 font-medium text-on-surface"><?= e($r['nama']) ?></td>
              <td class="py-2.5 px-3"><?= e($r['kota'] ?? '') !== '' ? e($r['kota']) : '<span class="text-on-surface-variant">—</span>' ?></td>
              <td class="py-2.5 px-3"><?= e($r['kelas'] ?? '') !== '' ? e($r['kelas']) : '<span class="text-on-surface-variant">—</span>' ?></td>
              <td class="py-2.5 px-3 text-right">
                <div class="inline-flex items-center gap-2.5 font-label-md text-label-md">
                  <a class="text-primary-container font-semibold hover:underline" href="<?= url('admin/hotel/edit.php?id=' . (int) $r['id']) ?>">Edit</a>
                  <form method="post" action="<?= url('admin/hotel/index.php') ?>" data-confirm="Yakin hapus hotel &quot;<?= e($r['nama']) ?>&quot;?">
                    <?= csrf_field() ?>
                    <input type="hidden" name="aksi" value="hapus"/>
                    <input type="hidden" name="id" value="<?= (int) $r['id'] ?>"/>
                    <button class="font-label-md text-outline hover:text-error transition-colors cursor-pointer" type="submit">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
