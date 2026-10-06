<?php
/** Daftar Jadwal Keberangkatan — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'jadwal';
$metaTitle = 'Jadwal Keberangkatan — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'hapus') {
        $id = (int) ($_POST['id'] ?? 0);
        $row = model('jadwal')->find($id);
        if (!$row) {
            flash_set('error', 'Data tidak ditemukan.');
            redirect('admin/jadwal/index.php');
        }
        model('jadwal')->delete($id);
        flash_set('success', 'Jadwal keberangkatan berhasil dihapus.');
        redirect('admin/jadwal/index.php');
    }
}

$statusFilter = (string) ($_GET['status'] ?? 'semua');
if (!in_array($statusFilter, ['semua', 'dibuka', 'penuh', 'ditutup'], true)) {
    $statusFilter = 'semua';
}

$semua = model('jadwal')->getAll();
if ($statusFilter !== 'semua') {
    $semua = array_values(array_filter($semua, static function (array $j) use ($statusFilter): bool {
        return (string) $j['status'] === $statusFilter;
    }));
}
$total = count($semua);
$halaman = max(1, (int) ($_GET['halaman'] ?? 1));
$totalHalaman = (int) ceil($total / PER_PAGE_ADMIN);
if ($totalHalaman > 0 && $halaman > $totalHalaman) {
    $halaman = $totalHalaman;
}
$list = array_slice($semua, ($halaman - 1) * PER_PAGE_ADMIN, PER_PAGE_ADMIN);

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
          <span class="text-on-surface font-semibold">Jadwal Keberangkatan</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Jadwal Keberangkatan</h1>
      </div>
      <a class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3.5 py-1.5 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm" href="<?= url('admin/jadwal/create.php') ?>">
        <span class="material-symbols-outlined text-[17px]">add</span>
        Tambah Jadwal
      </a>
    </div>

    <!-- Tabel Jadwal -->
    <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 px-3 py-2.5 bg-surface-container-low">
        <div class="flex items-center gap-2">
          <span class="font-headline-sm text-headline-sm text-primary">Daftar Jadwal</span>
          <span class="font-label-sm text-label-sm text-on-surface-variant"><?= $total ?> data<?= $statusFilter !== 'semua' ? ' · status ' . e($statusFilter) : '' ?></span>
        </div>
        <div class="flex items-center gap-2">
          <label class="font-label-sm text-label-sm text-on-surface-variant shrink-0" for="filterStatus">Filter status</label>
          <select class="bg-surface-container-lowest rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest focus:outline-none"
                  id="filterStatus"
                  onchange="window.location.href='<?= e(url('admin/jadwal/index.php')) ?>'+(this.value==='semua'?'':'?status='+encodeURIComponent(this.value))">
            <option value="semua" <?= $statusFilter === 'semua' ? 'selected' : '' ?>>Semua Status</option>
            <option value="dibuka" <?= $statusFilter === 'dibuka' ? 'selected' : '' ?>>Dibuka</option>
            <option value="penuh" <?= $statusFilter === 'penuh' ? 'selected' : '' ?>>Penuh</option>
            <option value="ditutup" <?= $statusFilter === 'ditutup' ? 'selected' : '' ?>>Ditutup</option>
          </select>
        </div>
      </div>
      <div class="w-full overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm tracking-normal select-none">
              <th class="py-2.5 px-3 font-semibold min-w-[200px]" scope="col">Tanggal Berangkat</th>
              <th class="py-2.5 px-3 font-semibold min-w-[220px]" scope="col">Nama Paket</th>
              <th class="py-2.5 px-3 font-semibold" scope="col">Kuota</th>
              <th class="py-2.5 px-3 font-semibold text-center" scope="col">Status</th>
              <th class="py-2.5 px-3 font-semibold text-right" scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y-0 text-on-surface font-body-sm text-body-sm">
            <?php if (!$list): ?>
            <tr><td class="py-6 px-3 text-center text-on-surface-variant" colspan="5"><?= $statusFilter !== 'semua' ? 'Tidak ada jadwal dengan status tersebut.' : 'Belum ada jadwal keberangkatan terdaftar.' ?></td></tr>
            <?php endif; ?>
            <?php foreach ($list as $i => $j):
                $tone = $j['status'] === 'dibuka' ? 'teal' : ($j['status'] === 'penuh' ? 'gold' : 'gray');
            ?>
            <tr class="<?= $i % 2 ? 'bg-surface-container-low/20 ' : '' ?>hover:bg-surface-container-low/60 transition-colors">
              <td class="py-2.5 px-3 font-label-md text-label-md font-medium text-on-surface"><?= tanggal((string) $j['tanggal_berangkat'], 'long') ?></td>
              <td class="py-2.5 px-3">
                <div class="flex flex-col">
                  <span class="font-medium text-on-surface"><?= e((string) $j['nama_paket']) ?></span>
                  <span class="font-label-sm text-label-sm text-on-surface-variant">Paket ID <?= (int) $j['paket_id'] ?> · <?= e((string) ($j['slug_paket'] ?? '')) ?></span>
                </div>
              </td>
              <td class="py-2.5 px-3 tabular-nums"><?= (int) $j['kuota'] ?> pax</td>
              <td class="py-2.5 px-3 text-center"><?= badge(ucfirst((string) $j['status']), $tone) ?></td>
              <td class="py-2.5 px-3 text-right">
                <div class="inline-flex items-center gap-2.5 font-label-md text-label-md">
                  <a class="text-primary-container font-semibold hover:underline" href="<?= url('admin/jadwal/edit.php?id=' . (int) $j['id']) ?>">Edit</a>
                  <form method="post" action="<?= url('admin/jadwal/index.php') ?>" data-confirm="Yakin hapus jadwal keberangkatan <?= e(tanggal((string) $j['tanggal_berangkat'])) ?> untuk &quot;<?= e((string) $j['nama_paket']) ?>&quot;?">
                    <?= csrf_field() ?>
                    <input type="hidden" name="aksi" value="hapus"/>
                    <input type="hidden" name="id" value="<?= (int) $j['id'] ?>"/>
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
        <?= pagination($total, PER_PAGE_ADMIN, $halaman, 'admin/jadwal/index.php', $statusFilter !== 'semua' ? ['status' => $statusFilter] : []) ?>
      </div>
      <?php endif; ?>
    </section>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
