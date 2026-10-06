<?php
/** Edit Hotel — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'hotel';
$metaTitle = 'Edit Hotel — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;
$row = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'simpan') {
        $id = (int) ($_POST['id'] ?? 0);
        $row = model('hotel')->find($id);
        if (!$row) {
            flash_set('error', 'Data tidak ditemukan.');
            redirect('admin/hotel/index.php');
        }
        $errors = validate_hotel($_POST);
        if (!$errors) {
            $up = handle_image_upload($_FILES['gambar'] ?? ['error' => UPLOAD_ERR_NO_FILE], 'hotel');
            if (!$up['ok']) {
                $errors['gambar'] = $up['error'];
            } else {
                $gambar = $row['gambar'];
                if ($up['file']) {
                    delete_upload($row['gambar'], 'hotel');
                    $gambar = $up['file'];
                }
                $nama = trim((string) $_POST['nama']);
                model('hotel')->update($id, [
                    'nama'   => $nama,
                    'kota'   => trim((string) ($_POST['kota'] ?? '')) ?: null,
                    'kelas'  => trim((string) ($_POST['kelas'] ?? '')) ?: null,
                    'gambar' => $gambar,
                ]);
                flash_set('success', 'Hotel "' . $nama . '" berhasil diperbarui.');
                redirect('admin/hotel/index.php');
            }
        }
    }
}

if ($row === null) {
    $id = (int) ($_GET['id'] ?? 0);
    $row = model('hotel')->find($id);
    if (!$row) {
        flash_set('error', 'Data tidak ditemukan.');
        redirect('admin/hotel/index.php');
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
          <a class="text-on-surface-variant hover:text-on-surface" href="<?= url('admin/hotel/index.php') ?>">Hotel &amp; Akomodasi</a>
          <span class="text-outline text-xs">/</span>
          <span class="text-on-surface font-semibold">Edit</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Edit Hotel</h1>
      </div>
      <a class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3.5 py-1.5 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/hotel/index.php') ?>">
        <span class="material-symbols-outlined text-[17px] text-outline">arrow_back</span>
        Kembali ke Daftar
      </a>
    </div>

    <!-- Formulir -->
    <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
      <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
        <span class="font-headline-sm text-headline-sm text-primary">Formulir Hotel</span>
        <span class="font-label-sm text-label-sm text-on-surface-variant">Kolom bertanda * wajib diisi</span>
      </div>
      <form class="p-4 flex flex-col gap-4 max-w-3xl" method="post" action="<?= url('admin/hotel/edit.php') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="simpan"/>
        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>"/>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="nama">Nama Hotel <span class="text-error">*</span></label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest"
                 id="nama" name="nama" type="text" maxlength="150" placeholder="Contoh: Raffles Makkah Palace" value="<?= old_value($old, 'nama', (string) $row['nama']) ?>"/>
          <?= field_error($errors, 'nama') ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="kota">Kota</label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest"
                   id="kota" name="kota" type="text" maxlength="100" placeholder="Makkah / Madinah" value="<?= old_value($old, 'kota', (string) ($row['kota'] ?? '')) ?>"/>
            <?= field_error($errors, 'kota') ?>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="kelas">Kelas</label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest"
                   id="kelas" name="kelas" type="text" maxlength="50" placeholder="Bintang 5" value="<?= old_value($old, 'kelas', (string) ($row['kelas'] ?? '')) ?>"/>
            <?= field_error($errors, 'kelas') ?>
          </div>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="gambar">Ganti Foto Hotel</label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-surface-container file:text-on-surface file:font-label-md file:cursor-pointer"
                 id="gambar" name="gambar" type="file" accept="image/jpeg,image/png,image/webp"/>
          <p class="font-label-sm text-label-sm text-on-surface-variant">Biarkan kosong untuk mempertahankan foto saat ini.</p>
          <?= field_error($errors, 'gambar') ?>
        </div>

        <!-- Foto saat ini -->
        <div class="flex items-center gap-3 bg-surface-container-low rounded p-3">
          <img class="w-14 h-14 rounded bg-surface-container-lowest object-cover" src="<?= e(upload_url($row['gambar'] ?? null, 'hotel')) ?>" alt=""/>
          <div class="flex flex-col">
            <span class="font-label-sm text-label-sm text-on-surface-variant">Foto saat ini</span>
            <span class="font-label-md text-label-md text-on-surface"><?= e(($row['gambar'] ?? '') !== '' ? (string) $row['gambar'] : 'Belum ada foto (placeholder)') ?></span>
          </div>
        </div>

        <div class="flex items-center gap-2 pt-1">
          <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm cursor-pointer" type="submit">
            <span class="material-symbols-outlined text-[17px]">save</span>
            Simpan Perubahan
          </button>
          <a class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/hotel/index.php') ?>">Batal</a>
        </div>
      </form>
    </section>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
