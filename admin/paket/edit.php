<?php
/** Edit Paket Umroh — CMS admin */
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'paket';
$metaTitle = 'Edit Paket Umroh — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;

$id = (int) ($_GET['id'] ?? 0);
$paket = $id > 0 ? model('paket')->find($id) : null;
if (!$paket) {
    flash_set('error', 'Data tidak ditemukan.');
    redirect('admin/paket/index.php');
}

$idList = static function (string $key): array {
    $v = $_POST[$key] ?? [];
    return is_array($v) ? array_map('intval', $v) : [];
};

$readItineraryPosts = static function (): array {
    $keys = array_values(array_filter(
        (array) ($_POST['itinerary_key'] ?? []),
        static fn($v) => is_string($v) || is_int($v)
    ));
    $hari = array_values((array) ($_POST['itinerary_hari'] ?? []));
    $judul = array_values((array) ($_POST['itinerary_judul'] ?? []));
    $deskripsi = array_values((array) ($_POST['itinerary_deskripsi'] ?? []));
    $tag = array_values((array) ($_POST['itinerary_tag'] ?? []));
    $highlight = (array) ($_POST['itinerary_highlight'] ?? []);
    $rows = [];
    foreach ($hari as $i => $rawHari) {
        $k = (int) ($keys[$i] ?? $i);
        $judul_i = trim((string) ($judul[$i] ?? ''));
        $deskripsi_i = trim((string) ($deskripsi[$i] ?? ''));
        if ($judul_i === '' && $deskripsi_i === '') {
            continue;
        }
        $rows[] = [
            'hari'         => trim((string) $rawHari),
            'judul'        => $judul_i,
            'deskripsi'    => $deskripsi_i,
            'tag'          => trim((string) ($tag[$i] ?? '')),
            'is_highlight' => !empty($highlight[$k]) ? 1 : 0,
            'urutan'       => $i,
        ];
    }
    return $rows;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'simpan') {
        $errors = validate_paket($_POST);
        if (!isset($errors['durasi_hari'])
            && (!isset($_POST['durasi_hari']) || $_POST['durasi_hari'] === '' || !ctype_digit((string) $_POST['durasi_hari']))) {
            $errors['durasi_hari'] = 'Durasi hari wajib diisi dengan angka.';
        }

        $hargaInput = [
            'quad'   => trim((string) ($_POST['harga_quad'] ?? '')),
            'triple' => trim((string) ($_POST['harga_triple'] ?? '')),
            'double' => trim((string) ($_POST['harga_double'] ?? '')),
        ];
        if ($hargaInput['quad'] === '' && $hargaInput['triple'] === '' && $hargaInput['double'] === '') {
            $errors['harga'] = 'Minimal satu harga tipe kamar wajib diisi.';
        } else {
            foreach ($hargaInput as $tipe => $nilai) {
                if ($nilai !== '' && !preg_match('/^[0-9.,\s]+$/', $nilai)) {
                    $errors['harga_' . $tipe] = 'Harga tipe kamar ' . $tipe . ' harus berupa angka yang valid.';
                }
            }
        }

        // Upload hanya jika seluruh validasi lolos, agar tak ada file yatim
        $up = ['ok' => true, 'file' => null, 'error' => null];
        if (!$errors) {
            $up = handle_image_upload($_FILES['thumbnail'] ?? ['error' => UPLOAD_ERR_NO_FILE], 'paket');
            if (!$up['ok']) {
                $errors['thumbnail'] = (string) $up['error'];
            }
        }

        if (!$errors) {
            $nama = trim((string) ($_POST['nama'] ?? ''));
            $slugInput = trim((string) ($_POST['slug'] ?? ''));
            $slug = model('paket')->uniqueSlug($slugInput !== '' ? slugify($slugInput) : slugify($nama), $id);
            $thumbnailBaru = $up['file'] ?? null;
            $thumbnailLama = $paket['thumbnail'] ?? null;

            model('paket')->update($id, [
                'nama'             => $nama,
                'slug'             => $slug,
                'deskripsi'        => trim((string) ($_POST['deskripsi'] ?? '')),
                'durasi_hari'      => (int) ($_POST['durasi_hari'] ?? 0),
                'thumbnail'        => $thumbnailBaru ?? $thumbnailLama,
                'status'           => ($_POST['status'] ?? 'aktif') === 'nonaktif' ? 'nonaktif' : 'aktif',
                'meta_title'       => trim((string) ($_POST['meta_title'] ?? '')) ?: null,
                'meta_description' => trim((string) ($_POST['meta_description'] ?? '')) ?: null,
            ]);

            if ($thumbnailBaru !== null && $thumbnailLama !== null && $thumbnailBaru !== $thumbnailLama) {
                delete_upload($thumbnailLama, 'paket');
            }

            $hargaRows = [];
            foreach ($hargaInput as $tipe => $nilai) {
                if ($nilai !== '') {
                    $hargaRows[] = ['tipe_kamar' => $tipe, 'harga' => $nilai];
                }
            }
            model('paket')->syncHarga($id, $hargaRows);
            model('paket')->syncFasilitas($id, $idList('fasilitas'));
            model('paket')->syncHotels($id, $idList('hotel'));
            model('paket')->syncMaskapai($id, $idList('maskapai'));
            model('paket')->syncItinerary($id, $readItineraryPosts());

            flash_set('success', 'Paket "' . $nama . '" berhasil diperbarui.');
            redirect('admin/paket/index.php');
        }
    }
}

