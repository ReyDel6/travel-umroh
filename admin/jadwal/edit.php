<?php
/** Edit Jadwal Keberangkatan — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'jadwal';
$metaTitle = 'Edit Jadwal Keberangkatan — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;

$id = (int) ($_GET['id'] ?? 0);
$jadwal = $id > 0 ? model('jadwal')->find($id) : null;
if (!$jadwal) {
    flash_set('error', 'Data tidak ditemukan.');
    redirect('admin/jadwal/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'simpan') {
        $errors = validate_jadwal($_POST);
        if (!$errors) {
            model('jadwal')->update($id, [
                'paket_id'          => (int) $_POST['paket_id'],
                'tanggal_berangkat' => (string) $_POST['tanggal_berangkat'],
                'kuota'             => (int) $_POST['kuota'],
                'status'            => in_array($_POST['status'] ?? '', ['dibuka', 'penuh', 'ditutup'], true) ? $_POST['status'] : 'dibuka',
            ]);
            flash_set('success', 'Jadwal keberangkatan berhasil diperbarui.');
            redirect('admin/jadwal/index.php');
        }
    }
}

$paketList = model('paket')->getAll();

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
          <a class="text-on-surface-variant hover:text-on-surface" href="<?= url('admin/jadwal/index.php') ?>">Jadwal Keberangkatan</a>
          <span class="text-outline text-xs">/</span>
          <span class="text-on-surface font-semibold">Edit</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Edit Jadwal Keberangkatan</h1>
      </div>
      <a class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3.5 py-1.5 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/jadwal/index.php') ?>">
        <span class="material-symbols-outlined text-[17px] text-outline">arrow_back</span>
        Kembali ke Daftar
      </a>
    </div>

    <!-- Formulir -->
    <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
      <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
        <span class="font-headline-sm text-headline-sm text-primary">Formulir Jadwal</span>
        <span class="font-label-sm text-label-sm text-on-surface-variant">Kolom bertanda * wajib diisi</span>
      </div>
      <form class="p-4 flex flex-col gap-4 max-w-3xl" method="post" action="<?= url('admin/jadwal/edit.php?id=' . (int) $jadwal['id']) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="simpan"/>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="paket_id">Paket Umroh <span class="text-error">*</span></label>
          <select class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest focus:outline-none"
                  id="paket_id" name="paket_id">
            <option value="">— Pilih Paket —</option>
            <?php foreach ($paketList as $p): ?>
            <option value="<?= (int) $p['id'] ?>"<?= old_value($old, 'paket_id', (string) $jadwal['paket_id']) === (string) $p['id'] ? ' selected' : '' ?>>
              <?= e($p['nama']) ?> (<?= (int) $p['durasi_hari'] ?> Hari)
            </option>
            <?php endforeach; ?>
          </select>
          <?php if (!$paketList): ?>
          <p class="font-label-sm text-label-sm text-on-surface-variant">Belum ada paket terdaftar. Tambahkan paket terlebih dahulu.</p>
          <?php endif; ?>
          <?= field_error($errors, 'paket_id') ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="tanggal_berangkat">Tanggal Berangkat <span class="text-error">*</span></label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest focus:outline-none"
                   id="tanggal_berangkat" name="tanggal_berangkat" type="date" value="<?= old_value($old, 'tanggal_berangkat', (string) $jadwal['tanggal_berangkat']) ?>"/>
            <?= field_error($errors, 'tanggal_berangkat') ?>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="kuota">Kuota (Pax) <span class="text-error">*</span></label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest focus:outline-none tabular-nums"
                   id="kuota" name="kuota" type="number" min="0" step="1" placeholder="45" value="<?= old_value($old, 'kuota', (string) $jadwal['kuota']) ?>"/>
            <?= field_error($errors, 'kuota') ?>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="status">Status</label>
            <select class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest focus:outline-none"
                    id="status" name="status">
              <?php $statusNow = old_value($old, 'status', (string) $jadwal['status']); ?>
              <option value="dibuka" <?= $statusNow === 'dibuka' ? 'selected' : '' ?>>Dibuka</option>
              <option value="penuh" <?= $statusNow === 'penuh' ? 'selected' : '' ?>>Penuh</option>
              <option value="ditutup" <?= $statusNow === 'ditutup' ? 'selected' : '' ?>>Ditutup</option>
            </select>
            <?= field_error($errors, 'status') ?>
          </div>
        </div>

        <div class="flex items-center gap-2 pt-1">
          <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm cursor-pointer" type="submit">
            <span class="material-symbols-outlined text-[17px]">save</span>
            Simpan Perubahan
          </button>
          <a class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/jadwal/index.php') ?>">Batal</a>
        </div>
      </form>
    </section>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
