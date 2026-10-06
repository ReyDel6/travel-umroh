<?php
/** Edit Artikel — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'artikel';
$metaTitle = 'Edit Artikel — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;

$id = (int) ($_GET['id'] ?? 0);
$row = $id > 0 ? model('artikel')->find($id) : null;
if (!$row) {
    flash_set('error', 'Data tidak ditemukan.');
    redirect('admin/artikel/index.php');
}
$old = $_POST ?: $row;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'simpan') {
        $errors = validate_artikel($old);

        $thumbnail = (string) ($row['thumbnail'] ?? '');
        if (!$errors) {
            $up = handle_image_upload($_FILES['thumbnail'] ?? ['error' => UPLOAD_ERR_NO_FILE], 'artikel');
            if (!$up['ok']) {
                $errors['thumbnail'] = $up['error'];
            } elseif ($up['file'] !== null) {
                $thumbnail = $up['file'];
            }
        }

        if (!$errors) {
            $judul = trim((string) ($old['judul'] ?? ''));
            $slugInput = trim((string) ($old['slug'] ?? ''));
            $slug = slugify($slugInput !== '' ? $slugInput : $judul);
            $slug = model('artikel')->uniqueSlug($slug, $id);
            $status = in_array($old['status'] ?? '', ['publish', 'draft'], true) ? $old['status'] : 'draft';

            model('artikel')->update($id, [
                'judul'            => $judul,
                'slug'             => $slug,
                'konten'           => trim((string) ($old['konten'] ?? '')),
                'thumbnail'        => $thumbnail ?: null,
                'status'           => $status,
                'meta_title'       => trim((string) ($old['meta_title'] ?? '')) ?: null,
                'meta_description' => trim((string) ($old['meta_description'] ?? '')) ?: null,
            ]);

            $lama = (string) ($row['thumbnail'] ?? '');
            if ($lama !== '' && $lama !== $thumbnail) {
                delete_upload($lama, 'artikel');
            }

            flash_set('success', 'Artikel "' . $judul . '" berhasil diperbarui.');
            redirect('admin/artikel/index.php');
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
          <a class="text-on-surface-variant hover:text-primary transition-colors" href="<?= url('admin/artikel/index.php') ?>">Artikel &amp; Jurnal</a>
          <span class="text-outline text-xs">/</span>
          <span class="text-on-surface font-semibold">Edit Artikel</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Edit Artikel</h1>
      </div>
      <a class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3.5 py-1.5 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/artikel/index.php') ?>">
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

    <!-- Formulir Edit Artikel -->
    <form class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col" method="post" action="<?= url('admin/artikel/edit.php?id=' . $id) ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="aksi" value="simpan"/>
      <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
        <span class="font-headline-sm text-headline-sm text-primary">Formulir Artikel</span>
        <span class="font-label-sm text-label-sm text-on-surface-variant">Dibuat <?= tanggal($row['created_at']) ?></span>
      </div>
      <div class="p-4 flex flex-col gap-4">
        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="judul">Judul Artikel <span class="text-error">*</span></label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="judul" name="judul" type="text" maxlength="200" placeholder="Contoh: Persiapan Batin Sebelum Berangkat Umroh" value="<?= old_value($old, 'judul') ?>"/>
          <?= field_error($errors, 'judul') ?>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="slug">Slug URL (opsional)</label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="slug" name="slug" type="text" maxlength="220" placeholder="persiapan-batin-sebelum-umroh" value="<?= old_value($old, 'slug') ?>"/>
          <p class="font-label-sm text-label-sm text-on-surface-variant">Kosongkan untuk menghasilkan slug otomatis dari judul. Isi manual bila ingin penyesuaian SEO.</p>
          <?= field_error($errors, 'slug') ?>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="konten">Konten <span class="text-error">*</span></label>
          <textarea class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest min-h-[320px] leading-relaxed" id="konten" name="konten" rows="14" placeholder="Tulis isi artikel di sini. Pisahkan paragraf dengan baris kosong."><?= old_value($old, 'konten') ?></textarea>
          <p class="font-label-sm text-label-sm text-on-surface-variant">Teks polos; setiap baris baru akan ditampilkan sesuai format saat halaman publik dirender.</p>
          <?= field_error($errors, 'konten') ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="thumbnail">Gambar Sampul</label>
            <?php $thumbSekarang = (string) ($row['thumbnail'] ?? ''); ?>
            <?php if ($thumbSekarang !== ''): ?>
            <div class="flex items-center gap-3">
              <img class="w-20 h-14 rounded bg-surface-container-low object-cover shadow-sm" src="<?= e(upload_url($thumbSekarang, 'artikel')) ?>" alt="Sampul saat ini"/>
              <span class="font-label-sm text-label-sm text-on-surface-variant">Sampul saat ini. Pilih file baru untuk menggantinya.</span>
            </div>
            <?php endif; ?>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest file:mr-3 file:rounded file:border-0 file:bg-surface-container file:px-3 file:py-1.5 file:font-label-md file:text-label-md file:text-on-surface" id="thumbnail" name="thumbnail" type="file" accept="image/jpeg,image/png,image/webp"/>
            <p class="font-label-sm text-label-sm text-on-surface-variant">Format JPG, PNG, atau WebP. Maksimal 2MB.</p>
            <?= field_error($errors, 'thumbnail') ?>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="status">Status Publikasi</label>
            <select class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest cursor-pointer" id="status" name="status">
              <option value="draft"<?= old_value($old, 'status', 'draft') === 'draft' ? ' selected' : '' ?>>Draft — belum tayang</option>
              <option value="publish"<?= old_value($old, 'status') === 'publish' ? ' selected' : '' ?>>Publish — tayang di situs</option>
            </select>
            <?= field_error($errors, 'status') ?>
          </div>
        </div>
      </div>

      <!-- Pengaturan SEO -->
      <div class="px-3 py-2.5 bg-surface-container-low border-t border-surface-container">
        <span class="font-headline-sm text-headline-sm text-primary">Pengaturan SEO</span>
      </div>
      <div class="p-4 flex flex-col gap-4">
        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="meta_title">Meta Title</label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="meta_title" name="meta_title" type="text" maxlength="160" placeholder="Judul hasil pencarian (maks. 160 karakter)" value="<?= old_value($old, 'meta_title') ?>"/>
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="meta_description">Meta Description</label>
          <textarea class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest min-h-[80px]" id="meta_description" name="meta_description" rows="2" maxlength="255" placeholder="Ringkasan hasil pencarian (maks. 255 karakter)"><?= old_value($old, 'meta_description') ?></textarea>
        </div>
      </div>

      <div class="px-4 py-3 bg-surface-container-low border-t border-surface-container flex flex-wrap items-center gap-2">
        <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm cursor-pointer" type="submit">
          <span class="material-symbols-outlined text-[17px]">save</span>
          Simpan Perubahan
        </button>
        <a class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-surface-container-lowest hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/artikel/index.php') ?>">Batal</a>
      </div>
    </form>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
