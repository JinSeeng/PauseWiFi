<?php
require_once __DIR__ . '/partials/header.php';

// Vérifier si le spot existe
if (!isset($spot)) {
    header('Location: /?page=not_found');
    exit;
}

// Masquer les favoris pour les admins
$showFavorites = isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin';

// Récupérer 3 spots à proximité
$nearbySpots = $wifiSpotModel->getNearbySpots(
    $spot['latitude'],
    $spot['longitude'],
    $spot['id'], // Exclure le spot actuel
    1, // Rayon de 1km
    3  // Limite à 3 résultats
);
?>

<div class="spot-detail">
    <div class="spot-detail__header">
        <h1 class="spot-detail__title"><?= htmlspecialchars($spot['site_name']) ?></h1>
        <p class="spot-detail__status spot-detail__status--<?= strtolower(str_replace(' ', '-', $spot['status'])) ?>">
            <?= $spot['status'] ?>
        </p>
    </div>
    
    <div class="spot-detail__content">
        <div class="spot-detail__info">
            <div class="spot-detail__section">
                <h2 class="spot-detail__section-title">Adresse</h2>
                <p class="spot-detail__text"><?= htmlspecialchars($spot['address']) ?></p>
                <p class="spot-detail__text"><?= htmlspecialchars($spot['postal_code']) ?> Paris</p>
                <p class="spot-detail__text"><?= $spot['arrondissement'] ?>e Arrondissement</p>
            </div>
            
            <div class="spot-detail__section">
                <h2 class="spot-detail__section-title">Informations</h2>
                <p class="spot-detail__text"><strong>Type :</strong> <?= htmlspecialchars($spot['site_type']) ?></p>
                <p class="spot-detail__text"><strong>Nombre de bornes :</strong> <?= $spot['num_bornes'] ?></p>
                <?php if (!empty($spot['site_code'])): ?>
                    <p class="spot-detail__text"><strong>Code site :</strong> <?= htmlspecialchars($spot['site_code']) ?></p>
                <?php endif; ?>
            </div>
            
            <div class="spot-detail__section">
                <h2 class="spot-detail__section-title">Coordonnées</h2>
                <div id="detail-map" class="spot-detail__map"></div>
                <p class="spot-detail__text">
                    <strong>Coordonnées :</strong> 
                    <?= htmlspecialchars($spot['latitude']) ?>, <?= htmlspecialchars($spot['longitude']) ?>
                </p>
            </div>
        </div>
        
        <div class="spot-detail__actions">
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin'): ?>
                <button class="spot-detail__favorite-btn <?= $isFavorite ? 'spot-detail__favorite-btn--active' : '' ?>" 
                        data-spot-id="<?= $spot['id'] ?>"
                        id="favorite-button">
                    ♥ <?= $isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>
                </button>
            <?php endif; ?>
            <a href="/?page=list" class="spot-detail__back-btn">Retour à la liste</a>
        </div>
    </div>

    <?php if (!empty($nearbySpots)): ?>
        <div class="spot-detail__recommendations">
            <h2 class="spot-detail__recommendations-title">Spots à proximité</h2>
            <div class="spot-detail__recommendations-list">
                <?php foreach ($nearbySpots as $nearbySpot): ?>
                    <div class="spot-detail__recommendation-card">
                        <h3 class="spot-detail__recommendation-title"><?= htmlspecialchars($nearbySpot['site_name']) ?></h3>
                        <p class="spot-detail__recommendation-text"><?= htmlspecialchars($nearbySpot['address']) ?></p>
                        <p class="spot-detail__recommendation-text"><?= $nearbySpot['arrondissement'] ?>e Arrondissement</p>
                        <p class="spot-detail__recommendation-distance">
                            À <?= number_format($nearbySpot['distance'] * 1000, 0) ?> mètres
                        </p>
                        <a href="/?page=spot&id=<?= $nearbySpot['id'] ?>" class="spot-detail__recommendation-btn">Voir détails</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Données pour la carte
const spotData = {
    id: <?= json_encode($spot['id']) ?>,
    site_name: <?= json_encode($spot['site_name']) ?>,
    address: <?= json_encode($spot['address']) ?>,
    latitude: <?= json_encode($spot['latitude']) ?>,
    longitude: <?= json_encode($spot['longitude']) ?>,
    arrondissement: <?= json_encode($spot['arrondissement']) ?>
};

// Gestion des favoris
document.addEventListener('DOMContentLoaded', function() {
    const favoriteButton = document.getElementById('favorite-button');
    
    if (favoriteButton) {
        favoriteButton.addEventListener('click', function() {
            if (!<?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>) {
                window.location.href = '/?page=login';
                return;
            }

            const spotId = this.dataset.spotId;
            const formData = new FormData();
            formData.append('spot_id', spotId);

            fetch('/actions/toggle-favorite.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 401) {
                        window.location.href = '/?page=login';
                    }
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (!data.error) {
                    // Mettre à jour le bouton
                    this.classList.toggle('spot-detail__favorite-btn--active');
                    this.innerHTML = data.is_favorite 
                        ? '♥ Retirer des favoris' 
                        : '♥ Ajouter aux favoris';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});
</script>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="/assets/js/map.js"></script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>