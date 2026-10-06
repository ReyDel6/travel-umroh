<?php
/** Edit Maskapai — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'maskapai';
$metaTitle = 'Edit Maskapai — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;
$row = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'simpan') {
        $id = (int) ($_POST['id'] ?? 0);
        $row = model('maskapai')->find($id);
        if (!$row) {
            flash_set('error', 'Data tidak ditemukan.');
            redirect('admin/maskapai/index.php');
        }
        $errors = validate_maskapai($_POST);
        if (!$errors) {
            $up = handle_image_upload($_FILES['logo'] ?? ['error' => UPLOAD_ERR_NO_FILE], 'maskapai');
            if (!$up['ok']) {
                $errors['logo'] = $up['error'];
            } else {
                $logo = $row['logo'];
                if ($up['file']) {
                    delete_upload($row['logo'], 'maskapai');
                    $logo = $up['file'];
                }
                $nama = trim((string) $_POST['nama']);
                model('maskapai')->update($id, [
                    'nama' => $nama,
                    'logo' => $logo,
                ]);
                flash_set('success', 'Maskapai "' . $nama . '" berhasil diperbarui.');
                redirect('admin/maskapai/index.php');
            }
        }
    }
}

if ($row === null) {
    $id = (int) ($_GET['id'] ?? 0);
    $row = model('maskapai')->find($id);
    if (!$row) {
        flash_set('error', 'Data tidak ditemukan.');
        redirect('admin/maskapai/index.php');
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
          <a class="text-on-surface-variant hover:text-on-surface" href="<?= url('admin/maskapai/index.php') ?>">Maskapai</a>
          <span class="text-outline text-xs">/</span>
          <span class="text-on-surface font-semibold">Edit</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Edit Maskapai</h1>
      </div>
      <a class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3.5 py-1.5 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/maskapai/index.php') ?>">
        <span class="material-symbols-outlined text-[17px] text-outline">arrow_back</span>
        Kembali ke Daftar
      </a>
    </div>

    <!-- Formulir -->
    <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
      <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
        <span class="font-headline-sm text-headline-sm text-primary">Formulir Maskapai</span>
        <span class="font-label-sm text-label-sm text-on-surface-variant">Kolom bertanda * wajib diisi</span>
      </div>
      <form class="p-4 flex flex-col gap-4 max-w-3xl" method="post" action="<?= url('admin/maskapai/edit.php') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="simpan"/>
        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>"/>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="nama">Nama Maskapai <span class="text-error">*</span></label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest"
                 id="nama" name="nama" type="text" maxlength="100" placeholder="Contoh: Saudia" value="<?= old_value($old, 'nama', (string) $row['nama']) ?>"/>
          <?= field_error($errors, 'nama') ?>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="logo">Ganti Logo Maskapai</label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-surface-container file:text-on-surface file:font-label-md file:cursor-pointer"
                 id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp"/>
          <p class="font-label-sm text-label-sm text-on-surface-variant">Biarkan kosong untuk mempertahankan logo saat ini.</p>
          <?= field_error($errors, 'logo') ?>
        </div>

        <!-- Logo saat ini -->
        <div class="flex items-center gap-3 bg-surface-container-low rounded p-3">
          <?php if (!empty($row['logo'])): ?>
          <img class="w-14 h-14 rounded bg-surface-container-lowest object-cover" src="<?= e(upload_url($row['logo'], 'maskapai')) ?>" alt="Logo maskapai"/>
          <?php else: ?>
          <span class="w-14 h-14 inline-flex items-center justify-center rounded bg-surface-container-lowest">
            <span class="material-symbols-outlined text-[26px] text-outline">flight</span>
          </span>
          <?php endif; ?>
          <div class="flex flex-col">
            <span class="font-label-sm text-label-sm text-on-surface-variant">Logo saat ini</span>
            <span class="font-label-md text-label-md text-on-surface"><?= e(($row['logo'] ?? '') !== '' ? (string) $row['logo'] : 'Belum ada logo') ?></span>
          </div>
        </div>

        <div class="flex items-center gap-2 pt-1">
          <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm cursor-pointer" type="submit">
            <span class="material-symbols-outlined text-[17px]">save</span>
            Simpan Perubahan
          </button>
          <a class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/maskapai/index.php') ?>">Batal</a>
        </div>
      </form>
    </section>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
