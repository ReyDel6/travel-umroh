<?php
/** Tambah Galeri — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'galeri';
$metaTitle = 'Tambah Foto Galeri — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'simpan') {
        $errors = validate_galeri($_POST);
        if (!$errors) {
            $up = handle_image_upload($_FILES['gambar'] ?? ['error' => UPLOAD_ERR_NO_FILE], 'galeri');
            if (!$up['ok']) {
                $errors['gambar'] = $up['error'];
            } elseif (!$up['file']) {
                $errors['gambar'] = 'Gambar wajib diunggah.';
            } else {
                $judul = trim((string) $_POST['judul']);
                model('galeri')->create([
                    'judul'    => $judul,
                    'gambar'   => $up['file'],
                    'kategori' => trim((string) ($_POST['kategori'] ?? '')) ?: null,
                ]);
                flash_set('success', 'Foto "' . $judul . '" berhasil ditambahkan.');
                redirect('admin/galeri/index.php');
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
          <a class="text-on-surface-variant hover:text-on-surface" href="<?= url('admin/galeri/index.php') ?>">Galeri Dokumentasi</a>
          <span class="text-outline text-xs">/</span>
          <span class="text-on-surface font-semibold">Tambah</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Tambah Foto Galeri</h1>
      </div>
      <a class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3.5 py-1.5 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/galeri/index.php') ?>">
        <span class="material-symbols-outlined text-[17px] text-outline">arrow_back</span>
        Kembali ke Daftar
      </a>
    </div>

    <!-- Formulir -->
    <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
      <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
        <span class="font-headline-sm text-headline-sm text-primary">Formulir Galeri</span>
        <span class="font-label-sm text-label-sm text-on-surface-variant">Kolom bertanda * wajib diisi</span>
      </div>
      <form class="p-4 flex flex-col gap-4 max-w-3xl" method="post" action="<?= url('admin/galeri/create.php') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="simpan"/>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="judul">Judul Foto <span class="text-error">*</span></label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest"
                 id="judul" name="judul" type="text" maxlength="150" placeholder="Contoh: Tawaf Perdana Jamaah" value="<?= old_value($old, 'judul') ?>"/>
          <?= field_error($errors, 'judul') ?>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="kategori">Kategori</label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest"
                 id="kategori" name="kategori" type="text" maxlength="100" placeholder="Contoh: Makkah / Madinah / Manasik" value="<?= old_value($old, 'kategori') ?>"/>
          <p class="font-label-sm text-label-sm text-on-surface-variant">Opsional — dipakai untuk pengelompokan foto di situs publik.</p>
          <?= field_error($errors, 'kategori') ?>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="gambar">Gambar <span class="text-error">*</span></label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-surface-container file:text-on-surface file:font-label-md file:cursor-pointer"
                 id="gambar" name="gambar" type="file" accept="image/jpeg,image/png,image/webp"/>
          <p class="font-label-sm text-label-sm text-on-surface-variant">Format JPG, PNG, atau WebP, maksimal 2MB.</p>
          <?= field_error($errors, 'gambar') ?>
        </div>

        <div class="flex items-center gap-2 pt-1">
          <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm cursor-pointer" type="submit">
            <span class="material-symbols-outlined text-[17px]">save</span>
            Simpan Foto
          </button>
          <a class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/galeri/index.php') ?>">Batal</a>
        </div>
      </form>
    </section>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
