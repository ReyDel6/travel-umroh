<?php
/** Daftar Inquiry & Konsultasi — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'inquiry';
$metaTitle = 'Inquiry & Konsultasi — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;

$statusRaw = (string) ($_GET['status'] ?? '');
$statusFilter = in_array($statusRaw, ['baru', 'ditindaklanjuti'], true) ? $statusRaw : '';
$redirectIndex = 'admin/inquiry/index.php' . ($statusFilter !== '' ? '?status=' . urlencode($statusFilter) : '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'status') {
        $id = (int) ($_POST['id'] ?? 0);
        $statusBaru = (string) ($_POST['status_baru'] ?? '');
        $row = model('inquiry')->find($id);
        if (!$row) {
            flash_set('error', 'Data tidak ditemukan.');
            redirect($redirectIndex);
        }
        if (!in_array($statusBaru, ['baru', 'ditindaklanjuti'], true)) {
            flash_set('error', 'Status tidak valid.');
            redirect($redirectIndex);
        }
        model('inquiry')->setStatus($id, $statusBaru);
        $labelStatus = $statusBaru === 'baru' ? 'Baru' : 'Ditindaklanjuti';
        flash_set('success', 'Status inquiry "' . $row['nama'] . '" diperbarui menjadi ' . $labelStatus . '.');
        redirect($redirectIndex);
    }

    if ($aksi === 'hapus') {
        $id = (int) ($_POST['id'] ?? 0);
        $row = model('inquiry')->find($id);
        if (!$row) {
            flash_set('error', 'Data tidak ditemukan.');
            redirect($redirectIndex);
        }
        model('inquiry')->delete($id);
        flash_set('success', 'Inquiry "' . $row['nama'] . '" berhasil dihapus.');
        redirect($redirectIndex);
    }
}

$items = model('inquiry')->getAll($statusFilter !== '' ? $statusFilter : null);
$total = count($items);
$jumlahBaru = model('inquiry')->countBaru();
$jumlahTotal = model('inquiry')->countAll();

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
          <span class="text-on-surface font-semibold">Inquiry &amp; Konsultasi</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Inquiry &amp; Konsultasi Jamaah</h1>
      </div>
    </div>

    <!-- Ringkasan -->
    <section class="grid grid-cols-1 sm:grid-cols-2 bg-surface-container-lowest rounded shadow-sm overflow-hidden">
      <div class="p-4 flex flex-col justify-between hover:bg-surface-container-low transition-colors">
        <div class="flex items-center justify-between text-on-surface-variant">
          <span class="font-label-sm text-label-sm">Inquiry Baru</span>
          <span class="w-2 h-2 rounded-full <?= $jumlahBaru > 0 ? 'bg-error' : 'bg-surface-tint' ?>"></span>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight tabular-nums"><?= (int) $jumlahBaru ?></span>
          <span class="font-label-sm text-label-sm <?= $jumlahBaru > 0 ? 'text-error font-medium' : 'text-on-surface-variant' ?>"><?= $jumlahBaru > 0 ? 'Perlu tindak lanjut' : 'Semua terlayani' ?></span>
        </div>
      </div>
      <div class="p-4 flex flex-col justify-between hover:bg-surface-container-low transition-colors bg-surface-container-low/40">
        <div class="flex items-center justify-between text-on-surface-variant">
          <span class="font-label-sm text-label-sm">Total Inquiry Masuk</span>
          <span class="material-symbols-outlined text-[18px] text-outline">support_agent</span>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight tabular-nums"><?= (int) $jumlahTotal ?></span>
          <span class="font-label-sm text-label-sm text-on-surface-variant">Terkirim sejak situs diluncurkan</span>
        </div>
      </div>
    </section>

    <!-- Tabel Inquiry -->
    <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 px-3 py-2.5 bg-surface-container-low">
        <div class="flex items-center gap-2">
          <span class="font-headline-sm text-headline-sm text-primary">Daftar Inquiry</span>
          <span class="font-label-sm text-label-sm text-on-surface-variant"><?= $total ?> data<?= $statusFilter !== '' ? ' · ' . e(ucfirst($statusFilter)) : '' ?></span>
        </div>
        <form class="flex items-center gap-2" method="get" action="<?= url('admin/inquiry/index.php') ?>">
          <label class="font-label-sm text-label-sm text-on-surface-variant" for="filterStatus">Status</label>
          <select class="bg-surface-container-lowest rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest cursor-pointer"
                  id="filterStatus" name="status" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="baru"<?= $statusFilter === 'baru' ? ' selected' : '' ?>>Baru</option>
            <option value="ditindaklanjuti"<?= $statusFilter === 'ditindaklanjuti' ? ' selected' : '' ?>>Ditindaklanjuti</option>
          </select>
          <?php if ($statusFilter !== ''): ?>
          <a class="font-label-md text-label-md text-outline hover:text-error transition-colors" href="<?= url('admin/inquiry/index.php') ?>">Reset</a>
          <?php endif; ?>
        </form>
      </div>
      <div class="w-full overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm tracking-normal select-none">
              <th class="py-2.5 px-3 font-semibold min-w-[190px]" scope="col">Nama &amp; Kontak</th>
              <th class="py-2.5 px-3 font-semibold min-w-[150px]" scope="col">Paket Diminati</th>
              <th class="py-2.5 px-3 font-semibold min-w-[220px]" scope="col">Pesan</th>
              <th class="py-2.5 px-3 font-semibold whitespace-nowrap" scope="col">Tanggal</th>
              <th class="py-2.5 px-3 font-semibold" scope="col">Status</th>
              <th class="py-2.5 px-3 font-semibold text-right" scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y-0 text-on-surface font-body-sm text-body-sm">
            <?php if (!$items): ?>
            <tr><td class="py-6 px-3 text-center text-on-surface-variant" colspan="6"><?= $statusFilter !== '' ? 'Tidak ada inquiry dengan status tersebut.' : 'Belum ada inquiry masuk.' ?></td></tr>
            <?php endif; ?>
            <?php foreach ($items as $i => $r): ?>
            <tr class="<?= $i % 2 ? 'bg-surface-container-low/20 ' : '' ?>hover:bg-surface-container-low/60 transition-colors">
              <td class="py-2.5 px-3">
                <div class="flex flex-col">
                  <a class="font-medium text-on-surface hover:text-primary transition-colors" href="<?= url('admin/inquiry/detail.php?id=' . (int) $r['id']) ?>"><?= e($r['nama']) ?></a>
                  <span class="font-label-sm text-label-sm text-on-surface-variant"><?= e($r['kontak']) ?></span>
                </div>
              </td>
              <td class="py-2.5 px-3"><?= !empty($r['nama_paket']) ? e($r['nama_paket']) : '<span class="text-on-surface-variant">—</span>' ?></td>
              <td class="py-2.5 px-3 text-on-surface-variant"><?= e(excerpt($r['pesan'] ?? '', 90)) ?></td>
              <td class="py-2.5 px-3 whitespace-nowrap font-label-md text-label-md text-on-surface-variant"><?= tanggal($r['created_at']) ?></td>
              <td class="py-2.5 px-3"><?= badge($r['status'] === 'baru' ? 'Baru' : 'Ditindaklanjuti', $r['status'] === 'baru' ? 'error' : 'teal') ?></td>
              <td class="py-2.5 px-3 text-right">
                <div class="inline-flex items-center gap-2.5 font-label-md text-label-md">
                  <a class="text-primary-container font-semibold hover:underline" href="<?= url('admin/inquiry/detail.php?id=' . (int) $r['id']) ?>">Detail</a>
                  <form method="post" action="<?= url('admin/inquiry/index.php') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="aksi" value="status"/>
                    <input type="hidden" name="id" value="<?= (int) $r['id'] ?>"/>
                    <input type="hidden" name="status_baru" value="<?= $r['status'] === 'baru' ? 'ditindaklanjuti' : 'baru' ?>"/>
                    <button class="font-label-md text-primary-container font-semibold hover:underline cursor-pointer" type="submit"><?= $r['status'] === 'baru' ? 'Tandai Selesai' : 'Tandai Baru' ?></button>
                  </form>
                  <form method="post" action="<?= url('admin/inquiry/index.php') ?>" data-confirm="Yakin hapus inquiry dari &quot;<?= e($r['nama']) ?>&quot;?">
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
