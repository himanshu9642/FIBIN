<?php $page_title='Home'; require_once 'includes/header.php';
$stats = $pdo->query("SELECT (SELECT COUNT(*) FROM food_donations) donations, (SELECT COUNT(*) FROM users WHERE role IN ('donor','receiver')) members, (SELECT COUNT(*) FROM food_donations WHERE status='available') available")->fetch();
$latest = $pdo->query("SELECT f.*, u.name donor_name FROM food_donations f JOIN users u ON u.id=f.donor_id WHERE f.status='available' ORDER BY f.created_at DESC LIMIT 6")->fetchAll();
?>
<section class="hero hero-refresh">
  <div class="hero-copy"><div class="eyebrow"><span class="pulse"></span> FOOD SHARING, REIMAGINED</div>
    <div class="hero-label">A little extra can mean everything</div>
    <h1>Share food.<br><span>Spread hope.</span></h1>
    <p class="hero-lead">Good food deserves a destination, not a dustbin. FIBIN brings donors, volunteers, NGOs and neighbours together to help surplus food reach people who can use it.</p>
    <div class="hero-actions"><a class="btn primary" href="donate.php">Donate food <span>→</span></a><a class="btn ghost" href="food.php">Explore available food</a><a class="btn ghost" href="about.php">Our story</a></div>
    <div class="hero-trust"><span>✓ Community-led</span><span>✓ Simple to join</span><span>✓ Impact that matters</span></div>
    <div class="mini-proof"><div><strong><?= (int)$stats['donations'] ?>+</strong><small>food listings</small></div><div><strong><?= (int)$stats['members'] ?>+</strong><small>community members</small></div><div><strong><?= (int)$stats['available'] ?></strong><small>available now</small></div></div>
  </div>
  <div class="hero-visual hero-visual-refresh"><div class="hero-art-card hero-art-refresh"><div class="hero-art-orb orb-a"></div><div class="hero-art-orb orb-b"></div><img src="assets/images/hero-donation.svg" alt="Illustration of people sharing food"><div class="hero-floating-card"><strong>1 meal = 1 smile</strong><span>Every donation can create impact.</span></div><div class="hero-side-note"><span class="hero-note-icon">♡</span><div><b>Share a little kindness</b><small>Make surplus food count</small></div></div></div></div>
</section>
<section class="home-promise-strip"><div><span>01</span><b>Rescue good food</b><small>Keep safe surplus food in use.</small></div><div><span>02</span><b>Connect locally</b><small>Bring people and organisations together.</small></div><div><span>03</span><b>Create real impact</b><small>Turn small actions into shared progress.</small></div></section>
<section class="founder-section">
  <div class="founder-art"><img class="founder-photo" src="assets/images/founder-himanshu.png" alt="Himanshu Kumar, founder of FIBIN"><div class="founder-art-caption"><span class="founder-dot"></span><span>THE IDEA BEHIND FIBIN</span></div><div class="founder-art-ring ring-one"></div><div class="founder-art-ring ring-two"></div></div>
  <div class="founder-copy"><span class="kicker">A NOTE FROM THE FOUNDER</span><h2>Meet Himanshu Kumar<span class="founder-period">.</span></h2><div class="founder-role">Founder &amp; Project Creator, FIBIN</div>
    <p>Himanshu Kumar is the creator of FIBIN, a community-focused food-sharing project built around one simple belief: surplus food can still have a meaningful purpose when the right people can find it in time.</p>
    <p>Through FIBIN, the aim is to make food donation easier to discover and coordinate by bringing donors, volunteers, NGOs and receivers into one accessible digital space. The project puts practical technology in service of a human need—helping communities share resources with care and dignity.</p>
    <div class="founder-values"><div><span>✦</span><div><b>People before waste</b><small>Make sharing easier and more approachable.</small></div></div><div><span>⌁</span><div><b>Technology with purpose</b><small>Use digital tools to connect local support.</small></div></div></div>
    <a class="btn primary" href="about.php">Read FIBIN’s story →</a><p class="founder-edit-note">Founder profile can be personalised with a real photo, education, experience and a message from Himanshu.</p>
  </div>
</section>
<section class="innovation-showcase">
  <div class="innovation-visual">
    <div class="innovation-main-card">
      <img src="assets/images/fibin-impact-card.png" alt="FIBIN one meal one smile" loading="lazy">
      <div class="innovation-glass-card"><span>LIVE IMPACT</span><strong>Every meal can become a moment of hope.</strong><small>Donate • Connect • Share</small></div>
    </div>
    <div class="innovation-float innovation-float-one"><b>01</b><span>Food rescue</span></div>
    <div class="innovation-float innovation-float-two"><b>02</b><span>People connect</span></div>
  </div>
  <div class="innovation-copy">
    <span class="kicker">THE FIBIN EXPERIENCE</span>
    <h2>A food donation website that feels like a community.</h2>
    <p>Discover food, meet people, create impact and stay connected — all through one modern platform designed around real human action.</p>
    <div class="innovation-pills"><span>⚡ Fast discovery</span><span>🤝 Human connection</span><span>📍 Nearby impact</span><span>🔔 Live updates</span></div>
    <a class="btn primary" href="community.php">Explore the experience →</a>
  </div>
