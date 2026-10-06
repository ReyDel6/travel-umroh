<?php
/** SEO Halaman Publik — CMS admin (meta title & meta description per halaman utama). */
require_once dirname(__DIR__) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'meta';
$metaTitle = 'SEO Halaman Publik — CMS ' . APP_NAME;
$errors = [];

// Kosong = gunakan nilai bawaan halaman (fallback di kode public).
$pages = [
    'home'    => ['label' => 'Beranda', 'hint' => 'Halaman pembuka utama situs.'],
    'paket'   => ['label' => 'Paket Umroh & Ibadah', 'hint' => 'Daftar kurasi paket rombongan.'],
    'galeri'  => ['label' => 'Galeri & Catatan Perjalanan', 'hint' => 'Dokumentasi perjalanan jamaah.'],
    'artikel' => ['label' => 'Artikel & Jurnal Manasik', 'hint' => 'Daftar tulisan asatidzah dan tim medis.'],
    'faq'     => ['label' => 'Pertanyaan yang Sering Diajukan', 'hint' => 'Kumpulan jawaban seputar keberangkatan.'],
    'kontak'  => ['label' => 'Kontak & Griya Konsultasi', 'hint' => 'Formulir konsultasi dan kontak pelayanan.'],
    'tentang' => ['label' => 'Tentang Kami', 'hint' => 'Profil lembaga, izin, dan tim pendamping.'],
];

$metaRows = [];
foreach (array_keys($pages) as $kode) {
    $metaRows[$kode] = model('meta')->getByKode($kode) ?? ['kode' => $kode, 'meta_title' => null, 'meta_description' => null];
}
$oldRows = $metaRows;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (($_POST['aksi'] ?? '') !== 'simpan_meta') {
        flash_set('error', 'Permintaan tidak dikenal.');
        redirect('admin/meta.php');
    }
    foreach (array_keys($pages) as $kode) {
        $title = trim((string) ($_POST['meta_title_' . $kode] ?? ''));
        $desc = trim((string) ($_POST['meta_description_' . $kode] ?? ''));
        $oldRows[$kode] = [
            'kode'             => $kode,
            'meta_title'       => $title === '' ? null : $title,
            'meta_description' => $desc === '' ? null : $desc,
        ];
        if (mb_strlen($title) > 160) {
            $errors['meta_title_' . $kode] = 'Maksimal 160 karakter.';
        }
        if (mb_strlen($desc) > 255) {
            $errors['meta_description_' . $kode] = 'Maksimal 255 karakter.';
        }
    }
    if (!$errors) {
        foreach (array_keys($pages) as $kode) {
            model('meta')->update($kode, $oldRows[$kode]['meta_title'], $oldRows[$kode]['meta_description']);
        }
        flash_set('success', 'Meta title & description halaman publik berhasil disimpan.');
        redirect('admin/meta.php');
    }
}

require ADMIN_PATH . '/partials/head.php';
require ADMIN_PATH . '/partials/sidebar.php';
?>
<div class="flex flex-col w-full">
  <div class="flex flex-col gap-5 pt-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div class="flex flex-col gap-1">
        <div class="flex items-center gap-2 font-label-md text-label-md text-on-surface-variant">
          <span class="text-on-surface-variant">CMS Operasional</span>
          <span class="text-outline text-xs">/</span>
          <span class="text-on-surface font-semibold">SEO Halaman Publik</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">SEO Halaman Publik</h1>
        <p class="font-body-sm text-body-sm text-on-surface-variant max-w-2xl">
          Atur meta title &amp; meta description untuk setiap halaman utama. Biarkan kosong agar situs memakai nilai bawaan halaman.
        </p>
      </div>
    </div>

    <?php if ($errors): ?>
    <div class="p-3 rounded bg-error/10 text-error font-body-sm text-body-sm flex items-start gap-2" role="alert">
      <span class="material-symbols-outlined text-[18px] mt-0.5">error</span>
      <span>Periksa kembali isian formulir. Beberapa kolom melebihi batas panjang karakter.</span>
    </div>
    <?php endif; ?>

    <form class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col" method="post" action="<?= url('admin/meta.php') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="aksi" value="simpan_meta"/>
      <div class="flex flex-col divide-y divide-surface-container">
        <?php foreach (array_keys($pages) as $kode): $row = $oldRows[$kode]; ?>
        <div class="p-4 flex flex-col gap-3">
          <div class="flex items-start justify-between gap-3">
            <div class="flex flex-col">
              <span class="font-headline-sm text-headline-sm text-on-surface"><?= e($pages[$kode]['label']) ?></span>
              <span class="font-label-sm text-label-sm text-on-surface-variant"><?= e($pages[$kode]['hint']) ?></span>
            </div>
            <span class="font-label-sm text-label-sm text-outline bg-surface-container-low rounded px-2 py-1">/<?= e($kode) ?></span>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
              <label class="font-label-sm text-label-sm text-on-surface-variant" for="meta_title_<?= e($kode) ?>">Meta Title</label>
              <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="meta_title_<?= e($kode) ?>" name="meta_title_<?= e($kode) ?>" type="text" maxlength="160" placeholder="Judul hasil pencarian (maks. 160 karakter)" value="<?= old_value($oldRows[$kode], 'meta_title', '') ?>"/>
              <?= field_error($errors, 'meta_title_' . $kode) ?>
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="font-label-sm text-label-sm text-on-surface-variant" for="meta_description_<?= e($kode) ?>">Meta Description</label>
              <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="meta_description_<?= e($kode) ?>" name="meta_description_<?= e($kode) ?>" type="text" maxlength="255" placeholder="Ringkasan hasil pencarian (maks. 255 karakter)" value="<?= old_value($oldRows[$kode], 'meta_description', '') ?>"/>
              <?= field_error($errors, 'meta_description_' . $kode) ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="px-4 py-3 bg-surface-container-low border-t border-surface-container flex flex-wrap items-center gap-2">
        <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm cursor-pointer" type="submit">
          <span class="material-symbols-outlined text-[17px]">save</span>
          Simpan Meta SEO
        </button>
        <span class="font-label-sm text-label-sm text-on-surface-variant">Kosongkan kolom untuk kembali ke nilai bawaan.</span>
      </div>
    </form>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>