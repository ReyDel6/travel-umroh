<?php
/** Daftar Artikel & Jurnal — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'artikel';
$metaTitle = 'Kelola Artikel — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'hapus') {
        $id = (int) ($_POST['id'] ?? 0);
        $row = model('artikel')->find($id);
        if (!$row) {
            flash_set('error', 'Data tidak ditemukan.');
            redirect('admin/artikel/index.php');
        }
        model('artikel')->delete($id);
        delete_upload($row['thumbnail'], 'artikel');
        flash_set('success', 'Artikel "' . $row['judul'] . '" berhasil dihapus.');
        redirect('admin/artikel/index.php');
    }
}

$cari = trim((string) ($_GET['cari'] ?? ''));
if (mb_strlen($cari) > 100) {
    $cari = mb_substr($cari, 0, 100);
}
$statusRaw = (string) ($_GET['status'] ?? '');
$status = in_array($statusRaw, ['publish', 'draft'], true) ? $statusRaw : '';
$statusOrNull = $status !== '' ? $status : null;

$semua = model('artikel')->getAll($statusOrNull, $cari);
$total = count($semua);
$totalPages = max(1, (int) ceil($total / PER_PAGE_ADMIN));
$halaman = min(max(1, (int) ($_GET['halaman'] ?? 1)), $totalPages);
$rows = array_slice($semua, ($halaman - 1) * PER_PAGE_ADMIN, PER_PAGE_ADMIN);

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
          <span class="text-on-surface font-semibold">Artikel &amp; Jurnal</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Kelola Artikel &amp; Jurnal</h1>
      </div>
      <a class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3.5 py-1.5 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm" href="<?= url('admin/artikel/create.php') ?>">
        <span class="material-symbols-outlined text-[17px]">add</span>
        Tulis Artikel
      </a>
    </div>

    <!-- Tabel Artikel -->
    <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 px-3 py-2.5 bg-surface-container-low">
        <div class="flex items-center gap-2">
          <span class="font-headline-sm text-headline-sm text-primary">Daftar Artikel</span>
          <span class="font-label-sm text-label-sm text-on-surface-variant"><?= $total ?> data</span>
        </div>
        <form class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2" method="get" action="<?= url('admin/artikel/index.php') ?>">
          <input class="w-full sm:w-64 bg-surface-container-lowest rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest placeholder:text-outline"
                 type="search" name="cari" value="<?= e($cari) ?>" placeholder="Cari judul artikel..."/>
          <select class="bg-surface-container-lowest rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest cursor-pointer"
                  name="status" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="publish"<?= $status === 'publish' ? ' selected' : '' ?>>Publish</option>
            <option value="draft"<?= $status === 'draft' ? ' selected' : '' ?>>Draft</option>
          </select>
          <button class="shrink-0 bg-surface-container hover:bg-surface-container-high text-on-surface rounded px-3 py-2 font-label-md text-label-md shadow-sm transition-colors" type="submit">Cari</button>
          <?php if ($cari !== '' || $status !== ''): ?>
          <a class="shrink-0 font-label-md text-label-md text-outline hover:text-error transition-colors text-center" href="<?= url('admin/artikel/index.php') ?>">Reset</a>
          <?php endif; ?>
        </form>
      </div>
      <div class="w-full overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm tracking-normal select-none">
              <th class="py-2.5 px-3 font-semibold min-w-[260px]" scope="col">Judul Artikel</th>
              <th class="py-2.5 px-3 font-semibold" scope="col">Status</th>
              <th class="py-2.5 px-3 font-semibold whitespace-nowrap" scope="col">Tanggal</th>
              <th class="py-2.5 px-3 font-semibold text-right" scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y-0 text-on-surface font-body-sm text-body-sm">
            <?php if (!$rows): ?>
            <tr><td class="py-6 px-3 text-center text-on-surface-variant" colspan="4"><?= ($cari !== '' || $status !== '') ? 'Tidak ada artikel yang cocok dengan filter.' : 'Belum ada artikel terdaftar.' ?></td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $i => $r): ?>
            <tr class="<?= $i % 2 ? 'bg-surface-container-low/20 ' : '' ?>hover:bg-surface-container-low/60 transition-colors">
              <td class="py-2.5 px-3">
                <div class="flex flex-col">
                  <a class="font-medium text-on-surface hover:text-primary transition-colors" href="<?= url('admin/artikel/edit.php?id=' . (int) $r['id']) ?>"><?= e($r['judul']) ?></a>
                  <span class="font-label-sm text-label-sm text-on-surface-variant line-clamp-2"><?= e(excerpt($r['konten'] ?? '', 120)) ?></span>
                </div>
              </td>
              <td class="py-2.5 px-3"><?= badge($r['status'] === 'publish' ? 'Publish' : 'Draft', $r['status'] === 'publish' ? 'teal' : 'gray') ?></td>
              <td class="py-2.5 px-3 whitespace-nowrap font-label-md text-label-md text-on-surface-variant"><?= tanggal($r['created_at']) ?></td>
              <td class="py-2.5 px-3 text-right">
                <div class="inline-flex items-center gap-2.5 font-label-md text-label-md">
                  <a class="text-primary-container font-semibold hover:underline" href="<?= url('admin/artikel/edit.php?id=' . (int) $r['id']) ?>">Edit</a>
                  <form method="post" action="<?= url('admin/artikel/index.php') ?>" data-confirm="Yakin hapus artikel &quot;<?= e($r['judul']) ?>&quot;?">
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

    <?= pagination($total, PER_PAGE_ADMIN, $halaman, 'admin/artikel/index.php', ['cari' => $cari, 'status' => $status]) ?>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
