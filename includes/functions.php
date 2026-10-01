<?php
declare(strict_types=1);
function e(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function formatRupiah(float|int|string|null $amount): string { return 'Rp '.number_format((float)($amount??0),0,',','.'); }
function parseAmount(mixed $value): float {
    if (is_int($value)||is_float($value)) return round((float)$value,2);
    $value=trim((string)$value);$value=preg_replace('/[^0-9,.-]/','',$value)??'0';
    if (str_contains($value,',')&&str_contains($value,'.')) {$value=str_replace('.','',$value);$value=str_replace(',','.',$value);}
    elseif (str_contains($value,',')) {$value=str_replace(',','.',$value);}
    elseif (substr_count($value,'.')>1||preg_match('/\.\d{3}$/',$value)) {$value=str_replace('.','',$value);}
    return round((float)$value,2);
}
function validDate(string $date): bool { $parsed=DateTime::createFromFormat('Y-m-d',$date);return $parsed!==false&&$parsed->format('Y-m-d')===$date; }
function dateOrToday(?string $date): string { return $date&&validDate($date)?$date:date('Y-m-d'); }
function csrfToken(): string { if(empty($_SESSION['csrf_token']))$_SESSION['csrf_token']=bin2hex(random_bytes(32));return $_SESSION['csrf_token']; }
function verifyCsrf(): void { $token=$_POST['csrf_token']??'';if(!is_string($token)||!hash_equals($_SESSION['csrf_token']??'',$token)){http_response_code(419);exit('Sesi formulir sudah kedaluwarsa. Silakan kembali dan coba lagi.');} }
function redirect(string $url): never { header('Location: '.$url);exit; }
function flash(string $type,string $message): void { $_SESSION['flash'][]=['type'=>$type,'message'=>$message]; }
function consumeFlash(): array { $messages=$_SESSION['flash']??[];unset($_SESSION['flash']);return $messages; }
function currentPage(): string { return basename($_SERVER['PHP_SELF']??'dashboard.php'); }
function userId(): int { return (int)($_SESSION['user_id']??0); }
function monthBounds(?string $month=null): array { $month=$month?:date('Y-m');if(!preg_match('/^\d{4}-\d{2}$/',$month))$month=date('Y-m');$start=$month.'-01';return [$start,date('Y-m-t',strtotime($start))]; }
function percentage(float|int $value,float|int $total): float { return $total>0?min(100,round(((float)$value/(float)$total)*100,1)):0.0; }
function statusBudget(float $used,float $budget): array { $percent=$budget>0?($used/$budget)*100:0;if($percent>100)return ['Melebihi Budget','danger',100];if($percent>=70)return ['Mendekati Batas','warning',round($percent,1)];return ['Aman','success',round($percent,1)]; }
function transactionSum(PDO $pdo,int $uid,string $type,?string $start=null,?string $end=null): float { $sql='SELECT COALESCE(SUM(amount),0) FROM transactions WHERE user_id=? AND type=?';$params=[$uid,$type];if($start!==null){$sql.=' AND transaction_date>=?';$params[]=$start;}if($end!==null){$sql.=' AND transaction_date<=?';$params[]=$end;}$stmt=$pdo->prepare($sql);$stmt->execute($params);return (float)$stmt->fetchColumn(); }
function currentUser(PDO $pdo): array { $stmt=$pdo->prepare('SELECT id,username,name FROM users WHERE id=? LIMIT 1');$stmt->execute([userId()]);return $stmt->fetch()?:['id'=>userId(),'username'=>'bagas','name'=>'Bagas']; }
function pageTitle(string $title): string { return $title.' | KEUANGAN BAGAS'; }