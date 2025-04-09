<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$page_title = "Trouvez des spots Wi-Fi gratuits à Paris";
require_once 'includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <h2>Trouvez des spots Wi-Fi gratuits à Paris</h2>
        <p>Découvrez des lieux avec accès Wi-Fi près de parcs, musées, bibliothèques et autres lieux d'intérêt.</p>
        
        <form action="search.php" method="get" class="search-form">
            <div class="form-group">
                <input type="text" name="q" placeholder="Rechercher par arrondissement, lieu...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>
</section>

<section class="features">
    <div class="feature">
        <i class="fas fa-wifi"></i>
        <h3>Wi-Fi gratuit</h3>
        <p>Accès à des centaines de spots Wi-Fi gratuits dans tout Paris.</p>
    </div>
    
    <div class="feature">
        <i class="fas fa-map-marked-alt"></i>
        <h3>Localisation précise</h3>
        <p>Trouvez facilement les spots sur notre carte interactive.</p>
    </div>
    
    <div class="feature">
        <i class="fas fa-route"></i>
        <h3>Itinéraires</h3>
        <p>Planifiez votre trajet vers les meilleurs spots Wi-Fi.</p>
    </div>
</section>

<section class="recent-spots">
    <h2>Derniers spots ajoutés</h2>
    
    <div class="spots-grid">
        <!-- Ces données seront dynamiques avec PHP plus tard -->
        <div class="spot-card">
            <div class="spot-image" style="background-image: url('https://via.placeholder.com/300x200')"></div>
            <div class="spot-info">
                <h3>Bibliothèque François Truffaut</h3>
                <p><i class="fas fa-map-marker-alt"></i> 14 Rue des Prouvaires, 75001</p>
                <p><i class="fas fa-wifi"></i> Opérationnel</p>
                <a href="spot.php?id=1" class="btn">Voir détails</a>
            </div>
        </div>
        
        <div class="spot-card">
            <div class="spot-image" style="background-image: url('https://via.placeholder.com/300x200')"></div>
            <div class="spot-info">
                <h3>Parc de Belleville</h3>
                <p><i class="fas fa-map-marker-alt"></i> 47 Rue des Couronnes, 75020</p>
                <p><i class="fas fa-wifi"></i> Opérationnel</p>
                <a href="spot.php?id=2" class="btn">Voir détails</a>
            </div>
        </div>
        
        <div class="spot-card">
            <div class="spot-image" style="background-image: url('https://via.placeholder.com/300x200')"></div>
            <div class="spot-info">
                <h3>Musée Carnavalet</h3>
                <p><i class="fas fa-map-marker-alt"></i> 23 Rue de Sévigné, 75003</p>
                <p><i class="fas fa-wifi"></i> Opérationnel</p>
                <a href="spot.php?id=3" class="btn">Voir détails</a>
            </div>
        </div>
    </div>
    
    <div class="text-center">
        <a href="map.php" class="btn btn-large">Voir tous les spots sur la carte</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>