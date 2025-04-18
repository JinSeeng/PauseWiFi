let map;
let markers;
let userMarker = null;

function initMap() {
    const defaultCoords = [48.8566, 2.3522];
    const defaultZoom = 13;
    
    map = L.map('map').setView(defaultCoords, defaultZoom);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Chargement initial
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

window.updateMap = function(spots) {
    if (markers) {
        map.removeLayer(markers);
    }

    markers = L.markerClusterGroup();
    
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
    
    if (spots.length > 0) {
        if (spots.length === 1) {
            map.setView([spots[0].latitude, spots[0].longitude], 15);
        } else {
            const bounds = markers.getBounds();
            // Si on a un marqueur utilisateur, on l'inclut dans le zoom
            if (userMarker) {
                bounds.extend(userMarker.getLatLng());
            }
            map.fitBounds(bounds);
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('map')) {
        initMap();
    }
    
    if (document.getElementById('detail-map') && typeof spotData !== 'undefined') {
        initDetailMap();
    }
});

function initSpotDetailMap() {
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

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('map')) {
        initMap();
    }
    
    if (document.getElementById('detail-map') && typeof spotData !== 'undefined') {
        initSpotDetailMap();
    }
});
