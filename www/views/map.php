<?php require_once __DIR__ . '/partials/header.php'; ?>

<div class="map-page">
    <div class="map-page__filters">
        <form id="search-form" class="map-page__search-form">
            <input type="text" name="search" class="map-page__search-input" 
                   placeholder="Rechercher par nom ou adresse..." 
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            
            <div class="map-page__filter-row">
                <select name="site_type" class="map-page__filter">
                    <option value="all">Tous les types</option>
                    <option value="Bibliothèque" <?= ($_GET['site_type'] ?? '') === 'Bibliothèque' ? 'selected' : '' ?>>Bibliothèque</option>
                    <option value="Parc" <?= ($_GET['site_type'] ?? '') === 'Parc' ? 'selected' : '' ?>>Parc</option>
                    <option value="Centre sportif" <?= ($_GET['site_type'] ?? '') === 'Centre sportif' ? 'selected' : '' ?>>Centre sportif</option>
                    <option value="Mairie" <?= ($_GET['site_type'] ?? '') === 'Mairie' ? 'selected' : '' ?>>Mairie</option>
                    <option value="Musée" <?= ($_GET['site_type'] ?? '') === 'Musée' ? 'selected' : '' ?>>Musée</option>
                    <option value="Hotel" <?= ($_GET['site_type'] ?? '') === 'Hotel' ? 'selected' : '' ?>>Hôtel</option>
                    <option value="Autre" <?= ($_GET['site_type'] ?? '') === 'Autre' ? 'selected' : '' ?>>Autre</option>
                </select>
                
                <select name="arrondissement" class="map-page__filter">
                    <option value="all">Tous les arrondissements</option>
                    <?php for ($i = 1; $i <= 20; $i++): ?>
                        <option value="<?= $i ?>" <?= isset($_GET['arrondissement']) && $_GET['arrondissement'] == $i ? 'selected' : '' ?>>
                            <?= $i ?>e Arrondissement 
                        </option>
                    <?php endfor; ?>
                </select>
                
                <select name="status" class="map-page__filter">
                    <option value="all">Tous les statuts</option>
                    <option value="Opérationnel" <?= ($_GET['status'] ?? '') === 'Opérationnel' ? 'selected' : '' ?>>Opérationnel</option>
                    <option value="Fermé pour travaux" <?= ($_GET['status'] ?? '') === 'Fermé pour travaux' ? 'selected' : '' ?>>Fermé pour travaux</option>
                    <option value="En déploiement" <?= ($_GET['status'] ?? '') === 'En déploiement' ? 'selected' : '' ?>>En déploiement</option>
                    <option value="En étude" <?= ($_GET['status'] ?? '') === 'En étude' ? 'selected' : '' ?>>En étude</option>
                </select>
            </div>
            
            <button type="submit" class="map-page__search-btn">Rechercher</button>
            <button type="button" id="locate-me" class="map-page__locate-btn">
                <i class="fas fa-location-arrow"></i> Me localiser
            </button>
        </form>
    </div>

    <div class="map-page__container">
        <div id="map" class="map-page__map"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const locateBtn = document.getElementById('locate-me');
    let userMarker = null;
    
    locateBtn.addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert("La géolocalisation n'est pas supportée par votre navigateur");
            return;
        }
        
        locateBtn.disabled = true;
        locateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Localisation...';
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const userLatLng = [
                    position.coords.latitude,
                    position.coords.longitude
                ];
                
                // Supprimer l'ancien marqueur s'il existe
                if (userMarker) {
                    map.removeLayer(userMarker);
                }
                
                // Centrer la carte sur la position de l'utilisateur
                map.setView(userLatLng, 15);
                
                // Ajouter un marqueur pour la position de l'utilisateur
                userMarker = L.marker(userLatLng, {
                    icon: L.divIcon({
                        className: 'map-page__user-marker',
                        html: '<i class="fas fa-user"></i>',
                        iconSize: [30, 30]
                    })
                }).addTo(map);
                
                // Mettre à jour la recherche avec les spots à proximité
                const searchForm = document.getElementById('search-form');
                const formData = new FormData(searchForm);
                formData.append('latitude', userLatLng[0]);
                formData.append('longitude', userLatLng[1]);
                
                fetch('/?page=map-search', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    updateMap(data);
                    locateBtn.innerHTML = '<i class="fas fa-location-arrow"></i> Me localiser';
                    locateBtn.disabled = false;
                });
            },
            function(error) {
                let errorMessage;
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = "Vous avez refusé la géolocalisation";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = "Position indisponible";
                        break;
                    case error.TIMEOUT:
                        errorMessage = "La requête a expiré";
                        break;
                    default:
                        errorMessage = "Erreur inconnue";
                }
                
                alert("Erreur de géolocalisation : " + errorMessage);
                locateBtn.innerHTML = '<i class="fas fa-location-arrow"></i> Me localiser';
                locateBtn.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });
});
</script>

<script src="/assets/js/map.js"></script>
<script src="/assets/js/search.js"></script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>