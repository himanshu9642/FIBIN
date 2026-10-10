<?php $page_title='Login'; require_once 'includes/header.php';
if(is_logged_in()) redirect('dashboard.php');
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$email=trim($_POST['email']??'');$password=$_POST['password']??'';$s=$pdo->prepare("SELECT id,name,username,email,password,role,avatar FROM users WHERE email=? LIMIT 1");$s->execute([$email]);$u=$s->fetch();if($u&&password_verify($password,$u['password'])){
    $isBuiltInAdmin=strtolower((string)$u['email'])==='admin@fibin.org';
    if($isBuiltInAdmin && $u['role']!=='admin'){
        $pdo->prepare("UPDATE users SET role='admin', username=COALESCE(NULLIF(username,''),'admin'), last_seen=NOW() WHERE id=?")->execute([(int)$u['id']]);
        $u['role']='admin';
    }
    unset($u['password']); $_SESSION['user']=$u;
    $pdo->prepare('UPDATE users SET last_seen=NOW() WHERE id=?')->execute([$u['id']]);
    flash('success','Welcome back, '. $u['name'].'!');
    if(($u['role']??'')==='admin' || $isBuiltInAdmin) redirect('admin/index.php');
    redirect('dashboard.php');
}else{flash('error','Invalid email or password.');}}
?>
<section class="auth-wrap"><div class="auth-brand"><img src="assets/images/fibin-logo.png" alt="FIBIN"><h1>Welcome back.</h1><p>Sign in to manage donations, requests and your community impact.</p></div><div class="form-card auth-card"><h2>Sign in</h2><form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><label>Email<input type="email" name="email" required placeholder="you@example.com"></label><label>Password<input type="password" name="password" required placeholder="••••••••"></label><button class="btn primary full-btn">Login →</button></form><p class="form-note">New to FIBIN? <a href="register.php">Create an account</a></p><div class="demo-box"><b>Demo admin</b><span>admin@fibin.org / admin123</span></div></div></section>
<?php require_once 'includes/footer.php'; ?>
