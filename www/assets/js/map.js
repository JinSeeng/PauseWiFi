let map;
let markers;
let userMarker = null;

// Initialise la carte principale
function initMap() {
    const defaultCoords = [48.8566, 2.3522]; // Coordonnées par défaut (Paris)
    const defaultZoom = 13;
    
    // Crée la carte Leaflet
    map = L.map('map').setView(defaultCoords, defaultZoom);
    
    // Ajoute la couche de tuiles OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributeurs'
    }).addTo(map);
    
    // Charge les données initiales si des paramètres existent dans l'URL
    const params = new URLSearchParams(window.location.search);
    if (params.toString()) {
        fetch(`/?page=search&${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            updateMap(data);
        });
    }
}

// Met à jour la carte avec les spots
window.updateMap = function(spots) {
    // Supprime les marqueurs existants
    if (markers) {
        map.removeLayer(markers);
    }

    // Crée un groupe de marqueurs
    markers = L.markerClusterGroup();
    
    // Ajoute chaque spot comme marqueur
    spots.forEach(spot => {
        const marker = L.marker([spot.latitude, spot.longitude])
            .bindPopup(`
                <h3>${spot.site_name}</h3>
                <p>${spot.address}</p>
                <p>Arrondissement ${spot.arrondissement}</p>
                ${spot.distance ? `<p>À ${Math.round(spot.distance * 1000)} mètres</p>` : ''}
                <p>Status: ${spot.status}</p>
                <a href="/?page=spot&id=${spot.id}" class="btn-details">Voir détails</a>
            `);
        
        markers.addLayer(marker);
    });
    
    map.addLayer(markers);
    
    // Ajuste la vue de la carte
    if (spots.length > 0) {
        if (spots.length === 1) {
            // Zoom sur un seul spot
            map.setView([spots[0].latitude, spots[0].longitude], 15);
        } else {
            // Zoom pour voir tous les spots
            const bounds = markers.getBounds();
            if (userMarker) {
                bounds.extend(userMarker.getLatLng());
            }
            map.fitBounds(bounds);
        }
    }
}

// Initialise la carte de détail d'un spot
function initSpotDetailMap() {
    const mapElement = document.getElementById('detail-map');
    
    if (!mapElement || !spotData) return;
    
    // Crée une carte centrée sur le spot
    const map = L.map(mapElement).setView([spotData.latitude, spotData.longitude], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributeurs'
    }).addTo(map);

    // Ajoute le marqueur du spot
    const marker = L.marker([spotData.latitude, spotData.longitude])
        .addTo(map)
        .bindPopup(`
            <h3>${spotData.site_name}</h3>
            <p>${spotData.address}</p>
        `)
        .openPopup();
    
    // Ajoute un cercle autour du spot
    L.circle([spotData.latitude, spotData.longitude], {
        color: '#0066cc',
        fillColor: '#0066cc',
        fillOpacity: 0.1,
        radius: 100
    }).addTo(map);
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('map')) {
        initMap();
    }
    
    if (document.getElementById('detail-map') && typeof spotData !== 'undefined') {
        initSpotDetailMap();
    }
});