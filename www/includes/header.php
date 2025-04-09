<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Charger les traductions de manière sécurisée
$langFile = __DIR__ . '/lang/' . ($_SESSION['lang'] ?? DEFAULT_LANG) . '.php';
$translations = file_exists($langFile) ? require $langFile : [];
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($_SESSION['lang'] ?? DEFAULT_LANG); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php 
        echo isset($page_title) 
            ? htmlspecialchars($page_title) . ' | ' 
            : '';
        echo htmlspecialchars($translations['site_name'] ?? 'Pause Wi-Fi'); 
        ?>
    </title>
    <meta name="description" content="<?php echo htmlspecialchars($translations['site_description'] ?? 'Find free Wi-Fi spots in Paris'); ?>">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/header.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/map.css">    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/search.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/user-auth.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/spots.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/responsive.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/lang.css">
    <?php if (str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/')): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin.css">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/images/logo.png">
    
    <!-- Leaflet CSS -->
    <?php if (basename($_SERVER['PHP_SELF'] ?? '') === 'map.php' || basename($_SERVER['PHP_SELF'] ?? '') === 'spot.php'): ?>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    <?php endif; ?>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>">
                    <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="<?php echo htmlspecialchars($translations['site_name'] ?? 'Pause Wi-Fi'); ?>">
                    <h1><?php echo htmlspecialchars($translations['site_name'] ?? 'Pause Wi-Fi'); ?></h1>
                </a>
            </div>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>"><i class="fas fa-home"></i> <?php echo htmlspecialchars($translations['home'] ?? 'Home'); ?></a></li>
                    <li><a href="<?php echo BASE_URL; ?>map.php"><i class="fas fa-map"></i> <?php echo htmlspecialchars($translations['map'] ?? 'Map'); ?></a></li>
                    <li><a href="<?php echo BASE_URL; ?>search.php"><i class="fas fa-search"></i> <?php echo htmlspecialchars($translations['search'] ?? 'Search'); ?></a></li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo BASE_URL; ?>favorites.php"><i class="fas fa-heart"></i> <?php echo htmlspecialchars($translations['favorites'] ?? 'Favorites'); ?></a></li>
                        <li><a href="<?php echo BASE_URL; ?>profile.php"><i class="fas fa-user"></i> <?php echo htmlspecialchars($translations['my_account'] ?? 'My Account'); ?></a></li>
                        <li><a href="<?php echo BASE_URL; ?>logout.php"><i class="fas fa-sign-out-alt"></i> <?php echo htmlspecialchars($translations['logout'] ?? 'Logout'); ?></a></li>
                        
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <li><a href="<?php echo BASE_URL; ?>admin/"><i class="fas fa-cog"></i> <?php echo htmlspecialchars($translations['admin'] ?? 'Admin'); ?></a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li><a href="<?php echo BASE_URL; ?>login.php"><i class="fas fa-sign-in-alt"></i> <?php echo htmlspecialchars($translations['login'] ?? 'Login'); ?></a></li>
                        <li><a href="<?php echo BASE_URL; ?>register.php"><i class="fas fa-user-plus"></i> <?php echo htmlspecialchars($translations['register'] ?? 'Register'); ?></a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            
            <div class="language-switcher">
                <form action="" method="get">
                    <select name="lang" onchange="this.form.submit()">
                        <option value="fr" <?php echo ($_SESSION['lang'] ?? DEFAULT_LANG) === 'fr' ? 'selected' : ''; ?>>Français</option>
                        <option value="en" <?php echo ($_SESSION['lang'] ?? DEFAULT_LANG) === 'en' ? 'selected' : ''; ?>>English</option>
                    </select>
                </form>
            </div>
            
            <button class="mobile-menu-toggle" aria-label="Menu mobile">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <main class="container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['form_errors'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> 
                <ul>
                    <?php foreach ($_SESSION['form_errors'] as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php unset($_SESSION['form_errors']); ?>
            </div>
        <?php endif; ?>