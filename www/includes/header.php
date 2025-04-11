<?php
require_once 'config.php';
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="<?= DEFAULT_LANG ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - <?= $pageTitle ?? 'Trouvez des spots Wi-Fi à Paris' ?></title>
    <link rel="stylesheet" href="<?= CSS_PATH ?>/styles.css">
    <link rel="stylesheet" href="<?= CSS_PATH ?>/header.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
</head>
<body>
    <header>
        <div class="container">
            <a href="<?= SITE_URL ?>" class="logo">
                <img src="<?= IMAGES_PATH ?>/logo.png" alt="PauseWiFi Logo">
            </a>
            <nav>
                <ul>
                    <li><a href="<?= SITE_URL ?>">Accueil</a></li>
                    <li><a href="<?= SITE_URL ?>/map.php">Carte</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="<?= SITE_URL ?>/favorites.php">Favoris</a></li>
                        <li><a href="<?= SITE_URL ?>/profile.php">Profil</a></li>
                        <li><a href="<?= SITE_URL ?>/logout.php">Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="<?= SITE_URL ?>/login.php">Connexion</a></li>
                        <li><a href="<?= SITE_URL ?>/register.php">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main>