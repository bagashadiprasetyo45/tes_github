<?php
require_once __DIR__ . '/includes/auth.php';
redirectIfLoggedIn();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        try {
            $stmt = db()->prepare('SELECT id, username, password, name FROM users WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $account = $stmt->fetch();
            if ($account && password_verify($password, $account['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $account['id'];
                $_SESSION['user_name'] = $account['name'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                redirect('dashboard.php');
            }
            $error = 'Username atau password tidak sesuai.';
        } catch (Throwable $exception) {
            $error = 'Database belum siap. Import database/keuangan_bagas.sql terlebih dahulu.';
        }
    }
}
function loginEscape(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
?><!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk | KEUANGAN BAGAS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#5b62da">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="KEUANGAN BAGAS">
<link rel="apple-touch-icon" href="assets/icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="192x192" href="assets/icons/icon-192.png">
<link rel="stylesheet" href="assets/css/style.css"><script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="login-page">
<div class="login-orb orb-one"></div><div class="login-orb orb-two"></div>
<section class="login-card">
    <div class="login-brand"><div class="brand-mark"><i data-lucide="sparkles"></i></div><h1>KEUANGAN BAGAS</h1><p>Kelola keuanganmu dengan lebih teratur</p></div>
    <?php if ($error): ?><div class="toast toast-error" style="position:static;margin-bottom:16px"><i data-lucide="alert-circle"></i><span><?= loginEscape($error) ?></span></div><?php endif; ?>
    <form class="login-form" method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?= loginEscape(csrfToken()) ?>">
        <div><label class="login-label" for="username">Username</label><div class="input-wrap"><i data-lucide="user-round"></i><input class="input" id="username" name="username" autocomplete="username" placeholder="Masukkan username" value="<?= loginEscape((string) ($_POST['username'] ?? '')) ?>" required></div></div>
        <div><label class="login-label" for="password">Password</label><div class="input-wrap"><i data-lucide="lock-keyhole"></i><input class="input" id="password" type="password" name="password" autocomplete="current-password" placeholder="Masukkan password" required></div></div>
        <button class="btn btn-primary login-submit" type="submit"><i data-lucide="log-in"></i> Masuk ke panel</button>
    </form>
    <div class="login-foot">Ruang pribadi untuk mencatat, memahami, dan menumbuhkan keuanganmu.</div>
</section>
<script>document.addEventListener('DOMContentLoaded',()=>{if(window.lucide)lucide.createIcons()});</script>
<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => navigator.serviceWorker.register('service-worker.js').catch(()=>{}));
}
</script>
</body></html>
