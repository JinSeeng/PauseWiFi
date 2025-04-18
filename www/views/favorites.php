<?php 
require_once __DIR__ . '/partials/header.php';

if (empty($favorites)): ?>
    <div class="favorites">
        <h1 class="favorites__title">Mes Favoris</h1>
        <p class="favorites__empty">Vous n'avez aucun spot en favoris pour le moment.</p>
        <a href="/?page=list" class="favorites__explore-btn">Explorer les spots</a>
    </div>
<?php else: ?>
    <div class="favorites">
        <h1 class="favorites__title">Mes Favoris</h1>
        
        <div class="favorites__list">
            <?php foreach ($favorites as $spot): ?>
                <div class="favorites__item" id="favorite-spot-<?= $spot['id'] ?>">
                    <h3 class="favorites__spot-title"><?= htmlspecialchars($spot['site_name']) ?></h3>
                    <p class="favorites__spot-text"><?= htmlspecialchars($spot['address']) ?></p>
                    <p class="favorites__spot-text"><?= $spot['arrondissement'] ?>e Arrondissement</p>
                    <div class="favorites__spot-actions">
                        <a href="/?page=spot&id=<?= $spot['id'] ?>" class="favorites__details-btn">Voir détails</a>
                        <button class="favorites__remove-btn" 
                                data-spot-id="<?= $spot['id'] ?>">
                            Retirer des favoris
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="favorites__pagination">
                <?php if ($pageNumber > 1): ?>
                    <a href="/?page=favorites&page=<?= $pageNumber - 1 ?>" class="favorites__page-link">&laquo; Précédent</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="/?page=favorites&page=<?= $i ?>" class="favorites__page-link <?= $i == $pageNumber ? 'favorites__page-link--active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($pageNumber < $totalPages): ?>
                    <a href="/?page=favorites&page=<?= $pageNumber + 1 ?>" class="favorites__page-link">Suivant &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Vérification de la connexion utilisateur
    function isUserLoggedIn() {
        return document.cookie.includes('PHPSESSID') || <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
    }

    // Gestionnaire d'événement pour les boutons de suppression
    document.querySelectorAll('.favorites__remove-btn').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (!isUserLoggedIn()) {
                window.location.href = '/?page=login';
                return;
            }

            const spotId = this.getAttribute('data-spot-id');
            const buttonElement = this;
            
            // Afficher un indicateur de chargement
            buttonElement.disabled = true;
            buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suppression...';

            try {
                const formData = new FormData();
                formData.append('spot_id', spotId);
                
                const response = await fetch('/actions/toggle-favorite.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin' // Important pour les cookies de session
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();

                if (data.error) {
                    throw new Error(data.error);
                }

                if (data.success && data.is_favorite === false) {
                    // Supprimer l'élément du DOM
                    const spotElement = document.getElementById(`favorite-spot-${spotId}`);
                    if (spotElement) {
                        spotElement.remove();
                    }
                    
                    // Mettre à jour l'affichage si plus de favoris
                    if (document.querySelectorAll('.favorites__item').length === 0) {
                        document.querySelector('.favorites').innerHTML = `
                            <h1 class="favorites__title">Mes Favoris</h1>
                            <p class="favorites__empty">Vous n'avez aucun spot en favoris pour le moment.</p>
                            <a href="/?page=list" class="favorites__explore-btn">Explorer les spots</a>
                        `;
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Erreur lors de la suppression du favori: ' + error.message);
                buttonElement.disabled = false;
                buttonElement.innerHTML = 'Retirer des favoris';
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
