<?php 
require_once __DIR__ . '/partials/header.php'; 
?>

<div class="spot-detail-container">
    <?php if ($spot): ?>
        <div class="spot-detail-header">
            <h1><?= htmlspecialchars($spot['site_name']) ?></h1>
            <p class="status-badge <?= strtolower(str_replace(' ', '-', $spot['status'])) ?>">
                <?= $spot['status'] ?>
            </p>
        </div>
        
        <div class="spot-detail-content">
            <div class="spot-detail-info">
                <div class="info-section">
                    <h2>Adresse</h2>
                    <p><?= htmlspecialchars($spot['address']) ?></p>
                    <p><?= htmlspecialchars($spot['postal_code']) ?> Paris</p>
                    <p>Arrondissement <?= $spot['arrondissement'] ?></p>
                </div>
                
                <div class="info-section">
                    <h2>Informations</h2>
                    <p><strong>Nombre de bornes :</strong> <?= htmlspecialchars($spot['num_bornes']) ?></p>
                    <p><strong>Code site :</strong> <?= htmlspecialchars($spot['site_code']) ?></p>
                </div>
                
                <div class="info-section">
                    <h2>Coordonnées</h2>
                    <div id="detail-map" class="detail-map"></div>
                    <p>
                        <strong>Coordonnées :</strong> 
                        <?= htmlspecialchars($spot['latitude']) ?>, <?= htmlspecialchars($spot['longitude']) ?>
                    </p>
                </div>
            </div>
            
            <div class="spot-detail-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="btn-favorite-large <?= $isFavorite ? 'active' : '' ?>" 
                            data-spot-id="<?= $spot['id'] ?>">
                        ♥ <?= $isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>
                    </button>
                <?php endif; ?>
                
                <a href="/" class="btn-back">Retour à la liste</a>
            </div>
        </div>
    <?php else: ?>
        <p class="error-message">Désolé, la page que vous recherchez n'existe pas ou a été déplacée.</p>
        <a href="/" class="btn-back">Retour à l'accueil</a>
    <?php endif; ?>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="/assets/js/map.js"></script>
<script src="/assets/js/favorites.js"></script>
<script>
    const spotData = <?= json_encode($spot) ?>;
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>