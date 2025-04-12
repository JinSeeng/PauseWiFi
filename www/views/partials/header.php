<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pause WiFi - Trouvez des spots WiFi gratuits à Paris</title>
    <meta name="description" content="Localisez les meilleurs spots WiFi gratuits à Paris">
    
    <!-- Feuilles de style CSS -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/header.css">
    <link rel="stylesheet" href="/assets/css/footer.css">
    <link rel="stylesheet" href="/assets/css/spots.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">
    <link rel="stylesheet" href="/assets/css/home.css">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    
    <!-- Leaflet MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css">
    
    <!-- Favicon -->
    <link rel="icon" href="/assets/img/favicon.ico" type="image/x-icon">
    
    <!-- Préchargement des polices -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="/" aria-label="Accueil">
                    <img src="/assets/img/logo.png" alt="Pause WiFi Logo" width="40" height="40">
                    <span>Pause WiFi</span>
                </a>
            </div>
            
            <nav class="main-nav" aria-label="Navigation principale">
                <ul>
                    <li><a href="/">Carte</a></li> <!-- Maintenant pointe vers la carte -->
                    <li><a href="/list">Liste des spots</a></li> <!-- Nouveau lien -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="/favorites">Mes Favoris</a></li>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <li><a href="/admin">Admin</a></li>
                        <?php endif; ?>
                        <li><a href="/logout">Déconnexion</a></li>
                        <li class="user-greeting">Bonjour, <?= htmlspecialchars($_SESSION['username']) ?></li>
                    <?php else: ?>
                        <li><a href="/login">Connexion</a></li>
                        <li><a href="/register">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            
            <button class="mobile-menu-toggle" aria-label="Menu mobile" aria-expanded="false">☰</button>
        </div>
    </header>

    <main class="container">