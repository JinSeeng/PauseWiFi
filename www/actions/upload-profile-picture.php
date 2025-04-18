<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ActivityLog.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /?page=login');
    exit;
}

$db = Database::getInstance();
$userModel = new User($db);
$activityLog = new ActivityLog($db);

$userId = $_SESSION['user_id'];
$errors = [];

// Vérifier si un fichier a été uploadé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    
    // Validation du fichier
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Erreur lors du téléchargement du fichier";
    } else {
        // Vérifier le type de fichier
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            $errors[] = "Seuls les fichiers JPEG, PNG et GIF sont autorisés";
        }
        
        // Vérifier la taille (max 2MB)
        if ($file['size'] > 2097152) {
            $errors[] = "La taille du fichier ne doit pas dépasser 2MB";
        }
    }
    
    if (empty($errors)) {
        // Créer le dossier uploads s'il n'existe pas
        $uploadDir = __DIR__ . '/../../www/uploads/profiles/'; // Chemin modifié
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Et la destination pour l'affichage
        $picturePath = $profilePicture 
            ? 'uploads/profiles/' . htmlspecialchars($profilePicture) // Chemin modifié
            : '/assets/img/default-profile.jpg';
        
        // Générer un nom de fichier unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $userId . '_' . uniqid() . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        // Supprimer l'ancienne photo si elle existe
        $oldPicture = $userModel->getProfilePicture($userId);
        if ($oldPicture && file_exists($uploadDir . $oldPicture)) {
            unlink($uploadDir . $oldPicture);
        }
        
        // Déplacer le fichier uploadé
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Mettre à jour la base de données
            if ($userModel->updateProfilePicture($userId, $filename)) {
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

$_SESSION['profile_errors'] = $errors;
header('Location: /?page=profile');
exit;