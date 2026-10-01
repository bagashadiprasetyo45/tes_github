<?php
require_once __DIR__ . '/auth.php';
requireLogin();
$pdo = db(); $uid = userId();
$type = $transactionType ?? 'income';
$pageTitle = $pageTitle ?? ($type === 'income' ? 'Pemasukan' : 'Pengeluaran');
$activePage = $activePage ?? currentPage();
$defaults = $type === 'income'
    ? ['Gaji','Freelance','Bisnis','Bonus','Komisi','Penjualan','Investasi','Hadiah','Pemasukan Lainnya']
    : ['Makanan','Minuman','Transportasi','Bensin','Parkir','Tagihan','Belanja','Hiburan','Gaming','Kesehatan','Pendidikan','Rumah Tangga','Pulsa/Internet','Fashion','Nongkrong','Travel','Pengeluaran Lainnya'];
$categoryTable = $type === 'income' ? 'income_categories' : 'expense_categories';
$stmt = $pdo->prepare("SELECT name FROM {$categoryTable} WHERE user_id = ? ORDER BY name");
$stmt->execute([$uid]);
$categories = array_values(array_unique(array_merge($defaults, array_column($stmt->fetchAll(), 'name'))));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM transactions WHERE id = ? AND user_id = ? AND type = ?');
        $stmt->execute([(int) ($_POST['id'] ?? 0), $uid, $type]);
        flash('success', 'Data berhasil dihapus.');
        redirect($type === 'income' ? 'pemasukan.php' : 'pengeluaran.php');
    }
    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $amount = parseAmount($_POST['amount'] ?? 0);
        $category = trim((string) ($_POST['category'] ?? ''));
        $date = (string) ($_POST['transaction_date'] ?? '');
        $description = trim((string) ($_POST['description'] ?? ''));
        $errors = [];
        if ($amount <= 0) $errors[] = 'Nominal harus lebih besar dari 0.';
        if ($category === '') $errors[] = 'Kategori wajib diisi.';
        if (!validDate($date)) $errors[] = 'Tanggal tidak valid.';
        if (strlen($description) > 255) $errors[] = 'Deskripsi terlalu panjang.';
        if ($errors) {
            flash('error', implode(' ', $errors));
            redirect(($type === 'income' ? 'pemasukan.php' : 'pengeluaran.php') . '?action=' . ($id ? 'edit&id=' . $id : 'add'));
        }
        if (!in_array($category, $defaults, true)) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO {$categoryTable} (user_id, name) VALUES (?, ?)");
            $stmt->execute([$uid, $category]);
        }
        if ($id) {
            $stmt = $pdo->prepare('UPDATE transactions SET amount = ?, category = ?, description = ?, transaction_date = ? WHERE id = ? AND user_id = ? AND type = ?');
            $stmt->execute([$amount, $category, $description, $date, $id, $uid, $type]);
            flash('success', ($type === 'income' ? 'Pemasukan' : 'Pengeluaran') . ' berhasil diperbarui.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO transactions (user_id, type, amount, category, description, transaction_date) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$uid, $type, $amount, $category, $description, $date]);
            flash('success', ($type === 'income' ? 'Pemasukan' : 'Pengeluaran') . ' berhasil ditambahkan.');
        }
        redirect($type === 'income' ? 'pemasukan.php' : 'pengeluaran.php');
    }
}$q = trim((string) ($_GET['q'] ?? ''));
$categoryFilter = trim((string) ($_GET['category'] ?? ''));
$sort = (string) ($_GET['sort'] ?? 'date_desc');
$sortMap = ['date_desc'=>'transaction_date DESC, id DESC','date_asc'=>'transaction_date ASC, id ASC','amount_desc'=>'amount DESC, id DESC','amount_asc'=>'amount ASC, id ASC'];
$order = $sortMap[$sort] ?? $sortMap['date_desc'];
$page = max(1, (int) ($_GET['page'] ?? 1)); $perPage = 12;
$where = ['user_id = ?', 'type = ?']; $params = [$uid, $type];
if ($q !== '') { $where[] = '(description LIKE ? OR category LIKE ?)'; $params[] = "%{$q}%"; $params[] = "%{$q}%"; }
if ($categoryFilter !== '') { $where[] = 'category = ?'; $params[] = $categoryFilter; }
$whereSql = implode(' AND ', $where);
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE {$whereSql}"); $countStmt->execute($params);
$total = (int) $countStmt->fetchColumn(); $pages = max(1, (int) ceil($total / $perPage)); $page = min($page, $pages); $offset = ($page - 1) * $perPage;
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE {$whereSql} ORDER BY {$order} LIMIT {$perPage} OFFSET {$offset}"); $stmt->execute($params); $rows = $stmt->fetchAll();
$edit = null;
if (($_GET['action'] ?? '') === 'edit') { $stmt = $pdo->prepare('SELECT * FROM transactions WHERE id = ? AND user_id = ? AND type = ?'); $stmt->execute([(int) ($_GET['id'] ?? 0), $uid, $type]); $edit = $stmt->fetch() ?: null; }
if (($_GET['action'] ?? '') === 'add') $edit = ['id'=>0,'amount'=>'','category'=>'','description'=>'','transaction_date'=>date('Y-m-d')];
$view = null;
if (isset($_GET['view'])) { $stmt = $pdo->prepare('SELECT * FROM transactions WHERE id = ? AND user_id = ? AND type = ?'); $stmt->execute([(int) $_GET['view'], $uid, $type]); $view = $stmt->fetch() ?: null; }
$user = currentUser($pdo); require __DIR__ . '/header.php';
$color = $type === 'income' ? 'positive' : 'negative'; $icon = $type === 'income' ? 'arrow-down-left' : 'arrow-up-right'; $noun = $type === 'income' ? 'pemasukan' : 'pengeluaran';
?><div class="page-heading"><div><p class="eyebrow">Catatan <?= e($noun) ?></p><h1><?= e($pageTitle) ?></h1><p>Kelola semua <?= e($noun) ?> dengan rapi dan mudah dicari.</p></div><a class="btn btn-primary" href="?action=add"><i data-lucide="plus"></i> Tambah <?= e(ucfirst($noun)) ?></a></div>
<form class="toolbar glass-card" method="get"><div class="toolbar-form"><input class="input search-input" name="q" value="<?= e($q) ?>" placeholder="Cari kategori atau deskripsi..."><select class="select" name="category"><option value="">Semua kategori</option><?php foreach ($categories as $cat): ?><option value="<?= e($cat) ?>" <?= $categoryFilter === $cat ? 'selected' : '' ?>><?= e($cat) ?></option><?php endforeach; ?></select><select class="select" name="sort"><option value="date_desc" <?= $sort === 'date_desc' ? 'selected' : '' ?>>Terbaru</option><option value="date_asc" <?= $sort === 'date_asc' ? 'selected' : '' ?>>Terlama</option><option value="amount_desc" <?= $sort === 'amount_desc' ? 'selected' : '' ?>>Nominal terbesar</option><option value="amount_asc" <?= $sort === 'amount_asc' ? 'selected' : '' ?>>Nominal terkecil</option></select><button class="btn btn-soft" type="submit"><i data-lucide="filter"></i> Filter</button></div></form>
<?php if ($view): ?><section class="card" style="margin-bottom:18px"><div class="card-header"><div><p class="eyebrow">Detail transaksi</p><h2 class="card-title"><?= e($view['description'] ?: $view['category']) ?></h2><p class="card-subtitle"><?= e($view['category']) ?> · <?= e(date('d/m/Y', strtotime($view['transaction_date']))) ?></p></div><a class="btn btn-ghost btn-sm" href="<?= e($type === 'income' ? 'pemasukan.php' : 'pengeluaran.php') ?>">Tutup</a></div><div class="stat-value <?= $color ?>"><?= $type === 'income' ? '+' : '-' ?><?= formatRupiah($view['amount']) ?></div></section><?php endif; ?>
<section class="card"><div class="card-header"><div><h2 class="card-title">Riwayat <?= e($noun) ?></h2><p class="card-subtitle"><?= number_format($total, 0, ',', '.') ?> catatan tersimpan</p></div></div><?php if (!$rows): ?><div class="empty-state"><div class="empty-icon"><i data-lucide="<?= $icon ?>"></i></div><strong>Belum ada <?= e($noun) ?>.</strong><p>Catat transaksi pertamamu untuk mulai melihat pola keuangan.</p><a class="btn btn-primary btn-sm" href="?action=add">+ Tambah <?= e(ucfirst($noun)) ?></a></div><?php else: ?><div class="table-wrap"><table class="data-table"><thead><tr><th>Tanggal</th><th>Kategori</th><th>Deskripsi</th><th>Nominal</th><th></th></tr></thead><tbody><?php foreach ($rows as $row): ?><tr><td><?= e(date('d/m/Y', strtotime($row['transaction_date']))) ?></td><td><span class="badge neutral"><?= e($row['category']) ?></span></td><td><?= e($row['description'] ?: '—') ?></td><td class="amount <?= $color ?>"><?= $type === 'income' ? '+' : '-' ?><?= formatRupiah($row['amount']) ?></td><td><div class="actions"><a class="btn btn-soft btn-sm icon-btn" href="?view=<?= (int) $row['id'] ?>" title="Detail"><i data-lucide="eye"></i></a><a class="btn btn-ghost btn-sm icon-btn" href="?action=edit&id=<?= (int) $row['id'] ?>" title="Edit"><i data-lucide="pencil"></i></a><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button class="btn btn-ghost btn-sm icon-btn" type="submit" data-confirm="Hapus <?= e($noun) ?> ini?" title="Hapus"><i data-lucide="trash-2"></i></button></form></div></td></tr><?php endforeach; ?></tbody></table></div><?php if ($pages > 1): ?><div class="pagination"><?php for ($i=1;$i<=$pages;$i++): ?><a class="<?= $i === $page ? 'active' : '' ?>" href="?<?= http_build_query(['q'=>$q,'category'=>$categoryFilter,'sort'=>$sort,'page'=>$i]) ?>"><?= $i ?></a><?php endfor; ?></div><?php endif; ?><?php endif; ?></section>
<div class="modal <?= $edit ? 'is-open' : '' ?>" id="transaction-modal" aria-hidden="<?= $edit ? 'false' : 'true' ?>"><div class="modal-head"><div><h3><?= $edit && $edit['id'] ? 'Edit' : 'Tambah' ?> <?= e(ucfirst($noun)) ?></h3><p>Nominal disimpan sebagai angka dan ditampilkan dalam Rupiah.</p></div><button class="icon-button" type="button" data-close-modal><i data-lucide="x"></i></button></div><form method="post" data-amount-form><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>"><div class="form-grid"><div class="form-field"><label for="amount">Nominal</label><input class="input" id="amount" name="amount" data-amount inputmode="numeric" placeholder="Contoh: 1.500.000" value="<?= e($edit['amount'] ?? '') ?>" required></div><div class="form-field"><label for="category">Kategori</label><input class="input" id="category" name="category" list="category-list" placeholder="Pilih atau ketik kategori" value="<?= e($edit['category'] ?? '') ?>" required><datalist id="category-list"><?php foreach ($categories as $cat): ?><option value="<?= e($cat) ?>"><?php endforeach; ?></datalist></div><div class="form-field"><label for="transaction_date">Tanggal</label><input class="input" id="transaction_date" type="date" name="transaction_date" value="<?= e($edit['transaction_date'] ?? date('Y-m-d')) ?>" required></div><div class="form-field"><label for="description">Deskripsi</label><input class="input" id="description" name="description" maxlength="255" placeholder="Contoh: Gaji bulan ini" value="<?= e($edit['description'] ?? '') ?>"></div></div><div class="modal-actions"><button class="btn btn-ghost" type="button" data-close-modal>Batal</button><button class="btn btn-primary" type="submit"><i data-lucide="save"></i> Simpan</button></div></form></div>
<?php require __DIR__ . '/footer.php'; ?>