</section>
<section class="section"><div class="section-heading"><div><span class="kicker">HOW IT WORKS</span><h2>One platform. Three simple moves.</h2></div><p>Designed to make every extra meal count.</p></div>
<div class="steps"><article><span>01</span><h3>Donate</h3><p>Post safe surplus food with quantity, pickup details and an expiry time.</p></article><article><span>02</span><h3>Connect</h3><p>People and community organizations discover available food near them.</p></article><article><span>03</span><h3>Share</h3><p>Requests are accepted, pickup is coordinated and food reaches its destination.</p></article></div></section>
<section class="section soft"><div class="section-heading"><div><span class="kicker">LIVE LISTINGS</span><h2>Food waiting to be shared</h2></div><a class="text-link" href="food.php">View all →</a></div>
<div class="food-grid"><?php foreach($latest as $item): ?><article class="food-card"><div class="food-image-wrap"><img class="food-image" src="<?=e(image_url($item['image']))?>" alt="<?=e($item['title'])?>"><span class="food-image-badge">FIBIN</span></div><div class="food-card-body"><div class="row"><span class="badge success">Available</span><span class="muted">#<?= (int)$item['id'] ?></span></div><h3><?= e($item['title']) ?></h3><p><?= e($item['description'] ?: 'Fresh surplus food ready for pickup.') ?></p><div class="food-meta"><span>📦 <?= (int)$item['quantity'] ?> <?= e($item['unit']) ?></span><span>📍 <?= e($item['location']) ?></span></div><a class="card-link" href="food.php?id=<?= (int)$item['id'] ?>">View details →</a></div></article><?php endforeach; if (!$latest): ?><div class="empty-state full">No food has been listed yet. Be the first donor!</div><?php endif; ?></div></section>

<section class="people-gallery-section">
  <div class="section-heading"><div><span class="kicker">PEOPLE IN ACTION</span><h2>Real people. Real sharing. Real impact.</h2></div><p>See how food sharing brings volunteers, donors and communities together.</p></div>
  <div class="people-gallery">
    <figure class="gallery-card gallery-large"><img src="https://www.pahchanfoundation.org/img/BTB.jpg" alt="Volunteers serving food in a community" loading="lazy"><figcaption><strong>Serving meals together</strong><span>Community food distribution</span></figcaption></figure>
    <figure class="gallery-card"><img src="https://www.nexzenfoundation.in/images/hero_social.png" alt="Volunteer handing food support to a community member" loading="lazy"><figcaption><strong>Sharing essentials</strong><span>People helping people</span></figcaption></figure>
    <figure class="gallery-card"><img src="https://files.globalgiving.org/pfil/52205/pict_featured_jumbo.jpg?t=1620052005000" alt="Food support being distributed in India" loading="lazy"><figcaption><strong>Care in action</strong><span>Food relief outreach</span></figcaption></figure>
    <figure class="gallery-card"><img src="https://hemkuntfoundation.com/storage/2024/07/Rectangle-286.webp" alt="Volunteer serving hot food" loading="lazy"><figcaption><strong>Every plate matters</strong><span>Local food seva</span></figcaption></figure>
  </div>
  <div class="gallery-note">Photos shown for the community showcase. <a href="https://www.pahchanfoundation.org/" target="_blank" rel="noopener">Pahchan Foundation</a> · <a href="https://www.nexzenfoundation.in/" target="_blank" rel="noopener">NexZen Foundation</a> · <a href="https://www.globalgiving.org/" target="_blank" rel="noopener">GlobalGiving</a> · <a href="https://hemkuntfoundation.com/" target="_blank" rel="noopener">Hemkunt Foundation</a></div>
</section>
<section class="impact-showcase"><div class="impact-art"><img src="assets/images/fibin-impact-card.png" alt="FIBIN one meal one smile"></div><div class="impact-copy"><span class="kicker">COMMUNITY IMPACT</span><h2>Food is more than a meal. It is a connection.</h2><p>FIBIN brings donors, volunteers and local communities together so good food reaches people instead of landfills.</p><div class="impact-points"><div><b>20+</b><span>demo food listings</span></div><div><b>3×</b><span>ways to get involved</span></div><div><b>24/7</b><span>community access</span></div></div><a class="btn primary" href="community.php">Explore community →</a></div></section>
<section class="section"><div class="section-heading"><div><span class="kicker">ADVANCED FEATURES</span><h2>Built for real-world food sharing.</h2></div><a class="text-link" href="location.php">Try smart nearby matching →</a></div><div class="feature-grid"><article class="feature-card"><div class="feature-icon">📍</div><h3>Location intelligence</h3><p>Use browser GPS to discover nearby food and open pickup directions without manually searching every listing.</p></article><article class="feature-card"><div class="feature-icon">🤝</div><h3>Community hub</h3><p>Post food-drive announcements, safety tips, volunteer opportunities and impact stories with comments and likes.</p></article><article class="feature-card"><div class="feature-icon">🔔</div><h3>Smart notifications</h3><p>Keep request updates, approvals and important community activity in one notification center.</p></article></div></section><section class="impact-banner"><div><span class="kicker">OUR MISSION</span><h2>Less waste. More dignity. Stronger communities.</h2><p>FIBIN turns a simple act of sharing into measurable social impact.</p></div><a class="btn primary" href="register.php">Become a FIBIN member →</a></section>
<?php require_once 'includes/footer.php'; ?>
