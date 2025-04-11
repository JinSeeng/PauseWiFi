<?php
$pageTitle = "Carte interactive";
require_once 'includes/header.php';
require_once 'includes/functions.php';

$spots = getAllWifiSpots();
?>

<div class="map-container">
    <div class="container">
        <h1>Carte des spots Wi-Fi à Paris</h1>
        <div class="map-filters">
            <?php include 'templates/search_form.php'; ?>
        </div>
    </div>
    
    <div id="map"></div>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="<?= JS_PATH ?>/map.js"></script>
<script>
    // Passer les données des spots au JS
    const wifiSpots = <?= json_encode($spots) ?>;
    document.addEventListener('DOMContentLoaded', function() {
        initMap(wifiSpots);
    });
</script>

<?php require_once 'includes/footer.php'; ?>