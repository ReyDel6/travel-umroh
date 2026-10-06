<?php
/** Daftar Testimoni Jamaah — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'testimoni';
$metaTitle = 'Kelola Testimoni — CMS ' . APP_NAME;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'hapus') {
        $id = (int) ($_POST['id'] ?? 0);
        $row = model('testimoni')->find($id);
        if (!$row) {
            flash_set('error', 'Data tidak ditemukan.');
            redirect('admin/testimoni/index.php');
        }
        model('testimoni')->delete($id);
        flash_set('success', 'Testimoni dari "' . e($row['nama']) . '" berhasil dihapus.');
        redirect('admin/testimoni/index.php');
    }
}

$items = model('testimoni')->getAll();
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
          <span class="text-on-surface font-semibold">Testimoni</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Kelola Testimoni Jamaah</h1>
      </div>
      <a class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3.5 py-1.5 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm" href="<?= url('admin/testimoni/create.php') ?>">
        <span class="material-symbols-outlined text-[17px]">add</span>
        Tambah Testimoni
      </a>
    </div>

    <!-- Tabel Testimoni -->
    <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
      <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
        <div class="flex items-center gap-2">
          <span class="font-headline-sm text-headline-sm text-primary">Daftar Testimoni</span>
          <span class="font-label-sm text-label-sm text-on-surface-variant"><?= $total ?> data</span>
        </div>
        <span class="font-label-sm text-label-sm text-on-surface-variant">Diurutkan berdasarkan nomor urut</span>
      </div>
      <div class="w-full overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm tracking-normal select-none">
              <th class="py-2.5 px-3 font-semibold min-w-[160px]" scope="col">Nama</th>
              <th class="py-2.5 px-3 font-semibold min-w-[120px]" scope="col">Asal</th>
              <th class="py-2.5 px-3 font-semibold min-w-[300px]" scope="col">Testimoni</th>
              <th class="py-2.5 px-3 font-semibold text-center w-24" scope="col">Urutan</th>
              <th class="py-2.5 px-3 font-semibold text-right" scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y-0 text-on-surface font-body-sm text-body-sm">
            <?php if (!$items): ?>
            <tr><td class="py-6 px-3 text-center text-on-surface-variant" colspan="5">Belum ada testimoni terdaftar.</td></tr>
            <?php endif; ?>
            <?php foreach ($items as $i => $r): ?>
            <tr class="<?= $i % 2 ? 'bg-surface-container-low/20 ' : '' ?>hover:bg-surface-container-low/60 transition-colors">
              <td class="py-2.5 px-3">
                <a class="font-medium text-on-surface hover:text-primary transition-colors" href="<?= url('admin/testimoni/edit.php?id=' . (int) $r['id']) ?>"><?= e($r['nama']) ?></a>
              </td>
              <td class="py-2.5 px-3 text-on-surface-variant"><?= e($r['asal_kota'] ?? '') ?: '—' ?></td>
              <td class="py-2.5 px-3 text-on-surface-variant"><?= e(excerpt($r['teks'] ?? '', 150)) ?></td>
              <td class="py-2.5 px-3 text-center">
                <span class="font-label-md text-label-md font-semibold text-primary-container tabular-nums"><?= (int) $r['urutan'] ?></span>
              </td>
              <td class="py-2.5 px-3 text-right">
                <div class="inline-flex items-center gap-2.5 font-label-md text-label-md">
                  <a class="text-primary-container font-semibold hover:underline" href="<?= url('admin/testimoni/edit.php?id=' . (int) $r['id']) ?>">Edit</a>
                  <form method="post" action="<?= url('admin/testimoni/index.php') ?>" data-confirm="Yakin hapus testimoni dari &quot;<?= e($r['nama']) ?>&quot;?">
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