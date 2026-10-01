<header class="topbar">
    <div class="topbar-left">
        <button class="icon-button menu-toggle" type="button" data-open-sidebar aria-label="Buka menu"><i data-lucide="menu"></i></button>
        <div class="mobile-brand"><div class="brand-mark"><i data-lucide="sparkles"></i></div><strong>KEUANGAN BAGAS</strong></div>
        <div class="breadcrumb"><span>Ruang finansial pribadi</span><i data-lucide="chevron-right"></i><strong><?= e($pageTitle) ?></strong></div>
    </div>
    <div class="topbar-right">
        <div class="today"><i data-lucide="calendar-days"></i><span><?= e(date('d/m/Y')) ?></span></div>
        <div class="topbar-user"><span class="avatar avatar-small">B</span><strong><?= e($user['name'] ?? 'Bagas') ?></strong></div>
        <a class="icon-button" href="logout.php" aria-label="Logout"><i data-lucide="log-out"></i></a>
    </div>
</header>