<?php 
require_once __DIR__ . '/partials/header.php'; 
?>

<!-- Page d'erreur 404 -->
<div class="error">
    <h1 class="error__title">404 - Page non trouvée</h1>
    <p class="error__text">Désolé, la page que vous recherchez n'existe pas ou a été déplacée.</p>
    <a href="/?page=home" class="error__home-btn">Retour à l'accueil</a>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>