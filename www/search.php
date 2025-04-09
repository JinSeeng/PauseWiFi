<?php
// www/search.php
require_once 'includes/config.php';
require_once 'includes/db.php';

$page_title = "Recherche de spots Wi-Fi";
require_once 'includes/header.php';

$db = new Database();
$conn = $db->getConnection();

// Récupérer les critères de recherche
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$borough = isset($_GET['borough']) ? (int)$_GET['borough'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;
$place_type = isset($_GET['place_type']) ? (int)$_GET['place_type'] : null;

// Construire la requête SQL
$sql = "SELECT s.* FROM wifi_spots s WHERE 1=1";
$params = [];

if (!empty($search_query)) {
    $sql .= " AND (s.name LIKE ? OR s.address LIKE ? OR s.borough LIKE ?)";
    $search_param = "%$search_query%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if ($borough) {
    $sql .= " AND s.postal_code LIKE ?";
    $params[] = "750" . str_pad($borough, 2, '0', STR_PAD_LEFT) . "%";
}

if ($status) {
    $sql .= " AND s.status = ?";
    $params[] = $status;
}

// Ajouter le tri
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
switch ($sort) {
    case 'distance':
        // Nécessite des coordonnées utilisateur
        $sql .= " ORDER BY s.name";
        break;
    case 'borough':
        $sql .= " ORDER BY s.postal_code";
        break;
    default:
        $sql .= " ORDER BY s.name";
}

// Exécuter la requête
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$spots = $stmt->fetchAll();

// Si recherche par type de lieu à proximité, filtrer les résultats
if ($place_type) {
    $filtered_spots = [];
    foreach ($spots as $spot) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM places_of_interest 
                               WHERE type_id = ? 
                               AND SQRT(POW(69.1 * (latitude - ?), 2) + POW(69.1 * (? - longitude) * COS(latitude / 57.3), 2)) < 0.5");
        $stmt->execute([$place_type, $spot['latitude'], $spot['longitude']]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            $filtered_spots[] = $spot;
        }
    }
    $spots = $filtered_spots;
}
?>

<section class="search-section">
    <!-- Formulaire de recherche existant -->
    
    <div class="search-results">
        <h2>Résultats de la recherche</h2>
        
        <div class="results-meta">
            <p><?php echo count($spots); ?> spots trouvés</p>
            <!-- Sélecteur de tri -->
        </div>
        
        <?php if (empty($spots)): ?>
            <div class="no-results">
                <p>Aucun spot Wi-Fi trouvé avec ces critères.</p>
                <a href="search.php" class="btn">Réinitialiser la recherche</a>
            </div>
        <?php else: ?>
            <div class="spots-list">
                <?php foreach ($spots as $spot): ?>
                    <div class="spot-item">
                        <div class="spot-info">
                            <h3><a href="spot.php?id=<?php echo $spot['id']; ?>"><?php echo htmlspecialchars($spot['name']); ?></a></h3>
                            <p class="address"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($spot['address']); ?>, <?php echo htmlspecialchars($spot['postal_code']); ?></p>
                            <p class="status"><i class="fas fa-wifi"></i> <?php echo htmlspecialchars($spot['status']); ?></p>
                            
                            <?php 
                            // Récupérer les lieux à proximité
                            $stmt = $conn->prepare("SELECT p.name, pt.name as type_name 
                                                   FROM places_of_interest p
                                                   JOIN place_types pt ON p.type_id = pt.id
                                                   WHERE SQRT(POW(69.1 * (p.latitude - ?), 2) + POW(69.1 * (? - p.longitude) * COS(p.latitude / 57.3), 2)) < 0.5
                                                   LIMIT 3");
                            $stmt->execute([$spot['latitude'], $spot['longitude']]);
                            $places = $stmt->fetchAll();
                            
                            if (!empty($places)): ?>
                                <p class="places-nearby">
                                    <i class="fas fa-map-pin"></i> À proximité : 
                                    <?php echo implode(', ', array_map(function($place) {
                                        return $place['name'] . ' (' . $place['type_name'] . ')';
                                    }, $places)); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="spot-actions">
                            <a href="map.php?spot=<?php echo $spot['id']; ?>" class="btn btn-outline"><i class="fas fa-map"></i> Voir sur la carte</a>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="api/toggle_favorite.php?spot_id=<?php echo $spot['id']; ?>&action=add" class="btn"><i class="far fa-heart"></i> Favoris</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>