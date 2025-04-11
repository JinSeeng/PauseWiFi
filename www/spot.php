<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$spotId = (int)$_GET['id'];
$spot = getWifiSpotById($spotId);

if (!$spot) {
    header('Location: 404.php');
    exit;
}

$pageTitle = $spot['nom_du_site'];
?>

<section class="spot-details">
    <div class="container">
        <div class="spot-header">
            <h1><?= htmlspecialchars($spot['nom_du_site']) ?></h1>
            <div class="spot-meta">
                <span class="arrondissement">Arrondissement: <?= $spot['code_postal'] ?></span>
                <span class="status">Statut: <?= $spot['etat'] ?></span>
            </div>
        </div>
        
        <div class="spot-content">
            <div class="spot-info">
                <div class="address">
                    <h2>Adresse</h2>
                    <p><?= htmlspecialchars($spot['adresse']) ?>, <?= $spot['code_postal'] ?> Paris</p>
                </div>
                
                <div class="coordinates">
                    <h2>Coordonnées</h2>
                    <p>Latitude: <?= $spot['latitude'] ?></p>
                    <p>Longitude: <?= $spot['longitude'] ?></p>
                </div>
                
                <div class="actions">
                    <?php if (isLoggedIn()): ?>
                        <button class="btn-favorite" data-spot-id="<?= $spot['id'] ?>">
                            <?= isFavorite($spot['id']) ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>
                        </button>
                    <?php endif; ?>
                    <a href="map.php?lat=<?= $spot['latitude'] ?>&lng=<?= $spot['longitude'] ?>" class="btn-view-on-map">
                        Voir sur la carte
                    </a>
                </div>
            </div>
            
            <div class="spot-map">
                <div id="mini-map"></div>
            </div>
        </div>
        
        <div class="nearby-places">
            <h2>Lieux d'intérêt à proximité</h2>
            <div class="places-list">
                <!-- Les lieux à proximité seront chargés via AJAX -->
                <div class="loading">Chargement des lieux à proximité...</div>
            </div>
        </div>
    </div>
</section>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser la mini-carte
        const miniMap = L.map('mini-map').setView(
            [<?= $spot['latitude'] ?>, <?= $spot['longitude'] ?>], 
            15
        );
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(miniMap);
        
        L.marker([<?= $spot['latitude'] ?>, <?= $spot['longitude'] ?>])
            .addTo(miniMap)
            .bindPopup('<?= addslashes($spot['nom_du_site']) ?>');
        
        // Charger les lieux à proximité
        loadNearbyPlaces(<?= $spot['latitude'] ?>, <?= $spot['longitude'] ?>);
    });
    
    function loadNearbyPlaces(lat, lng) {
        fetch(`api/nearby_places.php?lat=${lat}&lng=${lng}`)
            .then(response => response.json())
            .then(places => {
                const container = document.querySelector('.places-list');
                container.innerHTML = '';
                
                if (places.length === 0) {
                    container.innerHTML = '<p>Aucun lieu d\'intérêt trouvé à proximité.</p>';
                    return;
                }
                
                places.forEach(place => {
                    const placeElement = document.createElement('div');
                   