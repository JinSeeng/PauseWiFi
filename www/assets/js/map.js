// assets/js/map.js

let map;
let markers;

function initMap() {
    // Coordonnées par défaut (centre de Paris)
    const defaultCoords = [48.8566, 2.3522];
    const defaultZoom = 13;
    
    // Initialiser la carte
    map = L.map('map').setView(defaultCoords, defaultZoom);
    
    // Ajouter le calque OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
}

function updateMap(spots) {
    // Supprimer les marqueurs existants
    if (markers) {
        map.removeLayer(markers);
    }

    // Créer un nouveau groupe de marqueurs
    markers = L.markerClusterGroup();
    
    // Ajouter les marqueurs pour chaque spot
    spots.forEach(spot => {
        const marker = L.marker([spot.latitude, spot.longitude])
            .bindPopup(`
                <h3>${spot.site_name}</h3>
                <p>${spot.address}</p>
                <p>Arrondissement ${spot.arrondissement}</p>
                <p>Status: ${spot.status}</p>
                <a href="/spot/${spot.id}" class="btn-details">Voir détails</a>
            `);
        
        markers.addLayer(marker);
    });
    
    // Ajouter les marqueurs à la carte
    map.addLayer(markers);
    
    // Ajuster la vue pour afficher tous les marqueurs
    if (spots.length > 0) {
        if (spots.length === 1) {
            map.setView([spots[0].latitude, spots[0].longitude], 15);
        } else {
            map.fitBounds(markers.getBounds());
        }
    }
}

// Initialiser la carte de détail si nécessaire
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('detail-map') && typeof spotData !== 'undefined') {
        initDetailMap();
    }
});

function initDetailMap() {
    const mapElement = document.getElementById('detail-map');
    
    if (!mapElement || !spotData) return;
    
    const map = L.map(mapElement).setView([spotData.latitude, spotData.longitude], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    const marker = L.marker([spotData.latitude, spotData.longitude])
        .addTo(map)
        .bindPopup(`
            <h3>${spotData.site_name}</h3>
            <p>${spotData.address}</p>
        `)
        .openPopup();
    
    L.circle([spotData.latitude, spotData.longitude], {
        color: '#0066cc',
        fillColor: '#0066cc',
        fillOpacity: 0.1,
        radius: 100
    }).addTo(map);
}