$fasilitasList = model('fasilitas')->getAll();
$hotelList = model('hotel')->getAll();
$maskapaiList = model('maskapai')->getAll();

$hargaLama = ['quad' => '', 'triple' => '', 'double' => ''];
foreach (model('paket')->getHarga($id) as $h) {
    $tipe = (string) $h['tipe_kamar'];
    if (isset($hargaLama[$tipe])) {
        $hargaLama[$tipe] = (string) (int) (float) $h['harga'];
    }
}

// Saat POST gagal validasi, pertahankan persis isian user (termasuk kotak yang dilepas semua);
// saat GET, tampilkan keadaan tersimpan di database.
$isPostSimpan = $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'simpan';
$checkedFasilitas = $isPostSimpan
    ? (isset($old['fasilitas']) && is_array($old['fasilitas']) ? array_map('intval', $old['fasilitas']) : [])
    : model('paket')->getFasilitasIds($id);
$checkedHotel = $isPostSimpan
    ? (isset($old['hotel']) && is_array($old['hotel']) ? array_map('intval', $old['hotel']) : [])
    : model('paket')->getHotelIds($id);
$checkedMaskapai = $isPostSimpan
    ? (isset($old['maskapai']) && is_array($old['maskapai']) ? array_map('intval', $old['maskapai']) : [])
    : model('paket')->getMaskapaiIds($id);

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
          <a class="text-on-surface-variant hover:text-on-surface" href="<?= url('admin/paket/index.php') ?>">Paket Umroh</a>
          <span class="text-outline text-xs">/</span>
          <span class="text-on-surface font-semibold">Edit</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Edit Paket Umroh</h1>
      </div>
      <a class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3.5 py-1.5 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/paket/index.php') ?>">
        <span class="material-symbols-outlined text-[17px] text-outline">arrow_back</span>
        Kembali ke Daftar
      </a>
    </div>

    <form class="flex flex-col gap-5" method="post" action="<?= url('admin/paket/edit.php?id=' . (int) $paket['id']) ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="aksi" value="simpan"/>

      <!-- Informasi Dasar -->
      <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
          <span class="font-headline-sm text-headline-sm text-primary">Informasi Dasar</span>
          <span class="font-label-sm text-label-sm text-on-surface-variant">Kolom bertanda * wajib diisi</span>
        </div>
        <div class="p-4 flex flex-col gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="nama">Nama Paket <span class="text-error">*</span></label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest focus:outline-none"
                   id="nama" name="nama" type="text" maxlength="150" placeholder="Contoh: Umroh Reguler 12 Hari" value="<?= old_value($old, 'nama', (string) $paket['nama']) ?>"/>
            <?= field_error($errors, 'nama') ?>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex flex-col gap-1.5 md:col-span-2">
              <label class="font-label-md text-label-md font-medium text-on-surface" for="slug">Slug URL (SEO)</label>
              <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest focus:outline-none"
                     id="slug" name="slug" type="text" maxlength="170" placeholder="Kosongkan untuk dibuat otomatis dari nama paket" value="<?= old_value($old, 'slug', (string) $paket['slug']) ?>"/>
              <p class="font-label-sm text-label-sm text-on-surface-variant">Slug boleh diubah manual untuk kebutuhan SEO. Bila kosong, sistem membuatkan dari nama paket.</p>
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="font-label-md text-label-md font-medium text-on-surface" for="durasi_hari">Durasi (Hari) <span class="text-error">*</span></label>
              <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest focus:outline-none"
                     id="durasi_hari" name="durasi_hari" type="number" min="1" step="1" placeholder="12" value="<?= old_value($old, 'durasi_hari', (string) $paket['durasi_hari']) ?>"/>
              <?= field_error($errors, 'durasi_hari') ?>
            </div>
          </div>

          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="deskripsi">Deskripsi Paket <span class="text-error">*</span></label>
            <textarea class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest focus:outline-none resize-y min-h-[120px]"
                      id="deskripsi" name="deskripsi" rows="5" placeholder="Gambaran umum itinerary, keunggulan, dan catatan paket..."><?= old_value($old, 'deskripsi', (string) $paket['deskripsi']) ?></textarea>
            <?= field_error($errors, 'deskripsi') ?>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
              <label class="font-label-md text-label-md font-medium text-on-surface" for="thumbnail">Thumbnail Paket</label>
              <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest focus:outline-none file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-surface-container file:text-on-surface file:font-label-md file:cursor-pointer"
                     id="thumbnail" name="thumbnail" type="file" accept="image/jpeg,image/png,image/webp"/>
              <?php if (!empty($paket['thumbnail'])): ?>
              <div class="flex items-center gap-3 mt-1">
                <img class="w-16 h-16 rounded object-cover bg-surface-container-low border border-surface-container" src="<?= e(upload_url($paket['thumbnail'], 'paket')) ?>" alt="Thumbnail saat ini"/>
                <p class="font-label-sm text-label-sm text-on-surface-variant">Thumbnail saat ini. Pilih file baru bila ingin mengganti.</p>
              </div>
              <?php else: ?>
              <p class="font-label-sm text-label-sm text-on-surface-variant">Belum ada thumbnail. Format JPG, PNG, atau WebP, maksimal 2MB.</p>
              <?php endif; ?>
              <?= field_error($errors, 'thumbnail') ?>
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="font-label-md text-label-md font-medium text-on-surface" for="status">Status Publikasi</label>
              <select class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest focus:outline-none"
                      id="status" name="status">
                <option value="aktif" <?= old_value($old, 'status', (string) $paket['status']) === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="nonaktif" <?= old_value($old, 'status', (string) $paket['status']) === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
              </select>
              <?= field_error($errors, 'status') ?>
            </div>
          </div>
        </div>
      </section>

      <!-- Harga per Tipe Kamar -->
      <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
          <span class="font-headline-sm text-headline-sm text-primary">Harga per Tipe Kamar</span>
          <span class="font-label-sm text-label-sm text-on-surface-variant">Minimal satu tipe wajib diisi</span>
        </div>
        <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="harga_quad">Harga Quad (Rp)</label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest focus:outline-none tabular-nums"
                   id="harga_quad" name="harga_quad" type="number" min="0" step="1" placeholder="28500000" value="<?= old_value($old, 'harga_quad', $hargaLama['quad']) ?>"/>
            <?= field_error($errors, 'harga_quad') ?>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="harga_triple">Harga Triple (Rp)</label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest focus:outline-none tabular-nums"
                   id="harga_triple" name="harga_triple" type="number" min="0" step="1" placeholder="31500000" value="<?= old_value($old, 'harga_triple', $hargaLama['triple']) ?>"/>
            <?= field_error($errors, 'harga_triple') ?>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="harga_double">Harga Double (Rp)</label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest focus:outline-none tabular-nums"
                   id="harga_double" name="harga_double" type="number" min="0" step="1" placeholder="34500000" value="<?= old_value($old, 'harga_double', $hargaLama['double']) ?>"/>
            <?= field_error($errors, 'harga_double') ?>
          </div>
        </div>
        <?php if (isset($errors['harga'])): ?>
        <div class="px-4 pb-4 -mt-2"><?= field_error($errors, 'harga') ?></div>
        <?php endif; ?>
      </section>

      <!-- Fasilitas -->
      <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
          <span class="font-headline-sm text-headline-sm text-primary">Fasilitas Paket</span>
          <span class="font-label-sm text-label-sm text-on-surface-variant"><?= count($checkedFasilitas) ?> dipilih dari <?= count($fasilitasList) ?> data</span>
        </div>
        <div class="p-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
          <?php if (!$fasilitasList): ?>
          <p class="col-span-full font-body-sm text-body-sm text-on-surface-variant">Belum ada data fasilitas. Tambahkan melalui menu Fasilitas.</p>
          <?php endif; ?>
          <?php foreach ($fasilitasList as $f): ?>
          <label class="flex items-center gap-2 px-3 py-2 rounded bg-surface-container-low hover:bg-surface-container cursor-pointer transition-colors">
            <input class="w-4 h-4 accent-[#1F5C56] cursor-pointer" type="checkbox" name="fasilitas[]" value="<?= (int) $f['id'] ?>"<?= in_array((int) $f['id'], $checkedFasilitas, true) ? ' checked' : '' ?>/>
            <span class="font-body-sm text-body-sm text-on-surface truncate"><?= e($f['nama']) ?></span>
          </label>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- Hotel -->
      <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
          <span class="font-headline-sm text-headline-sm text-primary">Hotel Akomodasi</span>
          <span class="font-label-sm text-label-sm text-on-surface-variant"><?= count($checkedHotel) ?> dipilih dari <?= count($hotelList) ?> data</span>
        </div>
        <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
          <?php if (!$hotelList): ?>
          <p class="col-span-full font-body-sm text-body-sm text-on-surface-variant">Belum ada data hotel. Tambahkan melalui menu Hotel &amp; Akomodasi.</p>
          <?php endif; ?>
          <?php foreach ($hotelList as $h): ?>
          <label class="flex items-center gap-2 px-3 py-2 rounded bg-surface-container-low hover:bg-surface-container cursor-pointer transition-colors">
            <input class="w-4 h-4 accent-[#1F5C56] cursor-pointer" type="checkbox" name="hotel[]" value="<?= (int) $h['id'] ?>"<?= in_array((int) $h['id'], $checkedHotel, true) ? ' checked' : '' ?>/>
            <span class="font-body-sm text-body-sm text-on-surface truncate"><?= e($h['nama']) ?><?= !empty($h['kota']) ? ' <span class="text-on-surface-variant">· ' . e($h['kota']) . '</span>' : '' ?></span>
          </label>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- Maskapai -->
      <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
          <span class="font-headline-sm text-headline-sm text-primary">Maskapai Penerbangan</span>
          <span class="font-label-sm text-label-sm text-on-surface-variant"><?= count($checkedMaskapai) ?> dipilih dari <?= count($maskapaiList) ?> data</span>
        </div>
        <div class="p-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
          <?php if (!$maskapaiList): ?>
          <p class="col-span-full font-body-sm text-body-sm text-on-surface-variant">Belum ada data maskapai. Tambahkan melalui menu Maskapai.</p>
          <?php endif; ?>
          <?php foreach ($maskapaiList as $m): ?>
          <label class="flex items-center gap-2 px-3 py-2 rounded bg-surface-container-low hover:bg-surface-container cursor-pointer transition-colors">
            <input class="w-4 h-4 accent-[#1F5C56] cursor-pointer" type="checkbox" name="maskapai[]" value="<?= (int) $m['id'] ?>"<?= in_array((int) $m['id'], $checkedMaskapai, true) ? ' checked' : '' ?>/>
            <span class="font-body-sm text-body-sm text-on-surface truncate"><?= e($m['nama']) ?></span>
          </label>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- Itinerary / Rangkaian Harian -->
      <?php $itineraryList = $isPostSimpan ? $readItineraryPosts() : model('paket')->getItinerary($id); ?>
      <section id="itinerary-section" class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
          <span class="font-headline-sm text-headline-sm text-primary">Itinerary / Rangkaian Harian</span>
          <button class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md transition-colors cursor-pointer" type="button" id="itinerary-tambah">
            <span class="material-symbols-outlined text-[16px]">add</span>
            Tambah Baris
          </button>
        </div>
        <div class="p-4 flex flex-col gap-5">
          <?php if (!$itineraryList): ?>
          <p id="itinerary-kosong" class="font-body-sm text-body-sm text-on-surface-variant">Belum ada rangkaian harian. Tambahkan baris untuk menampilkan itinerary di halaman paket.</p>
          <?php endif; ?>
          <div class="flex flex-col gap-4">
            <?php foreach ($itineraryList as $k => $it): ?>
            <div class="itinerary-row border border-surface-container rounded p-3 bg-surface-container-low/30 flex flex-col gap-3">
              <input type="hidden" name="itinerary_key[]" value="<?= (int) $k ?>"/>
              <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">route</span>
                <input class="flex-1 min-w-0 bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" type="text" name="itinerary_hari[]" placeholder="Hari ke-1" maxlength="80" value="<?= e((string) ($it['hari'] ?? '')) ?>"/>
                <input class="w-44 bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" type="text" name="itinerary_tag[]" placeholder="Tag puncak (opsional)" maxlength="80" value="<?= e((string) ($it['tag'] ?? '')) ?>"/>
                <label class="flex items-center gap-1.5 font-label-sm text-label-sm text-on-surface-variant shrink-0 cursor-pointer">
                  <input class="w-4 h-4 accent-[#1F5C56] cursor-pointer" type="checkbox" name="itinerary_highlight[<?= (int) $k ?>]" value="1"<?= !empty($it['is_highlight']) ? ' checked' : '' ?>/>
                  Puncak Ibadah
                </label>
                <button class="itinerary-hapus font-label-md text-outline hover:text-error transition-colors cursor-pointer shrink-0" type="button" aria-label="Hapus baris">Hapus</button>
              </div>
              <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" type="text" name="itinerary_judul[]" placeholder="Judul kegiatan hari ini" maxlength="200" value="<?= e((string) ($it['judul'] ?? '')) ?>"/>
              <textarea class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest resize-y min-h-[70px]" name="itinerary_deskripsi[]" rows="3" placeholder="Rangkaian kegiatan pada hari tersebut"><?= e((string) ($it['deskripsi'] ?? '')) ?></textarea>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <template id="itinerary-template">
        <div class="itinerary-row border border-surface-container rounded p-3 bg-surface-container-low/30 flex flex-col gap-3">
          <input type="hidden" name="itinerary_key[]" value="NEXT-KEY"/>
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px] text-on-surface-variant">route</span>
            <input class="flex-1 min-w-0 bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" type="text" name="itinerary_hari[]" placeholder="Hari ke-1" maxlength="80" value=""/>
            <input class="w-44 bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" type="text" name="itinerary_tag[]" placeholder="Tag puncak (opsional)" maxlength="80" value=""/>
            <label class="flex items-center gap-1.5 font-label-sm text-label-sm text-on-surface-variant shrink-0 cursor-pointer">
              <input class="w-4 h-4 accent-[#1F5C56] cursor-pointer" type="checkbox" name="itinerary_highlight[NEXT-KEY]" value="1"/>
              Puncak Ibadah
            </label>
            <button class="itinerary-hapus font-label-md text-outline hover:text-error transition-colors cursor-pointer shrink-0" type="button" aria-label="Hapus baris">Hapus</button>
          </div>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" type="text" name="itinerary_judul[]" placeholder="Judul kegiatan hari ini" maxlength="200" value=""/>
          <textarea class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest resize-y min-h-[70px]" name="itinerary_deskripsi[]" rows="3" placeholder="Rangkaian kegiatan pada hari tersebut"></textarea>
        </div>
      </template>

      <!-- SEO -->
      <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
          <span class="font-headline-sm text-headline-sm text-primary">Optimasi Mesin Pencari (SEO)</span>
          <span class="font-label-sm text-label-sm text-on-surface-variant">Opsional</span>
        </div>
        <div class="p-4 grid grid-cols-1 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="meta_title">Meta Title</label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest focus:outline-none"
                   id="meta_title" name="meta_title" type="text" maxlength="160" placeholder="Judul halaman di hasil pencarian" value="<?= old_value($old, 'meta_title', (string) ($paket['meta_title'] ?? '')) ?>"/>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="meta_description">Meta Description</label>
            <textarea class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest focus:outline-none resize-y min-h-[80px]"
                      id="meta_description" name="meta_description" rows="3" maxlength="255" placeholder="Ringkasan singkat untuk hasil pencarian"><?= old_value($old, 'meta_description', (string) ($paket['meta_description'] ?? '')) ?></textarea>
          </div>
        </div>
      </section>

      <!-- Aksi -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-surface-container-lowest px-4 py-3 rounded shadow-sm">
        <span class="font-label-sm text-label-sm text-on-surface-variant">Perubahan langsung diterapkan pada katalog publik setelah disimpan.</span>
        <div class="flex items-center gap-2">
          <a class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors shadow-sm" href="<?= url('admin/paket/index.php') ?>">Batal</a>
          <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm cursor-pointer" type="submit">
            <span class="material-symbols-outlined text-[17px]">save</span>
            Simpan Perubahan
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
  (function () {
    var wrap = document.getElementById('itinerary-section');
    if (!wrap) return;
    var tmpl = document.getElementById('itinerary-template');
    var kosong = document.getElementById('itinerary-kosong');
    var tambah = document.getElementById('itinerary-tambah');
    var container = wrap.querySelector('.itinerary-row') ? wrap.querySelector('.flex.flex-col.gap-4') : null;
    function refresh() {
      var rows = wrap.querySelectorAll('.itinerary-row');
      if (kosong) kosong.style.display = rows.length ? 'none' : 'block';
    }
    function addRow() {
      if (!tmpl || !tmpl.content) return;
      if (!container) {
        container = document.createElement('div');
        container.className = 'flex flex-col gap-4';
        var parent = wrap.querySelector('.p-4.flex.flex-col.gap-5');
        if (parent) parent.appendChild(container);
      }
      var node = document.importNode(tmpl.content, true);
      var nextKey = 0;
      wrap.querySelectorAll('.itinerary-row input[name="itinerary_key[]"]').forEach(function (hk) {
        var v = parseInt(hk.value, 10);
        if (!isNaN(v) && v >= nextKey) nextKey = v + 1;
      });
      node.querySelectorAll('[name="itinerary_key[]"], [name="itinerary_highlight[NEXT-KEY]"]').forEach(function (el) {
        var name = el.getAttribute('name');
        el.setAttribute('name', name.replace(/NEXT-KEY/g, String(nextKey)));
        if (name.indexOf('itinerary_key') === 0) el.value = String(nextKey);
      });
      container.appendChild(node);
      refresh();
      var inp = container.lastChild.querySelector ? container.lastChild.querySelector('input[name="itinerary_hari[]"]') : null;
      if (inp) inp.focus();
    }
    if (tambah) tambah.addEventListener('click', addRow);
    wrap.addEventListener('click', function (e) {
      var btn = e.target.closest('.itinerary-hapus');
      if (!btn) return;
      var row = btn.closest('.itinerary-row');
      if (!row) return;
      row.parentNode.removeChild(row);
      refresh();
    });
    refresh();
  })();
</script>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
