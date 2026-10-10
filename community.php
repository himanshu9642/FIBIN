<?php
$page_title='Community';
require_once 'includes/header.php';
require_login();
$me=user()['id'];

if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $action=$_POST['action']??'';
    try{
        if($action==='post'){
            $title=trim($_POST['title']??'');
            $body=trim($_POST['body']??'');
            $cat=trim($_POST['category']??'General');
            $image=upload_image('post_image','community','post_');
            if(strlen($title)<4||strlen($body)<10) throw new RuntimeException('Please add a meaningful title and activity message.');
            $s=$pdo->prepare('INSERT INTO community_posts(user_id,title,body,category,image) VALUES(?,?,?,?,?)');
            $s->execute([$me,$title,$body,$cat,$image]);
            foreach(mention_user_ids($title.' '.$body) as $uid) if($uid!==$me) notify_user($uid,user()['name'].' mentioned you','You were mentioned in a FIBIN community activity.','community.php');
            flash('success','Activity posted to the community.');
        }elseif($action==='comment'){
            $post=(int)$_POST['post_id']; $body=trim($_POST['body']??'');
            if(!$body) throw new RuntimeException('Write a reply first.');
            $pdo->prepare('INSERT INTO community_comments(post_id,user_id,body) VALUES(?,?,?)')->execute([$post,$me,$body]);
            foreach(mention_user_ids($body) as $uid) if($uid!==$me) notify_user($uid,user()['name'].' mentioned you','You were mentioned in a community reply.','community.php');
            $q=$pdo->prepare('SELECT user_id FROM community_posts WHERE id=?');$q->execute([$post]);
            if($uid=$q->fetchColumn()) if($uid!=$me) notify_user($uid,user()['name'].' commented on your activity','Open Community to view the reply.','community.php');
        }elseif($action==='like'){
            $post=(int)$_POST['post_id'];
            $s=$pdo->prepare('SELECT 1 FROM post_likes WHERE post_id=? AND user_id=?');$s->execute([$post,$me]);
            if($s->fetch()){$pdo->prepare('DELETE FROM post_likes WHERE post_id=? AND user_id=?')->execute([$post,$me]);$pdo->prepare('UPDATE community_posts SET likes=GREATEST(likes-1,0) WHERE id=?')->execute([$post]);}
            else{$pdo->prepare('INSERT INTO post_likes(post_id,user_id) VALUES(?,?)')->execute([$post,$me]);$pdo->prepare('UPDATE community_posts SET likes=likes+1 WHERE id=?')->execute([$post]);}
        }elseif($action==='meeting'){
            $title=trim($_POST['meeting_title']??'');$starts=$_POST['starts_at']??'';
            if(strlen($title)<4||!$starts) throw new RuntimeException('Add a meeting title and date/time.');
            $dt=DateTime::createFromFormat('Y-m-d\TH:i',$starts);
            if(!$dt || $dt->getTimestamp()<time()-60) throw new RuntimeException('Choose a future meeting time.');
            $room='FIBIN-'.preg_replace('/[^A-Za-z0-9]+/','-',strtoupper($title)).'-'.bin2hex(random_bytes(4));
            $url='https://meet.jit.si/'.trim($room,'-');
            $pdo->prepare('INSERT INTO community_meetings(host_id,title,meeting_url,starts_at) VALUES(?,?,?,?)')->execute([$me,$title,$url,$dt->format('Y-m-d H:i:s')]);
            flash('success','Meeting created. Share the room with the community.');
        }elseif($action==='delete_post'){
            $post=(int)$_POST['post_id'];
            $q=$pdo->prepare('SELECT user_id FROM community_posts WHERE id=?');$q->execute([$post]);$owner=(int)$q->fetchColumn();
            if($owner===$me || user()['role']==='admin'){$pdo->prepare('DELETE FROM community_posts WHERE id=?')->execute([$post]);flash('success','Activity removed.');}
        }
    }catch(Throwable $e){flash('error',$e->getMessage());}
    redirect('community.php');
}

