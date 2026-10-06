<?php
/** Tambah Fasilitas — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'fasilitas';
$metaTitle = 'Tambah Fasilitas — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'simpan') {
        $errors = validate_fasilitas($_POST);
        if (!$errors) {
            $up = handle_image_upload($_FILES['ikon_file'] ?? ['error' => UPLOAD_ERR_NO_FILE], 'fasilitas');
            if (!$up['ok']) {
                $errors['ikon_file'] = $up['error'];
            } else {
                $ikon = $up['file'] ?? null;
                if (!$ikon) {
                    $teks = trim((string) ($_POST['ikon'] ?? ''));
                    $ikon = $teks !== '' ? $teks : null;
                }
                $nama = trim((string) $_POST['nama']);
                $id = model('fasilitas')->create([
                    'nama'      => $nama,
                    'deskripsi' => trim((string) ($_POST['deskripsi'] ?? '')) ?: null,
                    'ikon'      => $ikon,
                ]);
                flash_set('success', 'Fasilitas "' . $nama . '" berhasil ditambahkan.');
                redirect('admin/fasilitas/index.php');
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
          <a class="text-on-surface-variant hover:text-on-surface" href="<?= url('admin/fasilitas/index.php') ?>">Fasilitas</a>
          <span class="text-outline text-xs">/</span>
          <span class="text-on-surface font-semibold">Tambah</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Tambah Fasilitas</h1>
      </div>
      <a class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3.5 py-1.5 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/fasilitas/index.php') ?>">
        <span class="material-symbols-outlined text-[17px] text-outline">arrow_back</span>
        Kembali ke Daftar
      </a>
    </div>

    <!-- Formulir -->
    <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
      <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
        <span class="font-headline-sm text-headline-sm text-primary">Formulir Fasilitas</span>
        <span class="font-label-sm text-label-sm text-on-surface-variant">Kolom bertanda * wajib diisi</span>
      </div>
      <form class="p-4 flex flex-col gap-4 max-w-3xl" method="post" action="<?= url('admin/fasilitas/create.php') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="simpan"/>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="nama">Nama Fasilitas <span class="text-error">*</span></label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest"
                 id="nama" name="nama" type="text" maxlength="100" placeholder="Contoh: Makan Tiga Kali Sehari" value="<?= old_value($old, 'nama') ?>"/>
          <?= field_error($errors, 'nama') ?>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="deskripsi">Deskripsi</label>
          <textarea class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest resize-y min-h-[96px]"
                    id="deskripsi" name="deskripsi" rows="4" placeholder="Penjelasan singkat fasilitas untuk jamaah..."><?= old_value($old, 'deskripsi') ?></textarea>
          <?= field_error($errors, 'deskripsi') ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="ikon">Ikon Material Symbols</label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest"
                   id="ikon" name="ikon" type="text" maxlength="255" placeholder="restaurant / directions_bus" value="<?= old_value($old, 'ikon') ?>"/>
            <p class="font-label-sm text-label-sm text-on-surface-variant">Tulis nama ikon Material Symbols, contoh: <span class="material-symbols-outlined align-text-bottom text-[15px] text-primary-container">restaurant</span> restaurant atau <span class="material-symbols-outlined align-text-bottom text-[15px] text-primary-container">directions_bus</span> directions_bus.</p>
            <?= field_error($errors, 'ikon') ?>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="ikon_file">atau Upload Gambar Ikon</label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-surface-container file:text-on-surface file:font-label-md file:cursor-pointer"
                   id="ikon_file" name="ikon_file" type="file" accept="image/jpeg,image/png,image/webp"/>
            <p class="font-label-sm text-label-sm text-on-surface-variant">Format JPG, PNG, atau WebP, maksimal 2MB. Jika file dipilih, file gambar yang digunakan.</p>
            <?= field_error($errors, 'ikon_file') ?>
          </div>
        </div>

        <div class="flex items-center gap-2 pt-1">
          <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm cursor-pointer" type="submit">
            <span class="material-symbols-outlined text-[17px]">save</span>
            Simpan Fasilitas
          </button>
          <a class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/fasilitas/index.php') ?>">Batal</a>
        </div>
      </form>
    </section>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
