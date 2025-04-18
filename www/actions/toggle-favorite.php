<?php
// Inclusion des fichiers nécessaires
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Favorite.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ActivityLog.php';

// Démarrage de la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définition du type de contenu comme JSON
header('Content-Type: application/json');

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized - Please login']);
    exit;
}

// Initialisation des objets
$db = Database::getInstance();
$favoriteModel = new Favorite($db);
$userModel = new User($db);
$activityLog = new ActivityLog($db);

// Récupération de l'ID du spot
$spotId = (int)($_POST['spot_id'] ?? 0);
if ($spotId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid spot ID']);
    exit;
}

// Vérification si l'utilisateur est admin (les admins ne peuvent pas utiliser les favoris)
if ($userModel->isAdmin($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Admins cannot use favorites']);
    exit;
}

try {
    // Vérification si le spot est déjà en favoris
    $isFavorite = $favoriteModel->isFavorite($_SESSION['user_id'], $spotId);
    
    // Ajout ou suppression du favori selon son état actuel
    if ($isFavorite) {
        $result = $favoriteModel->removeFavorite($_SESSION['user_id'], $spotId);
        $action = 'removed';
    } else {
        $result = $favoriteModel->addFavorite($_SESSION['user_id'], $spotId);
        $action = 'added';
    }

    if ($result) {
        // Journalisation de l'action
        $activityLog->logAction(
            $_SESSION['user_id'],
            'favorite_' . $action,
            'Spot ID: ' . $spotId,
            $_SERVER['REMOTE_ADDR']
        );
        
        // Réponse JSON en cas de succès
        echo json_encode([
            'success' => true,
            'action' => $action,
            'is_favorite' => !$isFavorite
        ]);
    } else {
        throw new Exception('Failed to update favorite status');
    }
} catch (PDOException $e) {
    // Gestion des erreurs de base de données
    http_response_code(500);
    error_log("Database error in toggle-favorite: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
} catch (Exception $e) {
    // Gestion des autres erreurs
    http_response_code(500);
    error_log("Error in toggle-favorite: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}