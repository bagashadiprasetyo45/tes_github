<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
$pdo = db();
$uid = userId();
$pageTitle = 'Backup & Restore';
$activePage = 'export.php';
$tables = ['transactions','recurring_transactions','income_categories','expense_categories','budgets','savings','financial_goals','debts','receivables','bills','reminders','savings_history','debt_payments','receivable_payments'];
$userTables = ['transactions','recurring_transactions','income_categories','expense_categories','budgets','savings','financial_goals','debts','receivables','bills','reminders'];
$childTables = ['savings_history'=>['savings','saving_id'],'debt_payments'=>['debts','debt_id'],'receivable_payments'=>['receivables','receivable_id']];
function backupRows(PDO $pdo,string $table,int $uid,array $userTables,array $childTables): array {
    if (in_array($table, $userTables, true)) { $st=$pdo->prepare("SELECT * FROM {$table} WHERE user_id=?"); $st->execute([$uid]); return $st->fetchAll(); }
    [$parent,$foreign] = $childTables[$table]; $st=$pdo->prepare("SELECT c.* FROM {$table} c JOIN {$parent} p ON p.id=c.{$foreign} WHERE p.user_id=?"); $st->execute([$uid]); return $st->fetchAll();
}
if (isset($_GET['download'])) {
    $payload=['app'=>'KEUANGAN BAGAS','version'=>1,'exported_at'=>date(DATE_ATOM),'tables'=>[]];
    foreach ($tables as $table) $payload['tables'][$table]=backupRows($pdo,$table,$uid,$userTables,$childTables);
    header('Content-Type: application/json; charset=utf-8'); header('Content-Disposition: attachment; filename="keuangan_bagas_backup_'.date('Ymd_His').'.json"');
    echo json_encode($payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    if (empty($_FILES['backup_file']['tmp_name']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) { flash('error','Pilih file JSON backup yang valid.'); redirect('backup.php'); }
    $data=json_decode((string)file_get_contents($_FILES['backup_file']['tmp_name']),true);
    if (!is_array($data) || ($data['app']??'')!=='KEUANGAN BAGAS' || !is_array($data['tables']??null)) { flash('error','Format backup tidak dikenali atau rusak.'); redirect('backup.php'); }
    $columns=['transactions'=>['id','type','amount','category','description','transaction_date','created_at','updated_at'],'recurring_transactions'=>['id','type','amount','category','description','frequency','next_date','active','created_at'],'income_categories'=>['id','name','created_at'],'expense_categories'=>['id','name','created_at'],'budgets'=>['id','category','amount','month','year','created_at','updated_at'],'savings'=>['id','name','target_amount','current_amount','deadline','description','created_at','updated_at'],'financial_goals'=>['id','name','target_amount','current_amount','deadline','category','status','description','created_at','updated_at'],'debts'=>['id','name','total_amount','paid_amount','due_date','description','status','created_at','updated_at'],'receivables'=>['id','name','total_amount','paid_amount','due_date','description','status','created_at','updated_at'],'bills'=>['id','name','amount','due_date','category','recurring','status','description','created_at','updated_at'],'reminders'=>['id','title','description','reminder_date','type','status','created_at'],'savings_history'=>['id','saving_id','amount','type','description','date','created_at'],'debt_payments'=>['id','debt_id','amount','payment_date','description','created_at'],'receivable_payments'=>['id','receivable_id','amount','payment_date','description','created_at']];
    try {
        $pdo->beginTransaction(); $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
        foreach (['savings_history','debt_payments','receivable_payments'] as $table) $pdo->exec("DELETE FROM {$table}");
        foreach (array_reverse($userTables) as $table) { $st=$pdo->prepare("DELETE FROM {$table} WHERE user_id=?"); $st->execute([$uid]); }
        $order=['income_categories','expense_categories','transactions','recurring_transactions','budgets','savings','savings_history','financial_goals','debts','debt_payments','receivables','receivable_payments','bills','reminders'];
        foreach($order as $table){foreach(($data['tables'][$table]??[]) as $row){if(!is_array($row))continue;$cols=[];$values=[];foreach($columns[$table] as $col){if(array_key_exists($col,$row)){$cols[]=$col;$values[]=$row[$col];}}if(in_array($table,$userTables,true)){$cols[]='user_id';$values[]=$uid;}$marks=implode(',',array_fill(0,count($cols),'?'));$st=$pdo->prepare("INSERT INTO {$table} (".implode(',',$cols).") VALUES ({$marks})");$st->execute($values);}}
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1'); $pdo->commit(); flash('success','Backup berhasil dipulihkan. Data aplikasi sebelumnya telah diganti.');
    } catch (Throwable $exception) { if($pdo->inTransaction())$pdo->rollBack();$pdo->exec('SET FOREIGN_KEY_CHECKS=1');flash('error','Restore gagal. Pastikan file backup berasal dari KEUANGAN BAGAS.'); }
    redirect('backup.php');
}
$user=currentUser($pdo); require __DIR__.'/includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">Perlindungan data</p><h1>Backup & Restore</h1><p>Backup seluruh data menjadi JSON dan pulihkan hanya setelah file divalidasi.</p></div><a class="btn btn-primary" href="?download=1"><i data-lucide="download"></i> Download Backup JSON</a></div>
<section class="grid-two"><article class="card"><div class="card-header"><div><h2 class="card-title">Backup</h2><p class="card-subtitle">Mencakup transaksi, budget, target, tabungan, utang, piutang, tagihan, kategori, dan reminder.</p></div><span class="stat-icon"><i data-lucide="archive"></i></span></div><a class="btn btn-primary" href="?download=1">Unduh seluruh data</a></article><article class="card"><div class="card-header"><div><h2 class="card-title">Restore</h2><p class="card-subtitle">Restore akan mengganti seluruh data aplikasi Bagas yang ada.</p></div><span class="stat-icon"><i data-lucide="upload"></i></span></div><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?=e(csrfToken())?>"><div class="form-field"><label for="backup_file">File backup JSON</label><input class="input" id="backup_file" type="file" name="backup_file" accept="application/json,.json" required></div><button class="btn btn-danger" type="submit" data-confirm="Restore akan mengganti seluruh data aplikasi saat ini. Lanjutkan?" style="margin-top:15px"><i data-lucide="triangle-alert"></i> Restore data</button></form></article></section>
<section class="card"><div class="card-header"><div><h2 class="card-title">Catatan keamanan</h2><p class="card-subtitle">Simpan file backup di lokasi pribadi dan jangan membagikannya sembarangan.</p></div></div><div class="insight-list"><div class="insight"><div class="insight-icon"><i data-lucide="shield-check"></i></div><span>File restore harus JSON hasil backup KEUANGAN BAGAS dan isinya divalidasi sebelum diproses.</span></div><div class="insight"><div class="insight-icon"><i data-lucide="database"></i></div><span>Restore berjalan dalam transaksi database agar perubahan gagal dapat dibatalkan.</span></div></div></section>
<?php require __DIR__.'/includes/footer.php'; ?>