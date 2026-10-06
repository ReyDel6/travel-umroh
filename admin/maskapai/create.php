<?php
/** Tambah Maskapai — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'maskapai';
$metaTitle = 'Tambah Maskapai — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'simpan') {
        $errors = validate_maskapai($_POST);
        if (!$errors) {
            $up = handle_image_upload($_FILES['logo'] ?? ['error' => UPLOAD_ERR_NO_FILE], 'maskapai');
            if (!$up['ok']) {
                $errors['logo'] = $up['error'];
            } else {
                $nama = trim((string) $_POST['nama']);
                model('maskapai')->create([
                    'nama' => $nama,
                    'logo' => $up['file'],
                ]);
                flash_set('success', 'Maskapai "' . $nama . '" berhasil ditambahkan.');
                redirect('admin/maskapai/index.php');
            }
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
          <a class="text-on-surface-variant hover:text-on-surface" href="<?= url('admin/maskapai/index.php') ?>">Maskapai</a>
          <span class="text-outline text-xs">/</span>
          <span class="text-on-surface font-semibold">Tambah</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Tambah Maskapai</h1>
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
      <form class="p-4 flex flex-col gap-4 max-w-3xl" method="post" action="<?= url('admin/maskapai/create.php') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="simpan"/>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="nama">Nama Maskapai <span class="text-error">*</span></label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest"
                 id="nama" name="nama" type="text" maxlength="100" placeholder="Contoh: Saudia" value="<?= old_value($old, 'nama') ?>"/>
          <?= field_error($errors, 'nama') ?>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="logo">Logo Maskapai</label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-surface-container file:text-on-surface file:font-label-md file:cursor-pointer"
                 id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp"/>
          <p class="font-label-sm text-label-sm text-on-surface-variant">Format JPG, PNG, atau WebP, maksimal 2MB. Opsional — bila kosong akan memakai ikon default.</p>
          <?= field_error($errors, 'logo') ?>
        </div>

        <div class="flex items-center gap-2 pt-1">
          <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm cursor-pointer" type="submit">
            <span class="material-symbols-outlined text-[17px]">save</span>
            Simpan Maskapai
          </button>
          <a class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/maskapai/index.php') ?>">Batal</a>
        </div>
      </form>
    </section>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
