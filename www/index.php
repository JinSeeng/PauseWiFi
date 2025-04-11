<?php
$pageTitle = "Accueil";
require_once 'includes/header.php';
require_once 'includes/functions.php';

$spots = getAllWifiSpots();
?>

<div class="hero">
    <div class="container">
        <h1>Trouvez des spots Wi-Fi gratuits à Paris</h1>
        <p>Découvrez des lieux avec accès Wi-Fi près des parcs, musées et autres points d'intérêt</p>
        <?php include 'templates/search_form.php'; ?>
    </div>
</div>

<section class="featured-spots">
    <div class="container">
        <h2>Spots populaires</h2>
        <div class="spots-grid">
            <?php 
            // Afficher les 6 premiers spots
            $featuredSpots = array_slice($spots, 0, 6);
            foreach ($featuredSpots as $spot): 
                include 'templates/spot_card.php';
            endforeach; 
            ?>
        </div>
    </div>
</section>

<section class="how-it-works">
    <div class="container">
        <h2>Comment ça marche ?</h2>
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Recherchez</h3>
                <p>Trouvez un spot Wi-Fi par arrondissement ou type de lieu</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h3>Localisez</h3>
                <p>Consultez la carte pour voir les spots à proximité</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h3>Profitez</h3>
                <p>Connectez-vous gratuitement et découvrez les lieux autour</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>