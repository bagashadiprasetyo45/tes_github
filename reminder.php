<?php
require_once __DIR__.'/includes/auth.php';
requireLogin();
$pdo=db();$uid=userId();$pageTitle='Reminder';$activePage='reminder.php';$today=new DateTimeImmutable('today');$items=[];
$add=function(string $title,?string $date,string $type,string $desc)use(&$items,$today):void{if(!$date)return;$d=new DateTimeImmutable($date);$diff=(int)$today->diff($d)->format('%r%a');if($diff<0)$group='Terlambat';elseif($diff===0)$group='Hari Ini';elseif($diff===1)$group='Besok';elseif($diff<=7)$group='Minggu Ini';else$group='Mendatang';$items[$group][]=compact('title','date','type','desc','diff');};
$st=$pdo->prepare('SELECT name,due_date,amount,category FROM bills WHERE user_id=? AND status<>"Sudah Dibayar"');$st->execute([$uid]);foreach($st as $r)$add($r['name'],$r['due_date'],'Tagihan',formatRupiah($r['amount']).' · '.$r['category']);
$st=$pdo->prepare('SELECT name,due_date,total_amount,paid_amount FROM debts WHERE user_id=? AND status<>"Lunas"');$st->execute([$uid]);foreach($st as $r)$add('Utang: '.$r['name'],$r['due_date'],'Utang','Sisa '.formatRupiah((float)$r['total_amount']-(float)$r['paid_amount']));
$st=$pdo->prepare('SELECT name,due_date,total_amount,paid_amount FROM receivables WHERE user_id=? AND status<>"Lunas"');$st->execute([$uid]);foreach($st as $r)$add('Piutang: '.$r['name'],$r['due_date'],'Piutang','Sisa '.formatRupiah((float)$r['total_amount']-(float)$r['paid_amount']));
$st=$pdo->prepare('SELECT name,deadline,target_amount,current_amount FROM financial_goals WHERE user_id=? AND status="Aktif"');$st->execute([$uid]);foreach($st as $r)$add('Target: '.$r['name'],$r['deadline'],'Target','Progress '.number_format(percentage($r['current_amount'],$r['target_amount']),1,',','.').'%');
$groups=['Hari Ini','Besok','Minggu Ini','Terlambat','Mendatang'];$user=currentUser($pdo);require __DIR__.'/includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">Jangan sampai terlewat</p><h1>Reminder</h1><p>Semua tanggal penting dari catatan keuanganmu dikumpulkan di sini.</p></div><a class="btn btn-ghost" href="tagihan.php"><i data-lucide="receipt-text"></i> Kelola Tagihan</a></div>
<?php if (!$items): ?>
<section class="card empty-state"><div class="empty-icon"><i data-lucide="bell-off"></i></div><strong>Belum ada reminder.</strong><p>Tambahkan tagihan, target, utang, atau piutang dengan deadline.</p></section>
<?php else: ?>
<?php foreach ($groups as $group) { if (empty($items[$group])) { continue; } $groupItems=$items[$group]; $tone=$group==='Terlambat'?'danger':($group==='Hari Ini'?'warning':'neutral'); ?>
<section class="card" style="margin-bottom:18px"><div class="card-header"><div><h2 class="card-title"><?=e($group)?></h2><p class="card-subtitle"><?=count($groupItems)?> item perlu diperhatikan</p></div><span class="badge <?=e($tone)?>"><?=e($group)?></span></div><div class="list">
<?php foreach ($groupItems as $item) { $itemTone='income';$itemIcon='calendar-clock';if($item['type']==='Tagihan'){$itemTone='bill';$itemIcon='receipt-text';}elseif($item['type']==='Utang'){$itemTone='debt';}elseif($item['type']==='Piutang'){$itemTone='receivable';}elseif($item['type']==='Target'){$itemIcon='goal';} ?>
<div class="transaction-row"><div class="type-dot <?=e($itemTone)?>"><i data-lucide="<?=e($itemIcon)?>"></i></div><div class="row-main"><strong><?=e($item['title'])?></strong><small><?=e($item['type'])?> · <?=e($item['desc'])?></small></div><div class="row-amount"><?=e(date('d/m/Y',strtotime($item['date'])))?></div></div>
<?php } ?></div></section>
<?php } ?>
<?php endif; ?>
<?php require __DIR__.'/includes/footer.php'; ?>