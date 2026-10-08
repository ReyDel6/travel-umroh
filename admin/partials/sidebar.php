<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/**
 * Sidebar navigasi + topbar admin — design: dashboard.html
 * Variabel: $activeMenu (kunci menu aktif, contoh: 'paket')
 */
$activeMenu = $activeMenu ?? '';
$countInquiryBaru = (int) model('inquiry')->countBaru();

$menuGroups = [
    [
        'label' => 'Operasional',
        'items' => [
            ['key' => 'ringkasan', 'label' => 'Ringkasan', 'icon' => 'grid_view', 'path' => 'admin/dashboard.php'],
            ['key' => 'paket', 'label' => 'Paket Umroh', 'icon' => 'inventory_2', 'path' => 'admin/paket/index.php'],
            ['key' => 'jadwal', 'label' => 'Jadwal Keberangkatan', 'icon' => 'flight_takeoff', 'path' => 'admin/jadwal/index.php'],
            ['key' => 'fasilitas', 'label' => 'Fasilitas', 'icon' => 'checklist', 'path' => 'admin/fasilitas/index.php'],
            ['key' => 'hotel', 'label' => 'Hotel &amp; Akomodasi', 'icon' => 'hotel', 'path' => 'admin/hotel/index.php'],
            ['key' => 'maskapai', 'label' => 'Maskapai', 'icon' => 'flight', 'path' => 'admin/maskapai/index.php'],
        ],
    ],
    [
        'label' => 'Konten Situs',
        'items' => [
            ['key' => 'galeri', 'label' => 'Galeri Dokumentasi', 'icon' => 'photo_library', 'path' => 'admin/galeri/index.php'],
            ['key' => 'artikel', 'label' => 'Artikel &amp; Jurnal', 'icon' => 'edit_note', 'path' => 'admin/artikel/index.php'],
            ['key' => 'faq', 'label' => 'Pertanyaan Umum', 'icon' => 'quiz', 'path' => 'admin/faq/index.php'],
            ['key' => 'testimoni', 'label' => 'Testimoni', 'icon' => 'rate_review', 'path' => 'admin/testimoni/index.php'],
        ],
    ],
    [
        'label' => 'Pelayanan &amp; Sistem',
        'items' => [
            ['key' => 'inquiry', 'label' => 'Inquiry &amp; Konsultasi', 'icon' => 'support_agent', 'path' => 'admin/inquiry/index.php'],
            ['key' => 'meta', 'label' => 'SEO Halaman Publik', 'icon' => 'travel_explore', 'path' => 'admin/meta.php'],
            ['key' => 'profil', 'label' => 'Profil &amp; Pengaturan', 'icon' => 'tune', 'path' => 'admin/profil.php'],
        ],
    ],
];
?>
<aside id="adminSidebar" class="fixed left-0 top-0 h-full w-64 -translate-x-full lg:translate-x-0 bg-surface-container-low z-50 flex flex-col justify-between shadow-[0_1px_8px_rgba(0,0,0,0.04)] overflow-y-auto transition-transform duration-300 ease-in-out">
  <div class="flex flex-col">
    <a class="h-16 px-6 flex items-center gap-3 bg-surface-container" href="<?= url('admin/dashboard.php') ?>">
      <div class="w-7 h-7 bg-primary flex items-center justify-center rounded">
        <span class="material-symbols-outlined text-on-primary text-[18px]">corporate_fare</span>
      </div>
      <div class="flex flex-col">
        <span class="font-label-md text-label-md text-on-surface font-bold tracking-tight uppercase">Sakinah</span>
        <span class="font-label-sm text-label-sm text-on-surface-variant">Admin Workspace</span>
      </div>
    </a>
    <?php foreach ($menuGroups as $group): ?>
    <div class="px-4 pt-4 pb-1">
      <span class="font-label-sm text-label-sm text-outline px-3 uppercase tracking-wider"><?= $group['label'] ?></span>
    </div>
    <nav class="flex flex-col gap-1 px-3">
      <?php foreach ($group['items'] as $item): $isActive = $activeMenu === $item['key']; ?>
      <a class="flex items-center gap-3 px-3 py-2.5 rounded transition-all <?= $isActive ? 'bg-primary-container text-on-primary font-semibold shadow-sm' : 'text-on-surface-variant font-label-md text-label-md hover:bg-surface-container-high hover:text-on-surface' ?>"
         href="<?= url($item['path']) ?>"<?= $isActive ? ' aria-current="page"' : '' ?>>
        <span class="material-symbols-outlined text-[20px]"><?= $item['icon'] ?></span>
        <span><?= $item['label'] ?></span>
        <?php if ($item['key'] === 'inquiry' && ($countInquiryBaru ?? 0) > 0): ?>
        <span class="ml-auto inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full bg-error text-on-error font-label-sm text-label-sm"><?= (int) $countInquiryBaru ?></span>
        <?php endif; ?>
      </a>
      <?php endforeach; ?>
    </nav>
    <?php endforeach; ?>
  </div>
  <div class="p-4 bg-surface-container-high">
    <div class="flex items-center justify-between text-on-surface-variant">
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-surface-tint"></span>
        <span class="font-label-sm text-label-sm text-on-surface">Server Hijaz-01</span>
      </div>
      <span class="font-label-sm text-label-sm text-outline">v2.4</span>
    </div>
  </div>
