<?php
// www/favorites.php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';

checkAuthentication();

$page_title = "Mes favoris";
require_once 'includes/header.php';

$db = new Database();
$conn = $db->getConnection();

// Récupérer les favoris de l'utilisateur
$stmt = $conn->prepare("SELECT s.* FROM favorites f 
                       JOIN wifi_spots s ON f.wifi_spot_id = s.id 
                       WHERE f.user_id = ? 
                       ORDER BY f.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$favorites = $stmt->fetchAll();
?>

<section class="favorites-section">
    <div class="container">
        <h1>Mes spots Wi-Fi favoris</h1>
        
        <?php if (isset($_GET['removed'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Le spot a été retiré de vos favoris
            </div>
        <?php endif; ?>
        
        <div class="favorites-list">
            <?php if (empty($favorites)): ?>
                <div class="no-favorites">
                    <div class="empty-state">
                        <i class="fas fa-heart-broken"></i>
                        <h3>Aucun favoris pour le moment</h3>
                        <p>Ajoutez des spots Wi-Fi à vos favoris pour les retrouver facilement.</p>
                        <a href="search.php" class="btn">Rechercher des spots</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($favorites as $spot): ?>
                    <div class="favorite-item">
                        <div class="favorite-info">
                            <h3><a href="spot.php?id=<?php echo $spot['id']; ?>"><?php echo htmlspecialchars($spot['name']); ?></a></h3>
                            <p class="address"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($spot['address']); ?>, <?php echo htmlspecialchars($spot['postal_code']); ?> Paris</p>
                            <p class="status"><i class="fas fa-wifi"></i> <?php echo htmlspecialchars($spot['status']); ?></p>
                        </div>
                        <div class="favorite-actions">
                            <a href="map.php?spot=<?php echo $spot['id']; ?>" class="btn btn-outline"><i class="fas fa-map"></i> Voir sur la carte</a>
                            <a href="remove_favorite.php?id=<?php echo $spot['id']; ?>" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Retirer</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>