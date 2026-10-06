<?php
/** Daftar Galeri — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'galeri';
$metaTitle = 'Kelola Galeri — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'hapus') {
        $id = (int) ($_POST['id'] ?? 0);
        $row = model('galeri')->find($id);
        if (!$row) {
            flash_set('error', 'Data tidak ditemukan.');
            redirect('admin/galeri/index.php');
        }
        model('galeri')->delete($id);
        delete_upload($row['gambar'], 'galeri');
        flash_set('success', 'Foto "' . $row['judul'] . '" berhasil dihapus.');
        redirect('admin/galeri/index.php');
    }
}

$cari = trim((string) ($_GET['cari'] ?? ''));
$halaman = max(1, (int) ($_GET['halaman'] ?? 1));

$semua = model('galeri')->getAll();
if ($cari !== '') {
    $semua = array_values(array_filter($semua, static function (array $r) use ($cari): bool {
        return stripos((string) ($r['judul'] ?? ''), $cari) !== false;
    }));
}
$total = count($semua);
$totalHalaman = max(1, (int) ceil($total / PER_PAGE_ADMIN));
if ($halaman > $totalHalaman) {
    $halaman = $totalHalaman;
}
$items = array_slice($semua, ($halaman - 1) * PER_PAGE_ADMIN, PER_PAGE_ADMIN);

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
          <span class="text-on-surface font-semibold">Galeri Dokumentasi</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Kelola Galeri</h1>
      </div>
      <a class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3.5 py-1.5 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm" href="<?= url('admin/galeri/create.php') ?>">
        <span class="material-symbols-outlined text-[17px]">add</span>
        Tambah Foto
      </a>
    </div>

    <!-- Grid Galeri -->
    <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 px-3 py-2.5 bg-surface-container-low">
        <div class="flex items-center gap-2">
          <span class="font-headline-sm text-headline-sm text-primary">Galeri Dokumentasi</span>
          <span class="font-label-sm text-label-sm text-on-surface-variant"><?= $total ?> foto</span>
        </div>
        <form class="flex items-center gap-2" method="get" action="<?= url('admin/galeri/index.php') ?>">
          <input class="w-full md:w-64 bg-surface-container-lowest rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest placeholder:text-outline"
                 type="text" name="cari" value="<?= e($cari) ?>" placeholder="Cari judul foto..."/>
          <button class="shrink-0 bg-surface-container hover:bg-surface-container-high text-on-surface rounded px-3 py-2 font-label-md text-label-md shadow-sm transition-colors" type="submit">Cari</button>
          <?php if ($cari !== ''): ?>
          <a class="shrink-0 font-label-md text-label-md text-outline hover:text-error transition-colors" href="<?= url('admin/galeri/index.php') ?>">Reset</a>
          <?php endif; ?>
        </form>
      </div>

      <div class="p-3">
        <?php if (!$items): ?>
        <p class="py-8 text-center font-body-sm text-body-sm text-on-surface-variant"><?= $cari !== '' ? 'Tidak ada foto yang cocok dengan pencarian.' : 'Belum ada foto galeri.' ?></p>
        <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          <?php foreach ($items as $g): ?>
          <div class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col border border-surface-container">
            <a class="block aspect-[4/3] bg-surface-container-low overflow-hidden" href="<?= e(upload_url($g['gambar'] ?? null, 'galeri')) ?>" target="_blank" rel="noopener">
              <img class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" src="<?= e(upload_url($g['gambar'] ?? null, 'galeri')) ?>" alt="<?= e($g['judul'] ?? '') ?>"/>
            </a>
            <div class="p-3 flex flex-col gap-2">
              <div class="flex items-start justify-between gap-2">
                <span class="font-label-md text-label-md font-medium text-on-surface leading-snug"><?= e($g['judul'] ?? '') ?></span>
                <?php if (!empty($g['kategori'])): ?>
                <?= badge((string) $g['kategori'], 'gray') ?>
                <?php endif; ?>
              </div>
              <div class="flex items-center justify-between gap-2">
                <span class="font-label-sm text-label-sm text-on-surface-variant inline-flex items-center gap-1">
                  <span class="material-symbols-outlined text-[14px] text-outline">calendar_today</span>
                  <?= tanggal($g['created_at'] ?? null) ?>
                </span>
                <a class="font-label-md text-primary-container font-semibold hover:underline" href="<?= url('admin/galeri/edit.php?id=' . (int) $g['id']) ?>">Edit</a>
              </div>
              <form class="border-t border-surface-container pt-2" method="post" action="<?= url('admin/galeri/index.php') ?>" data-confirm="Yakin hapus foto &quot;<?= e($g['judul'] ?? '') ?>&quot;?">
                <?= csrf_field() ?>
                <input type="hidden" name="aksi" value="hapus"/>
                <input type="hidden" name="id" value="<?= (int) $g['id'] ?>"/>
                <button class="inline-flex items-center gap-1 font-label-md text-outline hover:text-error transition-colors cursor-pointer" type="submit">
                  <span class="material-symbols-outlined text-[16px]">delete</span>
                  Hapus Foto
                </button>
              </form>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?= pagination($total, PER_PAGE_ADMIN, $halaman, 'admin/galeri/index.php', $cari !== '' ? ['cari' => $cari] : []) ?>
      </div>
    </section>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
