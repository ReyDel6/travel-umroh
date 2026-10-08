<?php
if (!defined('ROOT_PATH')) { http_response_code(404); exit; } // guard: file include-only, bukan entry point
/**
 * Penutup halaman CMS admin: tutup <main>, notifikasi flash, skrip global.
 */
$flashNow = flash_get();
?>
  </main>
  <div class="h-8"></div>
</div>
<?php if ($flashNow): ?>
<div id="flashBox" class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-4 z-[60] sm:max-w-sm">
  <div class="<?= $flashNow['type'] === 'error' ? 'bg-error text-on-error' : 'bg-primary-container text-on-primary' ?> font-body-sm text-body-sm px-space-md py-space-sm rounded shadow-lg flex items-start gap-2">
    <span class="material-symbols-outlined text-[18px] mt-0.5"><?= $flashNow['type'] === 'error' ? 'error' : 'check_circle' ?></span>
    <span><?= e($flashNow['message']) ?></span>
  </div>
</div>
<script>setTimeout(function(){var b=document.getElementById('flashBox');if(b){b.style.display='none';}},5000);</script>
<?php endif; ?>
<script>
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.confirm(form.getAttribute('data-confirm'))) e.preventDefault();
    });
  });
  document.querySelectorAll('[data-confirm-link]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!window.confirm(el.getAttribute('data-confirm-link'))) e.preventDefault();
    });
  });
</script>
<script>
  // M19: loading state saat submit — cegah klik ganda, hanya untuk form POST
  document.addEventListener('submit', function (e) {
    if (e.defaultPrevented) return;
    var form = e.target;
    if (!form || form.tagName !== 'FORM') return;
    if ((form.getAttribute('method') || 'get').toLowerCase() === 'get') return;
    var btn = form.querySelector('button[type="submit"], button:not([type])');
    if (!btn || btn.disabled) return;
    btn.disabled = true;
    btn.classList.add('opacity-70', 'cursor-wait');
    if (!btn.dataset.label) btn.dataset.label = btn.textContent;
    btn.textContent = 'Mengirim...';
  });
</script>
</body>
</html>
