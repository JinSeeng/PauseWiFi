<?php
declare(strict_types=1);

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/WifiSpot.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Favorite.php';
require_once __DIR__ . '/controllers/WifiSpotController.php';
require_once __DIR__ . '/controllers/AuthController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
        'read_and_close'  => false,
    ]);
}

$db = Database::getInstance();
$wifiSpotModel = new WifiSpot($db);
$userModel = new User($db);
$favoriteModel = new Favorite($db);

$wifiController = new WifiSpotController($wifiSpotModel, $favoriteModel);
$authController = new AuthController($userModel);

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

try {
    switch ($requestUri) {
        case '/':
        case '/home':
            $wifiController->mapView();
            break;

        case '/list':
            $wifiController->index(); // Affiche la liste des spots
            break;

        case (preg_match('#^/spot/(\d+)$#', $requestUri, $matches) ? true : false):
            $wifiController->show((int)$matches[1]);
            break;

        case '/search':
            $wifiController->search();
            break;

        case '/login':
            if ($requestMethod === 'POST') {
                $authController->login();
            } else {
                require __DIR__ . '/views/login.php';
            }
            break;

        case '/register':
            if ($requestMethod === 'POST') {
                $authController->register();
            } else {
                require __DIR__ . '/views/register.php';
            }
            break;

        case '/logout':
            $authController->logout();
            break;

        case '/favorites':
            if (!isset($_SESSION['user_id'])) {
                header('Location: /login');
                exit;
            }
            require __DIR__ . '/views/profile.php';
            break;

        case '/api/toggle-favorite':
            if ($requestMethod === 'POST' && isset($_SESSION['user_id'])) {
                $spotId = (int)($_POST['spot_id'] ?? 0);
                $favoriteModel->toggleFavorite($_SESSION['user_id'], $spotId);
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            }
            http_response_code(401);
            break;

        case '/not-found':
            http_response_code(404);
            require __DIR__ . '/views/not_found.php';
            break;

        default:
            if (file_exists(__DIR__ . $requestUri)) {
                return false;
            }
            http_response_code(404);
            require __DIR__ . '/views/not_found.php';
            break;
    }
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    http_response_code(500);
    require __DIR__ . '/views/errors/server_error.php';
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
    http_response_code(500);
    require __DIR__ . '/views/errors/server_error.php';
}