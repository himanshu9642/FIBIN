<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/database.php';
function e($value){return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8');}
function redirect($url){header('Location: '.$url);exit;}
function is_logged_in(){return isset($_SESSION['user']);}
function user(){return $_SESSION['user']??null;}
function is_admin(){
    $u=user();
    return !empty($u) && (($u['role']??'')==='admin' || strtolower((string)($u['email']??''))==='admin@fibin.org');
}
function refresh_session_user(){
    global $pdo;
    if(empty($_SESSION['user']['id'])) return null;
    $s=$pdo->prepare('SELECT id,name,username,email,role,avatar,bio,phone,city FROM users WHERE id=? LIMIT 1');
    $s->execute([(int)$_SESSION['user']['id']]);
    $u=$s->fetch();
    if($u){$_SESSION['user']=$u;}
    return $u;
}
function require_login(){if(!is_logged_in()) redirect('login.php'); refresh_session_user();}
function require_admin(){
    global $pdo;
    if(!is_logged_in()) redirect('../login.php?error=login_required');
    $u=refresh_session_user();
    if(!$u) redirect('../login.php?error=admin_required');
    // Keep the built-in FIBIN administrator recoverable even if an older database
    // changed the role accidentally. This is restricted to the known admin email.
    if(strtolower((string)$u['email'])==='admin@fibin.org' && $u['role']!=='admin'){
        $pdo->prepare("UPDATE users SET role='admin', username=COALESCE(NULLIF(username,''),'admin') WHERE id=?")->execute([(int)$u['id']]);
        $u=refresh_session_user();
    }
    if(($u['role']??'')!=='admin') redirect('../login.php?error=admin_required');
    return $u;
}
function flash($key,$message=null){if($message!==null){$_SESSION['flash'][$key]=$message;return;} $msg=$_SESSION['flash'][$key]??null;unset($_SESSION['flash'][$key]);return $msg;}
function csrf_token(){if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf'];}
function verify_csrf(){if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??''))die('Invalid request token.');}
function badge_class($status){return match($status){'approved','available','accepted','completed','online'=>'success','pending','away'=>'warning','rejected','cancelled','expired'=>'danger',default=>'info'};}
function time_ago($date){$diff=time()-strtotime($date);if($diff<60)return 'just now';if($diff<3600)return floor($diff/60).'m ago';if($diff<86400)return floor($diff/3600).'h ago';if($diff<604800)return floor($diff/86400).'d ago';return date('d M Y',strtotime($date));}
function notify_user($userId,$title,$message,$link=null){global $pdo;$s=$pdo->prepare('INSERT INTO notifications(user_id,title,message,link) VALUES(?,?,?,?)');$s->execute([$userId,$title,$message,$link]);}
function distance_km($lat1,$lon1,$lat2,$lon2){$earth=6371;$dLat=deg2rad($lat2-$lat1);$dLon=deg2rad($lon2-$lon1);$a=sin($dLat/2)**2+cos(deg2rad($lat1))*cos(deg2rad($lat2))*sin($dLon/2)**2;return $earth*2*atan2(sqrt($a),sqrt(1-$a));}
function upload_image($field,$folder,$prefix='image_'){
    if(empty($_FILES[$field]) || $_FILES[$field]['error']===UPLOAD_ERR_NO_FILE) return null;
    if($_FILES[$field]['error']!==UPLOAD_ERR_OK) throw new RuntimeException('Image upload failed.');
    if($_FILES[$field]['size']>5*1024*1024) throw new RuntimeException('Image must be under 5MB.');
    $mime=(new finfo(FILEINFO_MIME_TYPE))->file($_FILES[$field]['tmp_name']);
    $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    if(!isset($allowed[$mime])) throw new RuntimeException('Only JPG, PNG or WebP images are allowed.');
    $base=dirname(__DIR__).'/uploads/'.$folder;
    if(!is_dir($base)) mkdir($base,0755,true);
    $name=$prefix.bin2hex(random_bytes(8)).'.'.$allowed[$mime];
    if(!move_uploaded_file($_FILES[$field]['tmp_name'],$base.'/'.$name)) throw new RuntimeException('Could not save the uploaded image.');
    return 'uploads/'.$folder.'/'.$name;
}
function image_url($path,$root_prefix=''){if(!$path)return $root_prefix.'assets/images/food/food-01.svg'; if(preg_match('#^https?://#i',$path))return $path; return $root_prefix.$path;}
function mention_user_ids($text){global $pdo;$ids=[];preg_match_all('/@([A-Za-z0-9_]{2,40})/',$text,$m);foreach(array_unique($m[1]??[]) as $handle){$s=$pdo->prepare('SELECT id FROM users WHERE username=? LIMIT 1');$s->execute([$handle]);if($id=$s->fetchColumn())$ids[]=(int)$id;}return $ids;}
