<?php
/** Detail Inquiry — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'inquiry';
$metaTitle = 'Detail Inquiry — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;

$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'status') {
        $postId = (int) ($_POST['id'] ?? 0);
        $statusBaru = (string) ($_POST['status_baru'] ?? '');
        $cek = model('inquiry')->find($postId);
        if (!$cek) {
            flash_set('error', 'Data tidak ditemukan.');
            redirect('admin/inquiry/index.php');
        }
        if (!in_array($statusBaru, ['baru', 'ditindaklanjuti'], true)) {
            flash_set('error', 'Status tidak valid.');
            redirect('admin/inquiry/detail.php?id=' . $postId);
        }
        model('inquiry')->setStatus($postId, $statusBaru);
        $labelStatus = $statusBaru === 'baru' ? 'Baru' : 'Ditindaklanjuti';
        flash_set('success', 'Status inquiry berhasil diperbarui menjadi ' . $labelStatus . '.');
        redirect('admin/inquiry/detail.php?id=' . $postId);
    }

    if ($aksi === 'hapus') {
        $postId = (int) ($_POST['id'] ?? 0);
        $row = model('inquiry')->find($postId);
        if (!$row) {
            flash_set('error', 'Data tidak ditemukan.');
            redirect('admin/inquiry/index.php');
        }
        model('inquiry')->delete($postId);
        flash_set('success', 'Inquiry "' . $row['nama'] . '" berhasil dihapus.');
        redirect('admin/inquiry/index.php');
    }
}

$inquiry = $id > 0 ? model('inquiry')->find($id) : null;
if (!$inquiry) {
    flash_set('error', 'Data tidak ditemukan.');
    redirect('admin/inquiry/index.php');
}

$kontak = (string) $inquiry['kontak'];
$kontakHref = '';
if (filter_var($kontak, FILTER_VALIDATE_EMAIL)) {
    $kontakHref = 'mailto:' . $kontak;
} elseif (preg_match('/^[0-9+\-\s()]{8,20}$/', $kontak)) {
    $kontakHref = 'tel:' . preg_replace('/[^0-9+]/', '', $kontak);
}

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
          <a class="text-on-surface-variant hover:text-primary transition-colors" href="<?= url('admin/inquiry/index.php') ?>">Inquiry &amp; Konsultasi</a>
          <span class="text-outline text-xs">/</span>
          <span class="text-on-surface font-semibold">Detail Inquiry</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Detail Inquiry Jamaah</h1>
      </div>
      <a class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3.5 py-1.5 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/inquiry/index.php') ?>">
        <span class="material-symbols-outlined text-[17px]">arrow_back</span>
        Kembali
      </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
      <!-- Kartu Utama: Detail -->
      <section class="lg:col-span-2 bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
          <span class="font-headline-sm text-headline-sm text-primary">Informasi Inquiry</span>
          <?= badge($inquiry['status'] === 'baru' ? 'Baru' : 'Ditindaklanjuti', $inquiry['status'] === 'baru' ? 'error' : 'teal') ?>
        </div>
        <div class="p-4 flex flex-col gap-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
              <span class="font-label-sm text-label-sm text-on-surface-variant">Nama Lengkap</span>
              <span class="font-label-md text-label-md font-medium text-on-surface"><?= e($inquiry['nama']) ?></span>
            </div>
            <div class="flex flex-col gap-1">
              <span class="font-label-sm text-label-sm text-on-surface-variant">Kontak</span>
              <?php if ($kontakHref !== ''): ?>
              <a class="font-label-md text-label-md font-medium text-primary-container hover:underline" href="<?= e($kontakHref) ?>"><?= e($kontak) ?></a>
              <?php else: ?>
              <span class="font-label-md text-label-md font-medium text-on-surface"><?= e($kontak) ?></span>
              <?php endif; ?>
            </div>
            <div class="flex flex-col gap-1">
              <span class="font-label-sm text-label-sm text-on-surface-variant">Paket Diminati</span>
              <span class="font-label-md text-label-md font-medium text-on-surface"><?= !empty($inquiry['nama_paket']) ? e($inquiry['nama_paket']) : '—' ?></span>
            </div>
            <div class="flex flex-col gap-1">
              <span class="font-label-sm text-label-sm text-on-surface-variant">Surel</span>
              <?php $email = trim((string) ($inquiry['email'] ?? '')); ?>
              <?php if ($email !== ''): ?>
              <a class="font-label-md text-label-md font-medium text-primary-container hover:underline" href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
              <?php else: ?>
              <span class="font-label-md text-label-md font-medium text-on-surface">—</span>
              <?php endif; ?>
            </div>
            <div class="flex flex-col gap-1">
              <span class="font-label-sm text-label-sm text-on-surface-variant">Tanggal Masuk</span>
              <span class="font-label-md text-label-md font-medium text-on-surface"><?= tanggal($inquiry['created_at'], 'long') ?></span>
            </div>
          </div>

          <div class="flex flex-col gap-1">
            <span class="font-label-sm text-label-sm text-on-surface-variant">Pesan / Kebutuhan Konsultasi</span>
            <div class="bg-surface-container-low rounded p-3 font-body-sm text-body-sm text-on-surface leading-relaxed">
              <?= nl2br(e((string) ($inquiry['pesan'] ?? ''))) ?>
            </div>
          </div>
        </div>
      </section>

      <!-- Kartu Tindak Lanjut -->
      <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
        <div class="px-3 py-2.5 bg-surface-container-low">
          <span class="font-headline-sm text-headline-sm text-primary">Tindak Lanjut</span>
        </div>
        <div class="p-4 flex flex-col gap-4">
          <form class="flex flex-col gap-3" method="post" action="<?= url('admin/inquiry/detail.php?id=' . (int) $inquiry['id']) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" value="status"/>
            <input type="hidden" name="id" value="<?= (int) $inquiry['id'] ?>"/>
            <div class="flex flex-col gap-1.5">
              <label class="font-label-md text-label-md font-medium text-on-surface" for="status_baru">Status Inquiry</label>
              <select class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest cursor-pointer" id="status_baru" name="status_baru">
                <option value="baru"<?= $inquiry['status'] === 'baru' ? ' selected' : '' ?>>Baru — belum ditangani</option>
                <option value="ditindaklanjuti"<?= $inquiry['status'] === 'ditindaklanjuti' ? ' selected' : '' ?>>Ditindaklanjuti — sudah ditangani</option>
              </select>
            </div>
            <button class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm cursor-pointer" type="submit">
              <span class="material-symbols-outlined text-[17px]">save</span>
              Simpan Status
            </button>
          </form>

          <div class="h-px bg-surface-container"></div>

          <form class="flex flex-col gap-2" method="post" action="<?= url('admin/inquiry/detail.php?id=' . (int) $inquiry['id']) ?>" data-confirm="Yakin hapus inquiry dari &quot;<?= e($inquiry['nama']) ?>&quot;? Tindakan ini tidak dapat dibatalkan.">
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" value="hapus"/>
            <input type="hidden" name="id" value="<?= (int) $inquiry['id'] ?>"/>
            <p class="font-label-sm text-label-sm text-on-surface-variant">Menghapus inquiry akan menghapusnya permanen dari daftar.</p>
            <button class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded bg-surface-container-low hover:bg-error/10 text-error font-label-md text-label-md transition-colors shadow-sm cursor-pointer" type="submit">
              <span class="material-symbols-outlined text-[17px]">delete</span>
              Hapus Inquiry
            </button>
          </form>
        </div>
      </section>
    </div>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
