<?php
// Inclusion des fichiers nécessaires
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ActivityLog.php';

// Démarrage de la session
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: /?page=login');
    exit;
}

// Initialisation des objets
$db = Database::getInstance();
$userModel = new User($db);
$activityLog = new ActivityLog($db);

$userId = $_SESSION['user_id'];
$errors = []; // Tableau pour stocker les erreurs

// Vérification si un fichier a été uploadé via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    
    // Validation du fichier uploadé
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Erreur lors du téléchargement du fichier";
    } else {
        // Vérification du type de fichier (JPEG, PNG, GIF)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            $errors[] = "Seuls les fichiers JPEG, PNG et GIF sont autorisés";
        }
        
        // Vérification de la taille du fichier (max 2MB)
        if ($file['size'] > 2097152) {
            $errors[] = "La taille du fichier ne doit pas dépasser 2MB";
        }
    }
    
    // Si aucune erreur de validation
    if (empty($errors)) {
        // Création du dossier uploads s'il n'existe pas
        $uploadDir = __DIR__ . '/../../www/uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Génération d'un nom de fichier unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $userId . '_' . uniqid() . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        // Suppression de l'ancienne photo si elle existe
        $oldPicture = $userModel->getProfilePicture($userId);
        if ($oldPicture && file_exists($uploadDir . $oldPicture)) {
            unlink($uploadDir . $oldPicture);
        }
        
        // Déplacement du fichier uploadé vers le dossier de destination
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Mise à jour de la photo de profil en base de données
            if ($userModel->updateProfilePicture($userId, $filename)) {
                // Journalisation de l'action
                $activityLog->logAction(
                    $userId,
                    'profile_picture_updated',
                    'Photo de profil mise à jour',
                    $_SERVER['REMOTE_ADDR']
                );
                
                $_SESSION['success'] = "Photo de profil mise à jour avec succès";
                header('Location: /?page=profile');
                exit;
            } else {
                $errors[] = "Erreur lors de la mise à jour de la base de données";
            }
        } else {
            $errors[] = "Erreur lors de l'enregistrement du fichier";
        }
    }
}

// Stockage des erreurs et redirection
$_SESSION['profile_errors'] = $errors;
header('Location: /?page=profile');
exit;