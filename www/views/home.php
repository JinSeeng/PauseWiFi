<?php 
// Inclure l'en-tête de la page
require_once __DIR__ . '/partials/header.php'; 
?>

<div class="home">
    <!-- Section Hero (bannière principale) -->
    <div class="home__hero">
        <div class="home__hero-content">
            <h1 class="home__hero-title">Bienvenue sur <span class="home__hero-highlight">Pause WiFi</span></h1>
            <p class="home__hero-subtitle">Trouvez les meilleurs spots WiFi gratuits à Paris</p>
            <div class="home__hero-actions">
                <!-- Bouton principal vers la carte -->
                <a href="/?page=map" class="home__hero-btn home__hero-btn--primary">
                    <i class="fas fa-map-marker-alt"></i> Voir la carte interactive
                </a>
                <!-- Bouton secondaire vers la liste -->
                <a href="/?page=list" class="home__hero-btn home__hero-btn--secondary">
                    <i class="fas fa-list"></i> Explorer tous les spots
                </a>
            </div>
        </div>
        <div class="home__hero-decoration">
            <div class="home__wifi-icon"></div>
        </div>
    </div>

    <!-- Section Statistiques -->
    <div class="home__stats">
        <div class="home__stat-item">
            <div class="home__stat-number">200+</div>
            <div class="home__stat-label">Spots WiFi</div>
        </div>
        <div class="home__stat-item">
            <div class="home__stat-number">20</div>
            <div class="home__stat-label">Arrondissements</div>
        </div>
        <div class="home__stat-item">
            <div class="home__stat-number">24/7</div>
            <div class="home__stat-label">Disponibilité</div>
        </div>
    </div>

    <!-- Section Fonctionnalités -->
    <div class="home__features">
        <div class="home__feature">
            <div class="home__feature-icon">
                <i class="fas fa-search"></i>
            </div>
            <h2 class="home__feature-title">Recherche facile</h2>
            <p class="home__feature-desc">Trouvez des spots WiFi par arrondissement, type de lieu ou statut</p>
        </div>
        <div class="home__feature">
            <div class="home__feature-icon">
                <i class="fas fa-heart"></i>
            </div>
            <h2 class="home__feature-title">Favoris</h2>
            <p class="home__feature-desc">Enregistrez vos spots préférés pour y accéder rapidement</p>
        </div>
        <div class="home__feature">
            <div class="home__feature-icon">
                <i class="fas fa-sync-alt"></i>
            </div>
            <h2 class="home__feature-title">À jour</h2>
            <p class="home__feature-desc">Données régulièrement mises à jour par la Ville de Paris</p>
        </div>
    </div>

    <!-- Section Call-to-Action (incitation à l'action) -->
    <div class="home__cta">
        <h2 class="home__cta-title">
            <?php if (isset($_SESSION['user_id'])): ?>
                Content de vous revoir 👋
            <?php else: ?>
                Prêt à trouver votre prochain spot WiFi ? 🔍
            <?php endif; ?>
        </h2>
        <p class="home__cta-text">
            <?php if (isset($_SESSION['user_id'])): ?>
                Explorez notre carte interactive pour trouver les meilleurs spots près de vous.
            <?php else: ?>
                Rejoignez notre communauté et découvrez les meilleurs endroits pour vous connecter à Paris.
            <?php endif; ?>
        </p>
        <!-- Bouton CTA différent selon si l'utilisateur est connecté ou non -->
        <a href="/?page=<?= isset($_SESSION['user_id']) ? 'map' : 'register' ?>" class="home__cta-btn">
            <?= isset($_SESSION['user_id']) ? 'Voir la carte' : 'Créer un compte' ?>
        </a>
    </div>
</div>

<?php 
// Inclure le pied de page
require_once __DIR__ . '/partials/footer.php'; 
?>