$posts=$pdo->query("SELECT p.*,u.name,u.username,u.avatar FROM community_posts p JOIN users u ON u.id=p.user_id ORDER BY p.created_at DESC LIMIT 50")->fetchAll();
$comments=$pdo->query("SELECT c.*,u.name,u.username,u.avatar FROM community_comments c JOIN users u ON u.id=c.user_id ORDER BY c.created_at ASC")->fetchAll();
$commentMap=[];foreach($comments as $c)$commentMap[$c['post_id']][]=$c;
$people=$pdo->query("SELECT id,name,username,avatar,role,city,last_seen FROM users ORDER BY (role='admin') DESC, created_at DESC LIMIT 18")->fetchAll();
$meetings=$pdo->query("SELECT m.*,u.name host_name,u.username host_username FROM community_meetings m JOIN users u ON u.id=m.host_id WHERE m.starts_at>=NOW() ORDER BY m.starts_at ASC LIMIT 12")->fetchAll();
?>
<section class="community-hero"><div><span class="kicker">FIBIN COMMUNITY HUB</span><h1>People, activities & live meetings.</h1><p>Share what is happening, find volunteers, mention members, message people privately and create a live meeting for a food drive.</p><div class="hero-actions"><a class="btn primary" href="#create-activity">Post activity</a><a class="btn secondary" href="#meetings">View meetings</a></div></div><div class="community-hero-stats"><div><strong><?=count($people)?></strong><span>active profiles</span></div><div><strong><?=count($posts)?></strong><span>recent activities</span></div><div><strong><?=count($meetings)?></strong><span>upcoming meetings</span></div></div></section>

<div class="community-tabs"><a class="active" href="#feed">Feed</a><a href="#people">People</a><a href="messages.php">Messages</a><a href="groups.php">Groups</a><a href="events.php">Events</a><a href="#meetings">Meetings</a></div>

<div class="community-layout-v2" id="feed"><main>
<?php foreach($posts as $p): ?>
<article class="activity-card">
 <div class="activity-top"><div class="user-line"><span class="avatar-md"><?php if($p['avatar']):?><img src="<?=e($p['avatar'])?>"><?php else:?><?=e(strtoupper(substr($p['name'],0,1)))?><?php endif;?></span><div><b><?=e($p['name'])?></b><small>@<?=e($p['username'])?> · <?=e(time_ago($p['created_at']))?></small></div></div><span class="activity-category"><?=e($p['category'])?></span></div>
 <h2><?=e($p['title'])?></h2><p><?=nl2br(e($p['body']))?></p>
 <?php if(!empty($p['image'])):?><img class="activity-image" src="<?=e(image_url($p['image']))?>" alt="Activity image"><?php endif;?>
 <div class="activity-actions"><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="like"><input type="hidden" name="post_id" value="<?=$p['id']?>"><button class="action-btn">💜 <?=intval($p['likes'])?> Likes</button></form><a class="action-btn" href="messages.php?user=<?=$p['user_id']?>">💬 Message</a><a class="action-btn" target="_blank" href="https://meet.jit.si/FIBIN-CHAT-<?=rawurlencode($p['user_id'].'-'.$me)?>">📹 Quick Meet</a><?php if($p['user_id']==$me || user()['role']==='admin'):?><form method="post" onsubmit="return confirm('Remove this activity?')"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="delete_post"><input type="hidden" name="post_id" value="<?=$p['id']?>"><button class="action-btn danger">Delete</button></form><?php endif;?></div>
 <?php foreach(($commentMap[$p['id']]??[]) as $c):?><div class="reply"><span class="avatar-xs"><?php if($c['avatar']):?><img src="<?=e($c['avatar'])?>"><?php else:?><?=e(strtoupper(substr($c['name'],0,1)))?><?php endif;?></span><div><b><?=e($c['name'])?></b><small>@<?=e($c['username'])?> · <?=e(time_ago($c['created_at']))?></small><p><?=nl2br(e($c['body']))?></p></div></div><?php endforeach;?>
 <form method="post" class="reply-box"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="comment"><input type="hidden" name="post_id" value="<?=$p['id']?>"><input name="body" required placeholder="Reply or mention @username"><button class="mini-btn">Reply</button></form>
