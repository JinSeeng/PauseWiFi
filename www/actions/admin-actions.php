<?php
// Inclusion des fichiers nécessaires
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/WifiSpot.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ActivityLog.php';

// Démarrage de la session
session_start();

// Vérification des droits admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /?page=login');
    exit;
}

// Initialisation des objets
$db = Database::getInstance();
$wifiSpotModel = new WifiSpot($db);
$userModel = new User($db);
$activityLog = new ActivityLog($db);

// Récupération de l'action demandée
$action = $_GET['action'] ?? '';
$errors = [];

// Gestion des différentes actions admin
switch ($action) {
    case 'create-spot':
        // Création d'un nouveau spot WIFI
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
                // Journalisation de la création
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
        // Suppression d'un spot WIFI
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            if ($wifiSpotModel->deleteSpot($id)) {
                // Journalisation de la suppression
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
        // Modification du rôle d'un utilisateur
        $id = (int)($_GET['id'] ?? 0);
        $role = $_POST['role'] ?? 'user';
        
        if ($id > 0 && in_array($role, ['user', 'admin'])) {
            if ($userModel->updateUserRole($id, $role)) {
                // Journalisation du changement de rôle
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
        // Suppression d'un utilisateur
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0 && $id !== $_SESSION['user_id']) {
            if ($userModel->deleteUser($id)) {
                // Journalisation de la suppression
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
        // Redirection par défaut vers l'admin
        header('Location: /?page=admin');
        exit;
}