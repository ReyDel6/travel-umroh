<?php
/** Edit Testimoni Jamaah — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'testimoni';
$metaTitle = 'Edit Testimoni — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;

$id = (int) ($_GET['id'] ?? 0);
$item = $id > 0 ? model('testimoni')->find($id) : null;
if (!$item) {
    flash_set('error', 'Data tidak ditemukan.');
    redirect('admin/testimoni/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'simpan') {
        $nama = trim((string) ($old['nama'] ?? ''));
        $teks = trim((string) ($old['teks'] ?? ''));
        $asalKota = trim((string) ($old['asal_kota'] ?? ''));
        $urutan = trim((string) ($old['urutan'] ?? ''));
        if ($nama === '') {
            $errors['nama'] = 'Nama wajib diisi.';
        } elseif (mb_strlen($nama) > 120) {
            $errors['nama'] = 'Nama maksimal 120 karakter.';
        }
        if (mb_strlen($asalKota) > 100) {
            $errors['asal_kota'] = 'Asal kota maksimal 100 karakter.';
        }
        if ($teks === '') {
            $errors['teks'] = 'Teks testimoni wajib diisi.';
        }
        if ($urutan !== '' && !ctype_digit($urutan)) {
            $errors['urutan'] = 'Urutan harus berupa angka.';
        }
        if (!$errors) {
            model('testimoni')->update($id, [
                'nama'      => $nama,
                'asal_kota' => $asalKota !== '' ? $asalKota : null,
                'teks'      => $teks,
                'urutan'    => $urutan !== '' ? (int) $urutan : 0,
            ]);
            flash_set('success', 'Testimoni dari "' . e($nama) . '" berhasil diperbarui.');
            redirect('admin/testimoni/index.php');
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
          <a class="text-on-surface-variant hover:text-primary transition-colors" href="<?= url('admin/testimoni/index.php') ?>">Testimoni</a>
          <span class="text-outline text-xs">/</span>
          <span class="text-on-surface font-semibold">Edit</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Edit Testimoni</h1>
      </div>
      <a class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3.5 py-1.5 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/testimoni/index.php') ?>">
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

    <!-- Formulir Testimoni -->
    <form class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col" method="post" action="<?= url('admin/testimoni/edit.php?id=' . (int) $item['id']) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="aksi" value="simpan"/>
      <div class="px-3 py-2.5 bg-surface-container-low">
        <span class="font-headline-sm text-headline-sm text-primary">Formulir Testimoni</span>
      </div>
      <div class="p-4 flex flex-col gap-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="nama">Nama Jamaah <span class="text-error">*</span></label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="nama" name="nama" type="text" maxlength="120" placeholder="Contoh: Ibu Siti Rahma" value="<?= old_value($old, 'nama', (string) $item['nama']) ?>"/>
            <?= field_error($errors, 'nama') ?>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="asal_kota">Asal Kota</label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="asal_kota" name="asal_kota" type="text" maxlength="100" placeholder="Contoh: Jakarta" value="<?= old_value($old, 'asal_kota', (string) ($item['asal_kota'] ?? '')) ?>"/>
            <?= field_error($errors, 'asal_kota') ?>
          </div>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="teks">Teks Testimoni <span class="text-error">*</span></label>
          <textarea class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest min-h-[150px] leading-relaxed" id="teks" name="teks" rows="6" placeholder="Cerita pengalaman jamaah selama perjalanan..."><?= old_value($old, 'teks', (string) $item['teks']) ?></textarea>
          <?= field_error($errors, 'teks') ?>
        </div>

        <div class="flex flex-col gap-1.5 max-w-xs">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="urutan">Urutan Tampil</label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="urutan" name="urutan" type="number" min="0" step="1" value="<?= old_value($old, 'urutan', (string) (int) $item['urutan']) ?>"/>
          <p class="font-label-sm text-label-sm text-on-surface-variant">Angka lebih kecil tampil lebih dahulu. Gunakan 0 bila tidak ada prioritas.</p>
          <?= field_error($errors, 'urutan') ?>
        </div>
      </div>

      <div class="px-4 py-3 bg-surface-container-low border-t border-surface-container flex flex-wrap items-center gap-2">
        <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm cursor-pointer" type="submit">
          <span class="material-symbols-outlined text-[17px]">save</span>
          Simpan Perubahan
        </button>
        <a class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-surface-container-lowest hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/testimoni/index.php') ?>">Batal</a>
      </div>
    </form>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>