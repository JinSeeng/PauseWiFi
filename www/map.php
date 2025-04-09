<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$page_title = "Carte des spots Wi-Fi";
require_once 'includes/header.php';
?>

<section class="map-section">
    <div class="container">
        <h1>Carte des spots Wi-Fi</h1>
        
        <div class="map-container">
            <div class="map-filters">
                <div class="filter-group">
                    <label for="filter-borough">Arrondissement</label>
                    <select id="filter-borough">
                        <option value="">Tous</option>
                        <?php for ($i = 1; $i <= 20; $i++): ?>
                            <option value="<?php echo $i; ?>">750<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="filter-status">Statut</label>
                    <select id="filter-status">
                        <option value="">Tous</option>
                        <option value="operational">Opérationnel</option>
                        <option value="closed">Fermé</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="filter-type">Type de lieu</label>
                    <select id="filter-type">
                        <option value="">Tous</option>
                        <option value="library">Bibliothèque</option>
                        <option value="park">Café</option>
                        <option value="park">Cinéma</option>
                        <option value="museum">Musée</option>
                        <option value="park">Parc/Jardin</option>
                        
                    </select>
                </div>
                
                <button id="reset-filters" class="btn btn-outline">Réinitialiser</button>
            </div>
            
            <div id="map" style="height: 600px;"></div>
        </div>
    </div>
</section>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation de la carte
    const map = L.map('map').setView([48.8566, 2.3522], 13);
    
    // Ajout du fond de carte
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Ici on ajoutera les marqueurs des spots Wi-Fi
    // Pour l'instant, on simule quelques marqueurs
    
    // Exemple de marqueur
    const marker = L.marker([48.8615, 2.3442]).addTo(map)
        .bindPopup("<b>Bibliothèque François Truffaut</b><br>Wi-Fi gratuit<br><a href='spot.php?id=1'>Plus d'infos</a>");
    
    // Géolocalisation
    map.locate({setView: true, maxZoom: 16});
    
    function onLocationFound(e) {
        const radius = e.accuracy / 2;
        
        L.marker(e.latlng).addTo(map)
            .bindPopup("Vous êtes ici").openPopup();
            
        L.circle(e.latlng, radius).addTo(map);
    }
    
    map.on('locationfound', onLocationFound);
    
    function onLocationError(e) {
        alert("Impossible de déterminer votre position : " + e.message);
    }
    
    map.on('locationerror', onLocationError);
    
    // Gestion des filtres
    document.getElementById('reset-filters').addEventListener('click', function() {
        document.getElementById('filter-borough').value = '';
        document.getElementById('filter-status').value = '';
        document.getElementById('filter-type').value = '';
        // Ici on réinitialisera les marqueurs sur la carte
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>