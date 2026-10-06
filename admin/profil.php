<?php
/** Profil Perusahaan & Manajemen Akun Admin — CMS admin */
require_once dirname(__DIR__) . '/app/bootstrap.php';
requireLogin();

$activeMenu = 'profil';
$metaTitle = 'Profil & Pengaturan — CMS ' . APP_NAME;
$errors = [];
$old = $_POST;

$profil = model('profil')->get();
$admins = model('admin')->getAll();
$oldProfil = $profil;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = $_POST['aksi'] ?? '';

    /* ---- A. Profil Perusahaan ---- */
    if ($aksi === 'simpan_profil') {
        $opt = static fn (string $v): ?string => $v === '' ? null : $v;
        $data = [
            'nama_perusahaan'      => trim((string) ($_POST['nama_perusahaan'] ?? '')),
            'tagline'              => $opt(trim((string) ($_POST['tagline'] ?? ''))),
            'tentang_kami'         => $opt(trim((string) ($_POST['tentang_kami'] ?? ''))),
            'alamat'               => $opt(trim((string) ($_POST['alamat'] ?? ''))),
            'telepon'              => $opt(trim((string) ($_POST['telepon'] ?? ''))),
            'email'                => $opt(trim((string) ($_POST['email'] ?? ''))),
            'jam_operasional'      => $opt(trim((string) ($_POST['jam_operasional'] ?? ''))),
            'maps_embed'           => $opt(trim((string) ($_POST['maps_embed'] ?? ''))),
            'meta_title'           => $opt(trim((string) ($_POST['meta_title'] ?? ''))),
            'meta_description'     => $opt(trim((string) ($_POST['meta_description'] ?? ''))),
            'izin_ppiu'            => $opt(trim((string) ($_POST['izin_ppiu'] ?? ''))),
            'tahun_berdiri'        => trim((string) ($_POST['tahun_berdiri'] ?? '')),
            'stat_grup_maks'       => trim((string) ($_POST['stat_grup_maks'] ?? '')),
            'stat_rasio_pembimbing'=> trim((string) ($_POST['stat_rasio_pembimbing'] ?? '')),
            'stat_sesi_manasik'    => trim((string) ($_POST['stat_sesi_manasik'] ?? '')),
        ];
        foreach (['tahun_berdiri', 'stat_grup_maks', 'stat_rasio_pembimbing', 'stat_sesi_manasik'] as $numKey) {
            if ($data[$numKey] !== '' && !ctype_digit((string) $data[$numKey])) {
                $errors[$numKey] = 'Isian harus berupa angka bulat.';
            }
        }
        $errors = array_merge(validate_profil($data), $errors);
        if (!$errors) {
            model('profil')->save($data);
            flash_set('success', 'Profil perusahaan berhasil disimpan.');
            redirect('admin/profil.php');
        }
        $oldProfil = $data;
    }

    /* ---- B1. Tambah akun admin ---- */
    if ($aksi === 'tambah_admin') {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $terdaftar = false;
        foreach ($admins as $a) {
            if (strcasecmp((string) $a['username'], $username) === 0) {
                $terdaftar = true;
                break;
            }
        }
        if ($username === '') {
            $errors['username'] = 'Username wajib diisi.';
        } elseif (mb_strlen($username) > 50) {
            $errors['username'] = 'Username maksimal 50 karakter.';
        } elseif ($terdaftar) {
            $errors['username'] = 'Username sudah digunakan oleh akun lain.';
        }
        if (mb_strlen($password) < 6) {
            $errors['password'] = 'Kata sandi minimal 6 karakter.';
        }
        if (!$errors) {
            model('admin')->create($username, $password);
            flash_set('success', 'Akun admin "' . $username . '" berhasil ditambahkan.');
            redirect('admin/profil.php');
        }
    }

    /* ---- B2. Ganti sandi akun (wajib konfirmasi kata sandi yang sedang login) ---- */
    if ($aksi === 'ganti_sandi') {
        $id = (int) ($_POST['id'] ?? 0);
        $password = (string) ($_POST['password'] ?? '');
        $passwordSekarang = (string) ($_POST['password_sekarang'] ?? '');
        $target = null;
        foreach ($admins as $a) {
            if ((int) $a['id'] === $id) {
                $target = $a;
                break;
            }
        }
        if (!$target) {
            flash_set('error', 'Data tidak ditemukan.');
            redirect('admin/profil.php');
        }
        $hashLogin = model('admin')->getPasswordHash((int) ($_SESSION['admin_id'] ?? 0));
        if ($passwordSekarang === '' || !$hashLogin || !password_verify($passwordSekarang, $hashLogin)) {
            flash_set('error', 'Kata sandi Anda saat ini salah. Perubahan tidak disimpan.');
            redirect('admin/profil.php');
        }
        if (mb_strlen($password) < 6) {
            flash_set('error', 'Kata sandi minimal 6 karakter. Perubahan tidak disimpan.');
            redirect('admin/profil.php');
        }
        model('admin')->updatePassword($id, $password);
        flash_set('success', 'Kata sandi akun "' . $target['username'] . '" berhasil diperbarui.');
        redirect('admin/profil.php');
    }

    /* ---- B3. Hapus akun admin ---- */
    if ($aksi === 'hapus_admin') {
        $id = (int) ($_POST['id'] ?? 0);
        $target = null;
        foreach ($admins as $a) {
            if ((int) $a['id'] === $id) {
                $target = $a;
                break;
            }
        }
        if (!$target) {
            flash_set('error', 'Data tidak ditemukan.');
            redirect('admin/profil.php');
        }
        $sendiri = (int) $target['id'] === (int) ($_SESSION['admin_id'] ?? 0)
            || (string) $target['username'] === (string) ($_SESSION['admin_username'] ?? '');
        if ($sendiri) {
            flash_set('error', 'Anda tidak dapat menghapus akun yang sedang digunakan untuk masuk.');
            redirect('admin/profil.php');
        }
        model('admin')->delete($id);
        flash_set('success', 'Akun admin "' . $target['username'] . '" berhasil dihapus.');
        redirect('admin/profil.php');
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
          <span class="text-on-surface font-semibold">Profil &amp; Pengaturan</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary">Profil &amp; Pengaturan Sistem</h1>
      </div>
    </div>

    <?php if ($errors): ?>
    <div class="p-3 rounded bg-error/10 text-error font-body-sm text-body-sm flex items-start gap-2" role="alert">
      <span class="material-symbols-outlined text-[18px] mt-0.5">error</span>
      <span>Periksa kembali isian formulir. Kolom bertanda wajib belum lengkap atau belum valid.</span>
    </div>
    <?php endif; ?>

    <!-- Bagian A: Profil Perusahaan -->
    <form class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col" method="post" action="<?= url('admin/profil.php') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="aksi" value="simpan_profil"/>
      <div class="px-3 py-2.5 bg-surface-container-low">
        <span class="font-headline-sm text-headline-sm text-primary">Profil Perusahaan</span>
      </div>
      <div class="p-4 flex flex-col gap-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="nama_perusahaan">Nama Perusahaan <span class="text-error">*</span></label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="nama_perusahaan" name="nama_perusahaan" type="text" maxlength="150" value="<?= old_value($oldProfil, 'nama_perusahaan') ?>"/>
            <?= field_error($errors, 'nama_perusahaan') ?>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="tagline">Tagline</label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="tagline" name="tagline" type="text" maxlength="150" placeholder="Perjalanan Spiritual yang Teduh" value="<?= old_value($oldProfil, 'tagline') ?>"/>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="telepon">Telepon</label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="telepon" name="telepon" type="text" maxlength="60" placeholder="021 555 0188" value="<?= old_value($oldProfil, 'telepon') ?>"/>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="email">Surel</label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="email" name="email" type="email" maxlength="100" placeholder="halo@sakinahjourneys.id" value="<?= old_value($oldProfil, 'email') ?>"/>
            <?= field_error($errors, 'email') ?>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="jam_operasional">Jam Operasional</label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="jam_operasional" name="jam_operasional" type="text" maxlength="150" placeholder="Senin–Sabtu, 08.00–17.00 WIB" value="<?= old_value($oldProfil, 'jam_operasional') ?>"/>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="maps_embed">Embed Peta (Google Maps)</label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="maps_embed" name="maps_embed" type="text" maxlength="255" placeholder="https://www.google.com/maps/embed?pb=..." value="<?= old_value($oldProfil, 'maps_embed') ?>"/>
          </div>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="tentang_kami">Tentang Kami</label>
          <textarea class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest min-h-[160px] leading-relaxed" id="tentang_kami" name="tentang_kami" rows="6" placeholder="Narasi profil perusahaan. Pisahkan paragraf dengan baris kosong."><?= old_value($oldProfil, 'tentang_kami') ?></textarea>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="alamat">Alamat</label>
          <textarea class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest min-h-[90px]" id="alamat" name="alamat" rows="3" placeholder="Alamat kantor / griya konsultasi"><?= old_value($oldProfil, 'alamat') ?></textarea>
        </div>

        <div class="h-px bg-surface-container"></div>

        <div class="flex flex-col gap-4">
          <span class="font-label-md text-label-md font-medium text-on-surface">Izin Resmi &amp; Statistik (satu sumber data untuk seluruh halaman publik)</span>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md font-medium text-on-surface" for="izin_ppiu">Izin PPIU (Kemenag)</label>
            <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="izin_ppiu" name="izin_ppiu" type="text" maxlength="150" placeholder="Contoh: PPIU No. U.412 Tahun 2021 — Akreditasi A" value="<?= old_value($oldProfil, 'izin_ppiu') ?>"/>
            <p class="font-label-sm text-label-sm text-on-surface-variant">Ditampilkan di halaman beranda, footer, dan paket.</p>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="flex flex-col gap-1.5">
              <label class="font-label-sm text-label-sm text-on-surface-variant" for="tahun_berdiri">Tahun Berdiri</label>
              <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="tahun_berdiri" name="tahun_berdiri" type="number" min="1900" max="2100" step="1" placeholder="2021" value="<?= old_value($oldProfil, 'tahun_berdiri') ?>"/>
              <?= field_error($errors, 'tahun_berdiri') ?>
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="font-label-sm text-label-sm text-on-surface-variant" for="stat_grup_maks">Maks Jamaah/Grup</label>
              <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="stat_grup_maks" name="stat_grup_maks" type="number" min="1" step="1" placeholder="24" value="<?= old_value($oldProfil, 'stat_grup_maks') ?>"/>
              <?= field_error($errors, 'stat_grup_maks') ?>
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="font-label-sm text-label-sm text-on-surface-variant" for="stat_rasio_pembimbing">Rasio Pembimbing (1:N)</label>
              <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="stat_rasio_pembimbing" name="stat_rasio_pembimbing" type="number" min="1" step="1" placeholder="15" value="<?= old_value($oldProfil, 'stat_rasio_pembimbing') ?>"/>
              <?= field_error($errors, 'stat_rasio_pembimbing') ?>
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="font-label-sm text-label-sm text-on-surface-variant" for="stat_sesi_manasik">Sesi Manasik Wajib</label>
              <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="stat_sesi_manasik" name="stat_sesi_manasik" type="number" min="1" step="1" placeholder="3" value="<?= old_value($oldProfil, 'stat_sesi_manasik') ?>"/>
              <?= field_error($errors, 'stat_sesi_manasik') ?>
            </div>
          </div>
        </div>

        <div class="h-px bg-surface-container"></div>

        <div class="h-px bg-surface-container"></div>

        <div class="flex flex-col gap-1.5">
          <span class="font-label-md text-label-md font-medium text-on-surface">Pengaturan SEO</span>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
              <label class="font-label-sm text-label-sm text-on-surface-variant" for="meta_title">Meta Title</label>
              <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="meta_title" name="meta_title" type="text" maxlength="160" placeholder="Judul hasil pencarian (maks. 160 karakter)" value="<?= old_value($oldProfil, 'meta_title') ?>"/>
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="font-label-sm text-label-sm text-on-surface-variant" for="meta_description">Meta Description</label>
              <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="meta_description" name="meta_description" type="text" maxlength="255" placeholder="Ringkasan hasil pencarian (maks. 255 karakter)" value="<?= old_value($oldProfil, 'meta_description') ?>"/>
            </div>
          </div>
        </div>
      </div>

      <div class="px-4 py-3 bg-surface-container-low border-t border-surface-container flex flex-wrap items-center gap-2">
        <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm cursor-pointer" type="submit">
          <span class="material-symbols-outlined text-[17px]">save</span>
          Simpan Profil
        </button>
        <span class="font-label-sm text-label-sm text-on-surface-variant">Perubahan langsung diterapkan pada halaman publik.</span>
      </div>
    </form>

    <!-- Bagian B: Manajemen Akun Admin -->
    <section class="bg-surface-container-lowest rounded shadow-sm overflow-hidden flex flex-col">
      <div class="flex items-center justify-between px-3 py-2.5 bg-surface-container-low">
        <span class="font-headline-sm text-headline-sm text-primary">Manajemen Akun Admin</span>
        <span class="font-label-sm text-label-sm text-on-surface-variant"><?= count($admins) ?> akun terdaftar</span>
      </div>

      <div class="w-full overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm tracking-normal select-none">
              <th class="py-2.5 px-3 font-semibold w-16" scope="col">ID</th>
              <th class="py-2.5 px-3 font-semibold min-w-[160px]" scope="col">Username</th>
              <th class="py-2.5 px-3 font-semibold whitespace-nowrap" scope="col">Terdaftar</th>
              <th class="py-2.5 px-3 font-semibold min-w-[300px]" scope="col">Ganti Kata Sandi</th>
              <th class="py-2.5 px-3 font-semibold text-right" scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y-0 text-on-surface font-body-sm text-body-sm">
            <?php if (!$admins): ?>
            <tr><td class="py-6 px-3 text-center text-on-surface-variant" colspan="5">Belum ada akun admin terdaftar.</td></tr>
            <?php endif; ?>
            <?php foreach ($admins as $i => $a): $isSelf = (string) $a['username'] === (string) ($_SESSION['admin_username'] ?? ''); ?>
            <tr class="<?= $i % 2 ? 'bg-surface-container-low/20 ' : '' ?>hover:bg-surface-container-low/60 transition-colors">
              <td class="py-2.5 px-3 font-label-md text-label-md font-semibold text-primary-container tabular-nums">#<?= (int) $a['id'] ?></td>
              <td class="py-2.5 px-3">
                <div class="flex flex-col">
                  <span class="font-medium text-on-surface"><?= e($a['username']) ?></span>
                  <?php if ($isSelf): ?>
                  <span class="font-label-sm text-label-sm text-on-surface-variant">Akun yang sedang digunakan</span>
                  <?php endif; ?>
                </div>
              </td>
              <td class="py-2.5 px-3 whitespace-nowrap font-label-md text-label-md text-on-surface-variant"><?= tanggal($a['created_at']) ?></td>
              <td class="py-2.5 px-3">
                <form class="flex items-center gap-2" method="post" action="<?= url('admin/profil.php') ?>">
                  <?= csrf_field() ?>
                  <input type="hidden" name="aksi" value="ganti_sandi"/>
                  <input type="hidden" name="id" value="<?= (int) $a['id'] ?>"/>
                  <input class="w-36 bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" type="password" name="password_sekarang" minlength="6" placeholder="Kata sandi Anda saat ini" autocomplete="current-password" required/>
                  <input class="w-44 bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" type="password" name="password" minlength="6" placeholder="Kata sandi baru" autocomplete="new-password"/>
                  <button class="shrink-0 bg-surface-container hover:bg-surface-container-high text-on-surface rounded px-3 py-2 font-label-md text-label-md shadow-sm transition-colors cursor-pointer" type="submit">Perbarui</button>
                </form>
              </td>
              <td class="py-2.5 px-3 text-right">
                <?php if ($isSelf): ?>
                <span class="font-label-sm text-label-sm text-on-surface-variant">—</span>
                <?php else: ?>
                <form method="post" action="<?= url('admin/profil.php') ?>" data-confirm="Yakin hapus akun admin &quot;<?= e($a['username']) ?>&quot;?">
                  <?= csrf_field() ?>
                  <input type="hidden" name="aksi" value="hapus_admin"/>
                  <input type="hidden" name="id" value="<?= (int) $a['id'] ?>"/>
                  <button class="font-label-md text-outline hover:text-error transition-colors cursor-pointer" type="submit">Hapus</button>
                </form>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Tambah Akun -->
      <div class="px-3 py-2.5 bg-surface-container-low border-t border-surface-container">
        <span class="font-headline-sm text-headline-sm text-primary">Tambah Akun Admin</span>
      </div>
      <form class="p-4 flex flex-col md:flex-row md:items-end gap-4" method="post" action="<?= url('admin/profil.php') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="tambah_admin"/>
        <div class="flex flex-col gap-1.5 flex-1 min-w-0">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="username">Username <span class="text-error">*</span></label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="username" name="username" type="text" maxlength="50" autocomplete="off" placeholder="username akun admin" value="<?= old_value($old, 'username') ?>"/>
          <?= field_error($errors, 'username') ?>
        </div>
        <div class="flex flex-col gap-1.5 flex-1 min-w-0">
          <label class="font-label-md text-label-md font-medium text-on-surface" for="password">Kata Sandi <span class="text-error">*</span></label>
          <input class="w-full bg-surface-container-low rounded px-3 py-2 font-body-sm text-body-sm text-on-surface outline-none focus:bg-surface-container-lowest" id="password" name="password" type="password" minlength="6" autocomplete="new-password" placeholder="Minimal 6 karakter"/>
          <?= field_error($errors, 'password') ?>
        </div>
        <button class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary transition-colors shadow-sm cursor-pointer shrink-0" type="submit">
          <span class="material-symbols-outlined text-[17px]">person_add</span>
          Tambah Akun
        </button>
      </form>
    </section>
  </div>
</div>
<?php require ADMIN_PATH . '/partials/footer.php'; ?>
