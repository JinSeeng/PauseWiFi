<?php require_once __DIR__ . '/partials/header.php'; ?>

<div class="search-filters">
    <form id="search-form" class="search-form">
        <input type="text" name="search" placeholder="Rechercher par nom ou adresse..." 
               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        
        <select name="arrondissement">
            <option value="">Tous les arrondissements</option>
            <?php for ($i = 1; $i <= 20; $i++): ?>
                <option value="<?= $i ?>" <?= isset($_GET['arrondissement']) && $_GET['arrondissement'] == $i ? 'selected' : '' ?>>
                    Arrondissement <?= $i ?>
                </option>
            <?php endfor; ?>
        </select>
        
        <button type="submit">Filtrer</button>
    </form>
</div>

<div id="spots-list" class="spots-list">
    <?php if (empty($spots)): ?>
        <p class="no-results">Aucun spot WiFi trouvé avec ces critères.</p>
    <?php else: ?>
        <?php foreach ($spots as $spot): ?>
            <div class="spot-card">
                <div class="spot-info">
                    <h2><?= htmlspecialchars($spot['site_name']) ?></h2>
                    <p class="address"><?= htmlspecialchars($spot['address']) ?>, <?= htmlspecialchars($spot['postal_code']) ?></p>
                    <p class="arrondissement">Arrondissement <?= $spot['arrondissement'] ?></p>
                    <p class="status <?= strtolower(str_replace(' ', '-', $spot['status'])) ?>">
                        <?= $spot['status'] ?>
                    </p>
                    <p class="bornes"><?= $spot['num_bornes'] ?> borne(s)</p>
                </div>
                
                <div class="spot-actions">
                    <a href="/spot/<?= $spot['id'] ?>" class="btn-details">Voir détails</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button class="btn-favorite <?= $favoriteModel->isFavorite($_SESSION['user_id'], $spot['id']) ? 'active' : '' ?>" 
                                data-spot-id="<?= $spot['id'] ?>">
                            ♥
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="/assets/js/search.js"></script>
<script src="/assets/js/favorites.js"></script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>