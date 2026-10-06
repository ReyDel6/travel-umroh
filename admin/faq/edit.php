<?php
/** Edit Pertanyaan Umum (FAQ) — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'faq';
$metaTitle = 'Edit Pertanyaan — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;

$id = (int) ($_GET['id'] ?? 0);
$row = $id > 0 ? model('faq')->find($id) : null;
if (!$row) {
    flash_set('error', 'Data tidak ditemukan.');
    redirect('admin/faq/index.php');
}
$old = $_POST ?: $row;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'simpan') {
        $errors = validate_faq($old);
        if (!isset($errors['pertanyaan']) && mb_strlen(trim((string) ($old['pertanyaan'] ?? ''))) > 255) {
            $errors['pertanyaan'] = 'Pertanyaan maksimal 255 karakter.';
        }
        if (!$errors) {
            model('faq')->update($id, [
                'pertanyaan' => trim((string) ($old['pertanyaan'] ?? '')),
                'jawaban'    => trim((string) ($old['jawaban'] ?? '')),
                'urutan'     => (int) ($old['urutan'] ?? 0),
            ]);
            flash_set('success', 'Pertanyaan berhasil diperbarui.');
            redirect('admin/faq/index.php');
        }
    }
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
          <a class="text-on-surface-variant hover:text-primary transition-colors" href="<?= url('admin/faq/index.php') ?>">Pertanyaan Umum</a>
          <span class="text-outline text-xs">/</span>
          <span class="text-on-surface font-semibold">Edit Pertanyaan</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Edit Pertanyaan</h1>
      </div>
      <a class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3.5 py-1.5 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/faq/index.php') ?>">
        <span class="material-symbols-outlined text-[17px]">arrow_back</span>
        Kembali ke Daftar
      </a>
    </div>

    <?php if ($errors): ?>
    <div class="p-3 rounded bg-error/10 text-error font-body-sm text-body-sm flex items-start gap-2" role="alert">
      <span class="material-symbols-outlined text-[18px] mt-0.5">error</span>
      <span>Periksa kembali isian formulir. Kolom bertanda wajib belum lengkap atau belum valid.</span>
    </div>
    <?php endif; ?>

    <!-- Formulir Edit FAQ -->
    <form class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col" method="post" action="<?= url('admin/faq/edit.php?id=' . $id) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="aksi" value="simpan"/>
      <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
        <span class="font-headline-sm text-headline-sm text-primary">Formulir Pertanyaan</span>
        <span class="font-label-sm text-label-sm text-on-surface-variant">ID #<?= (int) $row['id'] ?></span>
      </div>
      <div class="p-4 flex flex-col gap-4">
        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="pertanyaan">Pertanyaan <span class="text-error">*</span></label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="pertanyaan" name="pertanyaan" type="text" maxlength="255" placeholder="Contoh: Berapa biaya pendaftaran umroh?" value="<?= old_value($old, 'pertanyaan') ?>"/>
          <?= field_error($errors, 'pertanyaan') ?>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="jawaban">Jawaban <span class="text-error">*</span></label>
          <textarea class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest min-h-[180px] leading-relaxed" id="jawaban" name="jawaban" rows="7" placeholder="Tulis jawaban lengkap di sini. Pisahkan paragraf dengan baris kosong."><?= old_value($old, 'jawaban') ?></textarea>
          <?= field_error($errors, 'jawaban') ?>
        </div>

        <div class="flex flex-col gap-1.5 max-w-xs">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="urutan">Urutan Tampil</label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="urutan" name="urutan" type="number" min="0" step="1" value="<?= old_value($old, 'urutan', '0') ?>"/>
          <p class="font-label-sm text-label-sm text-on-surface-variant">Angka lebih kecil tampil lebih dahulu. Gunakan 0 bila tidak ada prioritas.</p>
          <?= field_error($errors, 'urutan') ?>
        </div>
      </div>

      <div class="px-4 py-3 bg-surface-container-low border-t border-surface-container flex flex-wrap items-center gap-2">
        <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm cursor-pointer" type="submit">
          <span class="material-symbols-outlined text-[17px]">save</span>
          Simpan Perubahan
        </button>
        <a class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-surface-container-lowest hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/faq/index.php') ?>">Batal</a>
      </div>
    </form>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