</article>
<?php endforeach; if(!$posts):?><div class="empty-state">No activities yet. Be the first person to share an update.</div><?php endif;?>
</main>
<aside>
<div class="side-card" id="people"><div class="side-title"><span><small>NETWORK</small><b>People</b></span><a href="profile.php">My profile</a></div><div class="people-grid-v2"><?php foreach($people as $p):?><div class="person-card-v2"><span class="avatar-md"><?php if($p['avatar']):?><img src="<?=e($p['avatar'])?>"><?php else:?><?=e(strtoupper(substr($p['name'],0,1)))?><?php endif;?></span><b><?=e($p['name'])?></b><small>@<?=e($p['username'])?></small><span class="role-chip"><?=e($p['role'])?></span><?php if($p['id']!=$me):?><div class="person-actions"><a href="messages.php?user=<?=$p['id']?>">Chat</a><a target="_blank" href="https://meet.jit.si/FIBIN-<?=rawurlencode($me.'-'.$p['id'])?>">Meet</a></div><?php endif;?></div><?php endforeach;?></div></div>

<div class="side-card" id="meetings"><div class="side-title"><span><small>LIVE COORDINATION</small><b>Community meetings</b></span><span class="live-dot">● LIVE</span></div><?php foreach($meetings as $m):?><div class="meeting-card"><div class="meeting-icon">📹</div><div><b><?=e($m['title'])?></b><small><?=date('d M Y · h:i A',strtotime($m['starts_at']))?></small><small>Hosted by <?=e($m['host_name'])?></small><div class="meeting-actions"><a target="_blank" href="<?=e($m['meeting_url'])?>">Join meeting</a><button class="copy-meeting" data-url="<?=e($m['meeting_url'])?>">Copy link</button></div></div></div><?php endforeach;if(!$meetings):?><p class="muted">No upcoming meetings. Create one for your next food drive.</p><?php endif;?></div>

<div class="side-card" id="create-activity"><div class="side-title"><span><small>CREATE</small><b>Post an activity</b></span></div><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="post"><label>Activity title<input name="title" required placeholder="e.g. Food drive this Sunday"></label><label>Category<select name="category"><option>General</option><option>Food Drive</option><option>Volunteering</option><option>Success Story</option><option>Announcements</option><option>Safety</option></select></label><label>Message<textarea name="body" rows="5" required placeholder="Share your activity and mention @username if needed..."></textarea></label><label>Photo (optional)<input type="file" name="post_image" accept="image/jpeg,image/png,image/webp"></label><button class="btn primary full-btn">Publish activity</button></form></div>

<div class="side-card"><div class="side-title"><span><small>HOST</small><b>Create a meeting</b></span></div><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="meeting"><label>Meeting title<input name="meeting_title" required placeholder="Volunteer coordination call"></label><label>Date & time<input type="datetime-local" name="starts_at" min="<?=date('Y-m-d\TH:i')?>" required></label><button class="btn secondary full-btn">Create video meeting</button><small class="form-help">Meetings use a unique Jitsi room. Admins and users can host or join.</small></form></div>
</aside></div>
<script>
document.querySelectorAll('.copy-meeting').forEach(btn=>btn.addEventListener('click',async()=>{try{await navigator.clipboard.writeText(btn.dataset.url);btn.textContent='Copied ✓';setTimeout(()=>btn.textContent='Copy link',1400)}catch(e){alert(btn.dataset.url)}}));
</script>
<?php require_once 'includes/footer.php'; ?>
