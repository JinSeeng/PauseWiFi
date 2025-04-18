<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/WifiSpot.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Favorite.php';
require_once __DIR__ . '/models/ActivityLog.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = Database::getInstance();
$wifiSpotModel = new WifiSpot($db);
$userModel = new User($db);
$favoriteModel = new Favorite($db);
$activityLog = new ActivityLog($db);

// Partie des requêtes AJAX
if (isset($_GET['page']) && ($_GET['page'] === 'search' || $_GET['page'] === 'map-search')) {
    header('Content-Type: application/json');
    
    $searchParams = [
        'search' => $_GET['search'] ?? '',
        'arrondissement' => $_GET['arrondissement'] ?? 'all',
        'site_type' => $_GET['site_type'] ?? 'all',
        'status' => $_GET['status'] ?? 'all'
    ];
    
    try {
        if (isset($_GET['latitude']) && isset($_GET['longitude'])) {
            $spots = $wifiSpotModel->getNearbySpots(
                (float)$_GET['latitude'],
                (float)$_GET['longitude'],
                null,
                2, // Rayon de 2km
                20, // Limite à 20 résultats
                $searchParams
            );
        } else {
            $spots = $wifiSpotModel->searchSpots($searchParams);
        }
        
        // Ajouter la propriété isFavorite si l'utilisateur est connecté et n'est pas admin
        if (isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin') {
            foreach ($spots as &$spot) {
                $spot['isFavorite'] = $favoriteModel->isFavorite($_SESSION['user_id'], $spot['id']);
            }
            unset($spot); // Important pour éviter des problèmes de référence
        }
        
        echo json_encode($spots);
    } catch (Exception $e) {
        error_log("Error in AJAX request: " . $e->getMessage());
        echo json_encode(['error' => 'Une erreur est survenue']);
    }
    exit;
}

// Détection de la page demandée
$page = $_GET['page'] ?? 'home';

// Inclure le header
require_once __DIR__ . '/views/partials/header.php';

// Charger la page appropriée
switch ($page) {
    case 'home':
        require __DIR__ . '/views/home.php';
        break;
        
    case 'map':
        require __DIR__ . '/views/map.php';
        break;
        
    case 'list':
        $searchParams = [
            'search' => $_GET['search'] ?? '',
            'arrondissement' => $_GET['arrondissement'] ?? 'all',
            'site_type' => $_GET['site_type'] ?? 'all',
            'status' => $_GET['status'] ?? 'all'
        ];
        
        try {
            if (isset($_GET['latitude']) && isset($_GET['longitude'])) {
                $spots = $wifiSpotModel->getNearbySpots(
                    (float)$_GET['latitude'],
                    (float)$_GET['longitude'],
                    null,
                    2, // Rayon de 2km
                    20, // Limite à 20 résultats
                    $searchParams
                );
            } else {
                $spots = $wifiSpotModel->searchSpots($searchParams);
            }
            
            require __DIR__ . '/views/list.php';
        } catch (Exception $e) {
            error_log("Error loading list page: " . $e->getMessage());
            $spots = [];
            require __DIR__ . '/views/list.php';
        }
        break;
        
    case 'spot':
        if (isset($_GET['id'])) {
            try {
                $spotId = (int)$_GET['id'];
                $spot = $wifiSpotModel->getSpotById($spotId);
                if ($spot) {
                    $isFavorite = (isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin') 
                        ? $favoriteModel->isFavorite($_SESSION['user_id'], $spotId) 
                        : false;
                    require __DIR__ . '/views/wifi_detail.php';
                } else {
                    require __DIR__ . '/views/not_found.php';
                }
            } catch (Exception $e) {
                error_log("Error loading spot detail: " . $e->getMessage());
                require __DIR__ . '/views/not_found.php';
            }
        } else {
            require __DIR__ . '/views/not_found.php';
        }
        break;
        
    case 'login':
        $errors = $_SESSION['login_errors'] ?? [];
        $oldInput = $_SESSION['old_login'] ?? [];
        unset($_SESSION['login_errors']);
        unset($_SESSION['old_login']);
        require __DIR__ . '/views/login.php';
        break;
        
    case 'register':
        $errors = $_SESSION['register_errors'] ?? [];
        $oldInput = $_SESSION['old_register'] ?? [];
        unset($_SESSION['register_errors']);
        unset($_SESSION['old_register']);
        require __DIR__ . '/views/register.php';
        break;
        
    case 'forgot-password':
        require __DIR__ . '/views/forgot_password.php';
        break;
        
    case 'profile':
        if (isset($_SESSION['user_id'])) {
            try {
                $user = $userModel->getUserById($_SESSION['user_id']);
                require __DIR__ . '/views/profile.php';
            } catch (Exception $e) {
                error_log("Error loading profile: " . $e->getMessage());
                header('Location: /?page=login');
                exit;
            }
        } else {
            header('Location: /?page=login');
            exit;
        }
        break;
        
    case 'favorites':
        if (isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin') {
            try {
                $pageNumber = max(1, (int)($_GET['page'] ?? 1));
                $perPage = 10;
                
                $totalFavorites = $favoriteModel->countUserFavorites($_SESSION['user_id']);
                $totalPages = max(1, ceil($totalFavorites / $perPage));
                $pageNumber = min($pageNumber, $totalPages);
                
                $favorites = $favoriteModel->getUserFavoritesPaginated(
                    $_SESSION['user_id'],
                    $pageNumber,
                    $perPage
                );
                
                require __DIR__ . '/views/favorites.php';
            } catch (Exception $e) {
                error_log("Error loading favorites: " . $e->getMessage());
                $favorites = [];
                $totalPages = 1;
                $pageNumber = 1;
                require __DIR__ . '/views/favorites.php';
            }
        } else {
            header('Location: /?page=login');
            exit;
        }
        break;
        
    case 'admin':
        if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
            try {
                $spots = $wifiSpotModel->getAllSpots();
                $users = $userModel->getAllUsers();
                require __DIR__ . '/views/admin_dashboard.php';
            } catch (Exception $e) {
                error_log("Error loading admin dashboard: " . $e->getMessage());
                $spots = [];
                $users = [];
                require __DIR__ . '/views/admin_dashboard.php';
            }
        } else {
            header('Location: /?page=login');
            exit;
        }
        break;
        
    case 'edit-spot':
        if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin' && isset($_GET['id'])) {
            require __DIR__ . '/views/edit-spot.php';
        } else {
            header('Location: /?page=login');
            exit;
        }
        break;
        
    case 'about':
        require __DIR__ . '/views/about.php';
        break;
        
    case 'contact':
        require __DIR__ . '/views/contact.php';
        break;
        
    default:
        require __DIR__ . '/views/not_found.php';
        break;
}

// Inclure le footer
require_once __DIR__ . '/views/partials/footer.php';
?>