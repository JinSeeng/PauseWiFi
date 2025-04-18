<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/WifiSpot.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ActivityLog.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /?page=login');
    exit;
}

$db = Database::getInstance();
$wifiSpotModel = new WifiSpot($db);
$userModel = new User($db);
$activityLog = new ActivityLog($db);

$action = $_GET['action'] ?? '';
$errors = [];

switch ($action) {
    case 'create-spot':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'site_name' => $_POST['site_name'],
                'site_type' => $_POST['site_type'],
                'address' => $_POST['address'],
                'postal_code' => $_POST['postal_code'],
                'site_code' => $_POST['site_code'] ?? null,
                'num_bornes' => (int)($_POST['num_bornes'] ?? 1),
                'status' => $_POST['status'],
                'latitude' => (float)$_POST['latitude'],
                'longitude' => (float)$_POST['longitude'],
                'arrondissement' => (int)$_POST['arrondissement']
            ];
            
            if ($wifiSpotModel->createSpot($data)) {
                $activityLog->logAction(
                    $_SESSION['user_id'],
                    'spot_created',
                    'Nouveau spot créé: ' . $data['site_name'],
                    $_SERVER['REMOTE_ADDR']
                );
                $_SESSION['success'] = "Spot créé avec succès";
            } else {
                $_SESSION['error'] = "Erreur lors de la création du spot";
            }
        }
        header('Location: /?page=admin');
        exit;
        
    case 'delete-spot':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            if ($wifiSpotModel->deleteSpot($id)) {
                $activityLog->logAction(
                    $_SESSION['user_id'],
                    'spot_deleted',
                    'Spot supprimé ID: ' . $id,
                    $_SERVER['REMOTE_ADDR']
                );
                $_SESSION['success'] = "Spot supprimé avec succès";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression du spot";
            }
        }
        header('Location: /?page=admin');
        exit;
        
    case 'update-user-role':
        $id = (int)($_GET['id'] ?? 0);
        $role = $_POST['role'] ?? 'user';
        
        if ($id > 0 && in_array($role, ['user', 'admin'])) {
            if ($userModel->updateUserRole($id, $role)) {
                $activityLog->logAction(
                    $_SESSION['user_id'],
                    'user_role_updated',
                    'Rôle utilisateur mis à jour ID: ' . $id,
                    $_SERVER['REMOTE_ADDR']
                );
                $_SESSION['success'] = "Rôle utilisateur mis à jour";
            } else {
                $_SESSION['error'] = "Erreur lors de la mise à jour du rôle";
            }
        }
        header('Location: /?page=admin');
        exit;
        
    case 'delete-user':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0 && $id !== $_SESSION['user_id']) {
            if ($userModel->deleteUser($id)) {
                $activityLog->logAction(
                    $_SESSION['user_id'],
                    'user_deleted',
                    'Utilisateur supprimé ID: ' . $id,
                    $_SERVER['REMOTE_ADDR']
                );
                $_SESSION['success'] = "Utilisateur supprimé avec succès";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression de l'utilisateur";
            }
        }
        header('Location: /?page=admin');
        exit;
        
    default:
        header('Location: /?page=admin');
        exit;
}