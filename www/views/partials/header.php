<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Déterminer la page active
$currentPage = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pause WiFi - Trouvez des spots WiFi gratuits à Paris</title>
    <meta name="description" content="Localisez les meilleurs spots WiFi gratuits à Paris">
    
    <!-- CSS de base et utilitaires -->
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/header_footer.css">

    <!-- CSS des pages spécifiques -->
    <link rel="stylesheet" href="/assets/css/home.css">
    <link rel="stylesheet" href="/assets/css/auth.css">
    <link rel="stylesheet" href="/assets/css/map.css">
    <link rel="stylesheet" href="/assets/css/list.css">
    <link rel="stylesheet" href="/assets/css/spot-detail.css">
    <link rel="stylesheet" href="/assets/css/favorites.css">
    <link rel="stylesheet" href="/assets/css/about.css">
    <link rel="stylesheet" href="/assets/css/contact.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <link rel="stylesheet" href="/assets/css/edit-spot.css">
    <link rel="stylesheet" href="/assets/css/not_found.css">
    <link rel="stylesheet" href="/assets/css/profile.css">

    <!-- Librairies externes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css">
    <link rel="icon" href="/assets/img/favicon.ico" type="image/x-icon">
</head>
<body>
    <header class="header">
        <div class="header__container">
            <div class="header__logo">
                <a href="/?page=home" class="header__logo-link" aria-label="Accueil">
                    <img src="/assets/img/logo.png" alt="Pause WiFi Logo" class="header__logo-img" width="40" height="40">
                    <span class="header__logo-text">Pause WiFi</span>
                </a>
            </div>
            
            <nav class="header__nav" aria-label="Navigation principale">
                <ul class="header__nav-list">
                    <li class="header__nav-item <?= $currentPage === 'home' ? 'header__nav-item--active' : '' ?>">
                        <a href="/?page=home" class="header__nav-link">Accueil</a>
                    </li>
                    <li class="header__nav-item <?= $currentPage === 'map' ? 'header__nav-item--active' : '' ?>">
                        <a href="/?page=map" class="header__nav-link">Carte</a>
                    </li>
                    <li class="header__nav-item <?= $currentPage === 'list' ? 'header__nav-item--active' : '' ?>">
                        <a href="/?page=list" class="header__nav-link">Liste des spots</a>
                    </li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <li class="header__nav-item <?= $currentPage === 'admin' ? 'header__nav-item--active' : '' ?>">
                                <a href="/?page=admin" class="header__nav-link">Dashboard</a>
                            </li>
                        <?php else: ?>
                            <li class="header__nav-item <?= $currentPage === 'favorites' ? 'header__nav-item--active' : '' ?>">
                                <a href="/?page=favorites" class="header__nav-link">Mes Favoris</a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="header__nav-item">
                            <a href="/logout.php" class="header__logout" aria-label="Déconnexion">
                                <i class="fas fa-sign-out-alt header__logout-icon"></i>
                            </a>
                        </li>
                        
                        <li class="header__user <?= $_SESSION['role'] === 'admin' ? 'header__user--admin' : '' ?>">
                            <?php
                            $profilePicture = isset($_SESSION['user_id']) 
                                ? $userModel->getProfilePicture($_SESSION['user_id'])
                                : null;
                            $picturePath = $profilePicture 
                                ? '/uploads/profiles/' . htmlspecialchars($profilePicture)
                                : '/assets/img/default-profile.png';
                            ?>
                            <a href="/?page=profile" class="header__user-link">
                                <img src="<?= $picturePath ?>" alt="Photo de profil" class="header__user-img" 
                                    onerror="this.src='/assets/img/default-profile.png'">
                                <span class="header__user-name">Bonjour, <?= htmlspecialchars($_SESSION['username']) ?></span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="header__nav-item <?= $currentPage === 'login' ? 'header__nav-item--active' : '' ?>">
                            <a href="/?page=login" class="header__nav-link">Connexion</a>
                        </li>
                        <li class="header__nav-item <?= $currentPage === 'register' ? 'header__nav-item--active' : '' ?>">
                            <a href="/?page=register" class="header__nav-link">Inscription</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            
            <button class="header__mobile-toggle" aria-label="Menu mobile" aria-expanded="false">
                <span class="header__mobile-icon">☰</span>
            </button>
        </div>
    </header>

    <main class="main">