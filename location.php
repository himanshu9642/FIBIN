<?php $page_title='Nearby Food'; require_once 'includes/header.php';
$items=$pdo->query("SELECT f.*,u.name donor_name FROM food_donations f JOIN users u ON u.id=f.donor_id WHERE f.status='available' AND f.latitude IS NOT NULL AND f.longitude IS NOT NULL ORDER BY f.created_at DESC")->fetchAll();
?>
<section class="page-hero"><span class="kicker">SMART LOCATION</span><h1>Food near you.</h1><p>Allow location access to find nearby donations, calculate distance and open pickup directions.</p></section>
<div class="map-layout"><section class="map-panel"><div class="location-status"><b id="locationTitle">Location not detected</b><div id="locationText" class="muted">Click “Use my location” to find food around you.</div></div><button class="btn primary" id="locateBtn">📍 Use my location</button><a class="btn ghost" href="food.php">Browse all food</a><div id="nearbyList" class="nearby-list"></div></section><section class="map-panel"><div id="foodMap" class="map"></div></section></div>
<script>window.FIBIN_FOOD=<?=json_encode($items)?>;</script>
<?php require_once 'includes/footer.php'; ?>
