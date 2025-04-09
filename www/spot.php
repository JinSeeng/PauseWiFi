<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Récupérer l'ID du spot depuis l'URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$spot_id = (int)$_GET['id'];

$db = new Database();
$conn = $db->getConnection();

// Récupérer les informations du spot
$stmt = $conn->prepare("SELECT * FROM wifi_spots WHERE id = ?");
$stmt->execute([$spot_id]);
$spot = $stmt->fetch();

if (!$spot) {
    header("Location: index.php");
    exit();
}

// Vérifier si le spot est dans les favoris de l'utilisateur
$is_favorite = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND wifi_spot_id = ?");
    $stmt->execute([$_SESSION['user_id'], $spot_id]);
    $is_favorite = $stmt->fetch() !== false;
}

$page_title = $spot['name'];
require_once 'includes/header.php';
?>

<section class="spot-details">
    <div class="container">
        <div class="spot-header">
            <h1><?php echo htmlspecialchars($spot['name']); ?></h1>
            <div class="spot-meta">
                <span class="status <?php echo strtolower(str_replace(' ', '-', $spot['status'])); ?>">
                    <i class="fas fa-wifi"></i> <?php echo htmlspecialchars($spot['status']); ?>
                </span>
                <span class="access-points">
                    <i class="fas fa-network-wired"></i> <?php echo $spot['access_points']; ?> borne<?php echo $spot['access_points'] > 1 ? 's' : ''; ?>
                </span>
                <span class="borough">
                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($spot['borough']); ?>
                </span>
            </div>
        </div>
        
        <div class="spot-content">
            <div class="spot-main">
                <div class="spot-image">
                    <img src="https://maps.googleapis.com/maps/api/streetview?size=800x400&location=<?php echo $spot['latitude']; ?>,<?php echo $spot['longitude']; ?>&fov=90&key=YOUR_API_KEY" alt="<?php echo htmlspecialchars($spot['name']); ?>">
                </div>
                
                <div class="spot-description">
                    <h2>Description</h2>
                    <p><?php echo !empty($spot['description']) ? htmlspecialchars($spot['description']) : 'Aucune description disponible pour ce spot.'; ?></p>
                    
                    <div class="spot-address">
                        <h3>Adresse</h3>
                        <p><?php echo htmlspecialchars($spot['address']); ?>, <?php echo htmlspecialchars($spot['postal_code']); ?> Paris</p>
                    </div>
                    
                    <div class="spot-actions">
                        <button class="btn btn-large favorite-btn <?php echo $is_favorite ? 'active' : ''; ?>" data-spot-id="<?php echo $spot['id']; ?>">
                            <i class="<?php echo $is_favorite ? 'fas' : 'far'; ?> fa-heart"></i> <?php echo $is_favorite ? 'Retirer des favoris' : 'Ajouter aux favoris'; ?>
                        </button>
                        <a href="map.php?spot=<?php echo $spot['id']; ?>" class="btn btn-large btn-outline"><i class="fas fa-map-marked-alt"></i> Voir sur la carte</a>
                    </div>
                </div>
            </div>
            
            <div class="spot-sidebar">
                <div class="nearby-places">
                    <h2>Lieux à proximité</h2>
                    <div id="nearby-places-list">
                        <p><i class="fas fa-spinner fa-spin"></i> Chargement des lieux à proximité...</p>
                    </div>
                </div>
                
                <div class="spot-map">
                    <div id="mini-map" style="height: 300px;"></div>
                    <input type="hidden" id="spot-lat" value="<?php echo $spot['latitude']; ?>">
                    <input type="hidden" id="spot-lon" value="<?php echo $spot['longitude']; ?>">
                    <input type="hidden" id="spot-name" value="<?php echo htmlspecialchars($spot['name']); ?>">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation de la mini-carte
    const spotLat = parseFloat(document.getElementById('spot-lat').value);
    const spotLon = parseFloat(document.getElementById('spot-lon').value);
    
    const map = L.map('mini-map').setView([spotLat, spotLon], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    L.marker([spotLat, spotLon]).addTo(map)
        .bindPopup(document.getElementById('spot-name').value)
        .openPopup();

    // Charger les lieux à proximité
    fetch(`api/get_nearby_places.php?spot_id=<?php echo $spot['id']; ?>`)
        .then(response => response.json())
        .then(places => {
            const placesList = document.getElementById('nearby-places-list');
            
            if (places.length === 0) {
                placesList.innerHTML = '<p>Aucun lieu à proximité trouvé.</p>';
                return;
            }
            
            let html = '';
            places.forEach(place => {
                html += `
                    <div class="place-item">
                        <h3><a href="#">${place.name}</a></h3>
                        <p class="distance"><i class="fas fa-walking"></i> ${Math.round(place.distance / 100 * 1.3)} min (${place.distance}m)</p>
                    </div>
                `;
            });
            
            placesList.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('nearby-places-list').innerHTML = 
                '<p class="text-danger">Erreur lors du chargement des lieux à proximité.</p>';
        });
});
</script>

<?php require_once 'includes/footer.php'; ?>