</aside>
<div id="sidebarBackdrop" class="fixed inset-0 z-30 bg-black/30 hidden lg:hidden"></div>
<div class="pl-0 lg:pl-64">
  <header class="fixed top-0 left-0 lg:left-64 right-0 h-16 bg-surface/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 flex items-center justify-between gap-4 px-4 lg:px-8">
    <div class="flex items-center justify-between gap-4 flex-1 min-w-0">
      <button type="button" id="btnSidebar" aria-label="Buka menu navigasi" aria-expanded="false" class="lg:hidden w-9 h-9 shrink-0 rounded border border-outline-variant/60 flex items-center justify-center text-primary">
        <span class="material-symbols-outlined text-[20px]">menu</span>
      </button>
      <form class="w-full max-w-96 flex items-center gap-2 px-3 py-1.5 bg-surface-container-lowest rounded shadow-[0_1px_8px_rgba(0,0,0,0.02)]" method="get" action="<?= url('admin/paket/index.php') ?>">
        <span class="material-symbols-outlined text-outline text-[18px]">search</span>
        <input class="w-full bg-transparent border-0 outline-none font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none"
               placeholder="Cari paket..." type="text" name="cari" value="<?= e($_GET['cari'] ?? '') ?>"/>
      </form>
    </div>
    <div class="flex items-center gap-5">
      <a aria-label="Lihat Situs Publik" class="w-8 h-8 flex items-center justify-center rounded text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors" href="<?= url('') ?>" rel="noopener" target="_blank" title="Lihat situs publik">
        <span class="material-symbols-outlined text-[20px]">language</span>
      </a>
      <div class="h-5 w-px bg-surface-variant"></div>
      <div class="flex items-center gap-3">
        <div class="flex flex-col text-right hidden sm:flex">
          <span class="font-label-md text-label-md text-on-surface font-semibold leading-tight"><?= e($_SESSION['admin_username'] ?? 'Admin') ?></span>
          <span class="font-label-sm text-label-sm text-on-surface-variant leading-tight">Staf Operasional</span>
        </div>
        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
          <span class="material-symbols-outlined text-on-primary text-[18px]">person</span>
        </div>
        <a class="w-8 h-8 flex items-center justify-center rounded text-on-surface-variant hover:bg-error-container hover:text-error transition-colors" href="<?= url('admin/logout.php') ?>" title="Keluar" aria-label="Keluar dari sistem">
          <span class="material-symbols-outlined text-[20px]">logout</span>
        </a>
      </div>
    </div>
  </header>
  <main class="relative pt-16 bg-surface w-full px-4 lg:px-8 pb-12">
  <script>
  (function () {
    var btn = document.getElementById('btnSidebar');
    var aside = document.getElementById('adminSidebar');
    var backdrop = document.getElementById('sidebarBackdrop');
    if (!btn || !aside || !backdrop) return;
    function open() { aside.classList.add('translate-x-0'); aside.classList.remove('-translate-x-full'); backdrop.classList.remove('hidden'); btn.setAttribute('aria-expanded', 'true'); }
    function close() { aside.classList.remove('translate-x-0'); aside.classList.add('-translate-x-full'); backdrop.classList.add('hidden'); btn.setAttribute('aria-expanded', 'false'); }
    btn.addEventListener('click', function () { aside.classList.contains('-translate-x-full') ? open() : close(); });
    backdrop.addEventListener('click', close);
    window.addEventListener('resize', function () { if (window.innerWidth >= 1024) close(); });
  })();
  </script>
