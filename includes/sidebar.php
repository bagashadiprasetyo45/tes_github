<?php
$menu = [
    ['dashboard.php', 'LayoutDashboard', 'Dashboard'],
    ['pemasukan.php', 'ArrowDownLeft', 'Pemasukan'],
    ['pengeluaran.php', 'ArrowUpRight', 'Pengeluaran'],
    ['recurring.php', 'RefreshCw', 'Transaksi Berulang'],
    ['budget.php', 'WalletCards', 'Budget'],
    ['target.php', 'Goal', 'Target'],
    ['tabungan.php', 'PiggyBank', 'Tabungan'],
    ['utang.php', 'CreditCard', 'Utang'],
    ['piutang.php', 'HandCoins', 'Piutang'],
    ['tagihan.php', 'ReceiptText', 'Tagihan'],
    ['kalender.php', 'CalendarDays', 'Kalender'],
    ['laporan.php', 'ChartNoAxesCombined', 'Laporan'],
    ['pencarian.php', 'Search', 'Pencarian'],
    ['kalkulator.php', 'Calculator', 'Kalkulator'],
    ['reminder.php', 'BellRing', 'Reminder'],
    ['export.php', 'Download', 'Export & Backup'],
];
?>
<aside class="sidebar" id="sidebar">
    <div class="brand-lockup">
        <div class="brand-mark"><i data-lucide="sparkles"></i></div>
        <div><strong>KEUANGAN</strong><span>BAGAS</span></div>
        <button class="icon-button sidebar-close" type="button" data-close-sidebar aria-label="Tutup menu"><i data-lucide="x"></i></button>
    </div>
    <div class="sidebar-label">Menu utama</div>
    <nav class="sidebar-nav">
        <?php foreach ($menu as [$href, $icon, $label]): ?>
            <a class="nav-link <?= $activePage === $href ? 'active' : '' ?>" href="<?= e($href) ?>"><i data-lucide="<?= e($icon) ?>"></i><span><?= e($label) ?></span></a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-bottom">
        <a class="profile-mini" href="dashboard.php"><span class="avatar avatar-small">B</span><span><strong>Bagas</strong><small>Personal finance</small></span></a>
        <a class="nav-link logout-link" href="logout.php"><i data-lucide="log-out"></i><span>Logout</span></a>
    </div>
</aside>
<div class="sidebar-overlay" data-close-sidebar></div>