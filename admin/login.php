<?php
/** Halaman masuk CMS admin — design: admin/login.html */
require_once dirname(__DIR__) . '/app/bootstrap.php';

if (is_logged_in()) {
    redirect('admin/dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'login') {
    verify_csrf();
    $identity = trim((string) ($_POST['identity'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    // Throttling sederhana: 5 kegagalan berturut-turut -> kunci 5 menit.
    $lockedUntil = (int) ($_SESSION['login_locked_until'] ?? 0);
    $attempts    = (int) ($_SESSION['login_attempts'] ?? 0);

    if ($lockedUntil > time()) {
        $sisa = $lockedUntil - time();
        $errors['form'] = 'Terlalu banyak percobaan gagal. Coba lagi dalam ' . ceil($sisa / 60) . ' menit.';
    } elseif ($identity === '' || $password === '') {
        $errors['form'] = 'Identitas pengguna dan kata sandi wajib diisi.';
    } elseif (!try_login(db(), $identity, $password)) {
        $attempts++;
        if ($attempts >= 5) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['login_locked_until'] = time() + 300;
            $errors['form'] = 'Terlalu banyak percobaan gagal. Akun dikunci sementara selama 5 menit.';
        } else {
            $_SESSION['login_attempts'] = $attempts;
            $errors['form'] = 'Identitas pengguna atau kata sandi salah. Periksa kembali kredensial Anda.';
        }
    } else {
        unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
        flash_set('success', 'Selamat bertugas kembali, ' . $identity . '.');
        redirect('admin/dashboard.php');
    }
}

$metaTitle = 'Portal Masuk CMS — ' . APP_NAME;
$bodyClass = 'bg-surface font-body-md text-body-md text-on-surface min-h-screen flex flex-col justify-between selection:bg-primary-fixed selection:text-on-primary-fixed';
require ADMIN_PATH . '/partials/head.php';
?>
<header class="w-full py-space-md px-margin lg:px-margin-desktop flex items-center justify-between">
  <div class="flex items-center gap-space-xs">
    <span class="material-symbols-outlined text-primary text-[20px]">admin_panel_settings</span>
    <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface">CMS Console</span>
  </div>
  <div class="flex items-center gap-space-xs text-on-surface-variant">
    <span class="material-symbols-outlined text-[16px]">lock</span>
    <span class="font-label-sm text-label-sm uppercase tracking-wider">Operational Environment</span>
  </div>
</header>
<main class="flex-1 flex items-center justify-center w-full px-margin py-space-xl">
  <div class="flex flex-col w-full items-center justify-center">
    <div class="w-full max-w-[440px] flex flex-col items-center">
      <?php if (($flashLogin = flash_get()) !== null): ?>
      <div class="w-full mb-space-sm p-space-sm rounded font-body-sm text-body-sm flex items-start gap-2 <?= $flashLogin['type'] === 'error' ? 'bg-error-container text-on-error-container' : 'bg-primary-fixed text-on-primary-fixed-variant' ?>" role="status">
        <span class="material-symbols-outlined text-[18px] mt-0.5"><?= $flashLogin['type'] === 'error' ? 'error' : 'check_circle' ?></span>
        <span><?= e($flashLogin['message']) ?></span>
      </div>
      <?php endif; ?>
      <div class="w-full bg-surface-container-lowest rounded-xl shadow-sm p-space-lg md:p-space-xl flex flex-col gap-space-lg">
        <div class="flex flex-col gap-space-xs">
          <div class="flex items-center justify-between">
            <span class="font-headline-sm text-headline-sm text-on-surface tracking-tight font-medium"><?= e(APP_NAME) ?></span>
            <span class="bg-surface-container px-space-xs py-0.5 rounded text-on-surface-variant font-label-sm text-label-sm">Portal Manajemen &amp; CMS Operasional</span>
          </div>
          <p class="font-body-sm text-body-sm text-on-surface-variant">Sistem Informasi Manajemen Jamaah • Divisi Operasional</p>
          <div class="mt-space-xs inline-flex items-center gap-1.5 py-1 px-2.5 rounded bg-surface-container-high w-fit">
            <span class="material-symbols-outlined text-[14px] text-primary">lock</span>
            <span class="font-label-sm text-label-sm text-on-surface-variant">Internal Staff Only • v2.4.0 Secure Access</span>
          </div>
        </div>

        <div class="flex flex-col gap-space-sm">
          <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider font-semibold">Masuk sebagai</span>
          <div class="grid grid-cols-2 gap-space-sm" id="roleCards" role="radiogroup" aria-label="Pilih peran login">
            <button aria-pressed="false" class="role-card flex flex-col items-start gap-0.5 text-left bg-surface-container-low border border-outline-variant/40 hover:bg-surface-container-high rounded-lg p-3 transition-colors cursor-pointer" data-identity="admin" type="button">
              <span class="material-symbols-outlined text-secondary text-[20px]">admin_panel_settings</span>
              <span class="font-label-md text-label-md text-on-surface font-semibold">Admin</span>
              <span class="font-body-sm text-body-sm text-on-surface-variant">Isi ID pengguna &bull; admin</span>
            </button>
            <button aria-pressed="false" class="role-card flex flex-col items-start gap-0.5 text-left bg-surface-container-low border border-outline-variant/40 hover:bg-surface-container-high rounded-lg p-3 transition-colors cursor-pointer" data-identity="staff" type="button">
              <span class="material-symbols-outlined text-secondary text-[20px]">badge</span>
              <span class="font-label-md text-label-md text-on-surface font-semibold">Staff</span>
              <span class="font-body-sm text-body-sm text-on-surface-variant">Isi ID pengguna &bull; staf</span>
            </button>
          </div>
        </div>

        <?php if (isset($errors['form'])): ?>
        <div class="p-space-sm rounded bg-error-container text-on-error-container font-body-sm text-body-sm flex items-start gap-2" role="alert">
          <span class="material-symbols-outlined text-[18px] mt-0.5">error</span>
          <span><?= e($errors['form']) ?></span>
        </div>
        <?php endif; ?>

        <form class="flex flex-col gap-space-md" method="post" action="<?= url('admin/login.php') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="aksi" value="login"/>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="identity">ID Pengguna (username)</label>
            <div class="relative">
              <input autocomplete="username" class="w-full bg-surface-container-low text-on-surface font-body-md text-body-md px-space-sm py-2 rounded focus:outline-none focus:bg-surface-container-lowest transition-colors placeholder:text-outline"
                     id="identity" name="identity" placeholder="admin" required="" type="text" value="<?= e($_POST['identity'] ?? '') ?>"/>
            </div>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="password">Kata Sandi</label>
            <div class="relative flex items-center">
              <input autocomplete="current-password" class="w-full bg-surface-container-low text-on-surface font-body-md text-body-md pl-space-sm pr-10 py-2 rounded focus:outline-none focus:bg-surface-container-lowest transition-colors placeholder:text-outline"
                     id="password" name="password" placeholder="Masukkan kata sandi akun" required="" type="password"/>
              <button aria-label="Tampilkan atau sembunyikan kata sandi" class="absolute right-2 text-on-surface-variant hover:text-on-surface p-1 rounded transition-colors flex items-center justify-center" id="togglePasswordBtn" type="button">
                <span class="material-symbols-outlined text-[18px]" id="toggleIcon">visibility</span>
              </button>
            </div>
          </div>
          <div class="pt-space-xs flex flex-col gap-space-sm">
            <button class="w-full bg-primary-container hover:bg-primary text-on-primary font-label-md text-label-md py-2.5 px-space-md rounded transition-colors flex items-center justify-center shadow-sm cursor-pointer" id="submitBtn" type="submit">
              Masuk ke Sistem CMS
            </button>
          </div>
        </form>

        <div class="pt-space-xs bg-surface-container-low p-space-sm rounded flex items-start gap-2 text-on-surface-variant">
          <span class="material-symbols-outlined text-[16px] text-outline mt-0.5 shrink-0">help_outline</span>
          <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
            Mengalami kendala otentikasi? Hubungi Tim IT Helpdesk melalui
            <a class="text-primary hover:underline font-medium" href="mailto:it-support@sakinahjourneys.id">it-support@sakinahjourneys.id</a> atau ext. 104
          </p>
        </div>
      </div>
      <div class="mt-space-md text-center">
        <p class="font-label-sm text-label-sm text-on-surface-variant tracking-normal">© 2025 PT Sakinah Harmoni Mulia. Hak Akses Terbatas Khusus Karyawan dan Manajemen.</p>
      </div>
    </div>
  </div>
</main>
<footer class="w-full py-space-md px-margin lg:px-margin-desktop flex flex-col sm:flex-row items-center justify-between gap-space-xs text-on-surface-variant">
  <span class="font-label-sm text-label-sm tracking-wide">Internal Management Subsystem • Authorized Personnel Only</span>
  <span class="font-label-sm text-label-sm tracking-wide">v4.1.2-sec</span>
</footer>
<script>
  function togglePasswordVisibility() {
    var pwd = document.getElementById('password');
    var icon = document.getElementById('toggleIcon');
    if (pwd.type === 'password') { pwd.type = 'text'; icon.textContent = 'visibility_off'; }
    else { pwd.type = 'password'; icon.textContent = 'visibility'; }
  }
  document.getElementById('togglePasswordBtn').addEventListener('click', togglePasswordVisibility);

  var activeClasses = ['ring-1', 'ring-primary', 'bg-primary-fixed/20'];
  function clearRoleActive() {
    document.querySelectorAll('.role-card').forEach(function (b) {
      activeClasses.forEach(function (c) { b.classList.remove(c); });
      b.setAttribute('aria-pressed', 'false');
    });
  }
  var identityInput = document.getElementById('identity');
  var passwordInput = document.getElementById('password');
  document.querySelectorAll('.role-card').forEach(function (btn) {
    btn.addEventListener('click', function () {
      clearRoleActive();
      activeClasses.forEach(function (c) { btn.classList.add(c); });
      btn.setAttribute('aria-pressed', 'true');
      identityInput.value = btn.getAttribute('data-identity');
      passwordInput.focus();
    });
  });
  identityInput.addEventListener('input', clearRoleActive);
</script>
</body>
</html>
