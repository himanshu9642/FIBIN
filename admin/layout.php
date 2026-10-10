<?php
require_once '../config/config.php';
require_admin();
$page_title=$page_title??'Admin'; $root_prefix='../';
require_once '../includes/header.php';
$admin_nav=[
 ['Dashboard','index.php','📊'],['Users','users.php','👥'],['Food Listings','foods.php','🍱'],['NGOs','ngos.php','🏢'],['Volunteers','volunteers.php','🤝'],['Donations','donations.php','🎁'],['Deliveries','deliveries.php','🚚'],['Campaigns','campaigns.php','📣'],['Community','community.php','💬'],['Reports','reports.php','🚩'],['Analytics','analytics.php','📈']
];
?>
<div class="admin-shell">
 <aside class="admin-sidebar"><div class="admin-brand"><span>FIBIN</span><small>ADMIN CONTROL</small></div><nav><?php foreach($admin_nav as $n):?><a class="<?=basename($_SERVER['PHP_SELF'])===$n[1]?'active':''?>" href="<?=$n[1]?>"><b><?=$n[2]?></b><span><?=e($n[0])?></span></a><?php endforeach;?></nav><a class="admin-back" href="../dashboard.php">← User dashboard</a></aside>
 <main class="admin-main">
<?php if($m=flash('success')):?><div class="alert success-alert">✓ <?=e($m)?></div><?php endif;?><?php if($m=flash('error')):?><div class="alert error-alert">⚠ <?=e($m)?></div><?php endif;?>
