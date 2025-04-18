<?php require_once __DIR__ . '/partials/header.php'; ?>

<div class="spots-list">
    <div class="spots-list__filters">
        <form id="search-form" class="spots-list__search-form">
            <input type="text" name="search" class="spots-list__search-input" 
                   placeholder="Rechercher par nom ou adresse..." 
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            
            <div class="spots-list__filter-row">
                <select name="site_type" class="spots-list__filter">
                    <option value="all">Tous les types</option>
                    <?php 
                    $types = ['Bibliothèque', 'Parc', 'Centre sportif', 'Mairie', 'Musée', 'Hotel', 'Autre'];
                    foreach ($types as $type): ?>
                        <option value="<?= $type ?>" <?= ($_GET['site_type'] ?? '') === $type ? 'selected' : '' ?>>
                            <?= $type ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <select name="arrondissement" class="spots-list__filter">
                    <option value="all">Tous les arrondissements</option>
                    <?php for ($i = 1; $i <= 20; $i++): ?>
                        <option value="<?= $i ?>" <?= isset($_GET['arrondissement']) && $_GET['arrondissement'] == $i ? 'selected' : '' ?>>
                            <?= $i ?>e Arrondissement
                        </option>
                    <?php endfor; ?>
                </select>
                
                <select name="status" class="spots-list__filter">
                    <option value="all">Tous les statuts</option>
                    <?php 
                    $statuses = ['Opérationnel', 'Fermé pour travaux', 'En déploiement', 'En étude'];
                    foreach ($statuses as $status): ?>
                        <option value="<?= $status ?>" <?= ($_GET['status'] ?? '') === $status ? 'selected' : '' ?>>
                            <?= $status ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="spots-list__search-btn">Rechercher</button>
            <button type="button" id="locate-me" class="spots-list__locate-btn">
                <i class="fas fa-location-arrow"></i> Spots près de moi
            </button>
        </form>
    </div>

    <div id="spots-list" class="spots-list__results">
        <?php if (empty($spots)): ?>
            <div class="spots-list__empty">
                <p class="spots-list__empty-text">Aucun spot WiFi trouvé avec ces critères.</p>
            </div>
        <?php else: ?>
            <?php foreach ($spots as $spot): ?>
                <div class="spots-list__item">
                    <div class="spots-list__item-info">
                        <h2 class="spots-list__item-title"><?= htmlspecialchars($spot['site_name']) ?></h2>
                        <p class="spots-list__item-address"><?= htmlspecialchars($spot['address']) ?></p>
                        <p class="spots-list__item-arrondissement"><?= $spot['arrondissement'] ?>e Arrondissement</p>
                        <?php if (isset($spot['distance'])): ?>
                            <p class="spots-list__item-distance">À <?= number_format($spot['distance'] * 1000, 0) ?> mètres</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="spots-list__item-actions">
                        <a href="/?page=spot&id=<?= $spot['id'] ?>" class="spots-list__details-btn">Voir détails</a>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin'): ?>
                            <button class="spots-list__favorite-btn <?= $favoriteModel->isFavorite($_SESSION['user_id'], $spot['id']) ? 'spots-list__favorite-btn--active' : '' ?>" 
                                    data-spot-id="<?= $spot['id'] ?>">
                                ♥
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Déléguer les événements de favoris
    document.addEventListener('click', function(e) {
        const btnFavorite = e.target.closest('.spots-list__favorite-btn');
        if (btnFavorite) {
            e.preventDefault();
            handleFavoriteClick(btnFavorite);
        }
    });

    const searchForm = document.getElementById('search-form');
    const spotsList = document.getElementById('spots-list');
    const locateBtn = document.getElementById('locate-me');
    
    // Gestion de la soumission du formulaire
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        performSearch();
    });
    
    // Gestion du changement des filtres
    searchForm.querySelectorAll('input, select').forEach(input => {
        input.addEventListener('change', function() {
            performSearch();
        });
    });
    
    // Gestion du bouton "Spots près de moi"
    locateBtn.addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert("La géolocalisation n'est pas supportée par votre navigateur");
            return;
        }
        
        locateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Localisation...';
        locateBtn.disabled = true;
        
        navigator.geolocation.getCurrentPosition(
            position => {
                const params = new URLSearchParams();
                const formData = new FormData(searchForm);
                
                for (const [key, value] of formData.entries()) {
                    if (value && value !== 'all') {
                        params.append(key, value);
                    }
                }
                
                params.append('latitude', position.coords.latitude);
                params.append('longitude', position.coords.longitude);
                
                fetch(`/?page=search&${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    updateSearchResults(data);
                    window.history.pushState({}, '', `/?page=list&${params.toString()}`);
                })
                .catch(error => {
                    console.error('Error:', error);
                })
                .finally(() => {
                    locateBtn.innerHTML = '<i class="fas fa-location-arrow"></i> Spots près de moi';
                    locateBtn.disabled = false;
                });
            },
            error => {
                let errorMessage = "Impossible d'obtenir votre position";
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = "Vous avez refusé l'accès à votre position";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = "Position indisponible";
                        break;
                    case error.TIMEOUT:
                        errorMessage = "La requête a expiré";
                        break;
                }
                alert(errorMessage);
                locateBtn.innerHTML = '<i class="fas fa-location-arrow"></i> Spots près de moi';
                locateBtn.disabled = false;
            }
        );
    });
    
    function handleFavoriteClick(button) {
        if (!isUserLoggedIn()) {
            window.location.href = '/?page=login';
            return;
        }

        const spotId = button.dataset.spotId;
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
                button.classList.toggle('spots-list__favorite-btn--active', data.is_favorite);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function performSearch() {
        const formData = new FormData(searchForm);
        const params = new URLSearchParams();
        
        for (const [key, value] of formData.entries()) {
            if (value && value !== 'all') {
                params.append(key, value);
            }
        }
        
        fetch(`/?page=search&${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            updateSearchResults(data);
            window.history.pushState({}, '', `/?page=list&${params.toString()}`);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    function updateSearchResults(spots, isAdmin = false) {
        if (spots.length === 0) {
            spotsList.innerHTML = `
                <div class="spots-list__empty">
                    <p class="spots-list__empty-text">Aucun spot WiFi trouvé avec ces critères.</p>
                </div>
            `;
        } else {
            let html = '';
            spots.forEach(spot => {
                const isUserAllowed = <?= isset($_SESSION['user_id']) && !$userModel->isAdmin($_SESSION['user_id']) ? 'true' : 'false' ?>;
                
                html += `
                    <div class="spots-list__item">
                        <div class="spots-list__item-info">
                            <h2 class="spots-list__item-title">${escapeHtml(spot.site_name)}</h2>
                            <p class="spots-list__item-address">${escapeHtml(spot.address)}</p>
                            <p class="spots-list__item-arrondissement">${spot.arrondissement}e Arrondissement</p>
                            ${spot.distance ? `<p class="spots-list__item-distance">À ${Math.round(spot.distance * 1000)} mètres</p>` : ''}
                        </div>
                        <div class="spots-list__item-actions">
                            <a href="/?page=spot&id=${spot.id}" class="spots-list__details-btn">Voir détails</a>
                            ${isUserAllowed ? `
                            <button class="spots-list__favorite-btn ${spot.isFavorite ? 'spots-list__favorite-btn--active' : ''}" 
                                    data-spot-id="${spot.id}">
                                ♥
                            </button>
                            ` : ''}
                        </div>
                    </div>
                `;
            });
            spotsList.innerHTML = html;
        }
    }
    
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    function isUserLoggedIn() {
        return document.querySelector('.header__user') !== null;
    }

    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.innerHTML = `<p>${message}</p>`;
        
        document.body.prepend(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>