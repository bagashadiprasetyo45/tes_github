        </main>
        <footer class="footer"><span>Â© <?= date('Y') ?> KEUANGAN BAGAS</span><span>Catat, pahami, tumbuh.</span></footer>
    </div>
</div>
<div id="toast-stack" class="toast-stack">
    <?php foreach (consumeFlash() as $message): ?>
        <div class="toast toast-<?= e($message['type']) ?>"><i data-lucide="<?= $message['type'] === 'success' ? 'check-circle-2' : 'alert-circle' ?>"></i><span><?= e($message['message']) ?></span><button type="button" class="toast-close" aria-label="Tutup">Ã—</button></div>
    <?php endforeach; ?>
</div>
<div class="modal-backdrop" data-modal-backdrop></div>
<div class="confirm-modal glass-card" id="confirm-modal" aria-hidden="true">
    <div class="modal-icon danger-icon"><i data-lucide="triangle-alert"></i></div>
    <h3>Konfirmasi tindakan</h3>
    <p id="confirm-message">Apakah kamu yakin ingin melanjutkan?</p>
    <div class="modal-actions"><button type="button" class="btn btn-ghost" data-close-confirm>Batal</button><button type="button" class="btn btn-danger" data-confirm-submit>Lanjutkan</button></div>
</div>
<script src="assets/js/app.js"></script>
<?php if (!empty($pageScripts)) echo $pageScripts; ?>
<script>document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });</script>
<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => navigator.serviceWorker.register('service-worker.js').catch(()=>{}));
}
let deferredPrompt;
const pwaBanner = document.getElementById('pwa-install-banner');
window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault(); deferredPrompt = e;
  if (pwaBanner) pwaBanner.style.display = 'flex';
});
window.addEventListener('appinstalled', () => { if (pwaBanner) pwaBanner.style.display = 'none'; deferredPrompt = null; });
document.getElementById('pwa-install-btn')?.addEventListener('click', async () => {
  if (!deferredPrompt) return;
  deferredPrompt.prompt(); await deferredPrompt.userChoice; deferredPrompt = null;
  if (pwaBanner) pwaBanner.style.display = 'none';
});
document.getElementById('pwa-dismiss')?.addEventListener('click', () => { if (pwaBanner) pwaBanner.style.display = 'none'; });
</script>
<div id="pwa-install-banner" style="display:none;position:fixed;left:16px;right:16px;bottom:16px;z-index:9999;align-items:center;gap:12px;padding:14px 16px;border-radius:16px;background:#17233d;color:#fff;box-shadow:0 18px 50px rgba(24,39,75,.25);font-size:13px">
  <span style="flex:1"><b>Install KEUANGAN BAGAS</b><br><small style="opacity:.7">Pasang di HP biar buka lebih cepat & bisa fullscreen</small></span>
  <button id="pwa-install-btn" class="btn btn-primary" style="white-space:nowrap;background:#5b62da;border:0;padding:10px 14px;border-radius:12px;color:#fff;font-weight:700">Install</button>
  <button id="pwa-dismiss" style="background:transparent;border:0;color:rgba(255,255,255,.6);font-size:18px;line-height:1">×</button>
</div>
</body>
</html>
