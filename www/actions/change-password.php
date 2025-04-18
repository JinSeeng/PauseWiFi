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

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $userId = $_SESSION['user_id'];

    // Validation
    if (empty($currentPassword)) {
        $errors[] = "Le mot de passe actuel est requis";
    }

    if (empty($newPassword)) {
        $errors[] = "Le nouveau mot de passe est requis";
    } elseif (!$userModel->isPasswordStrong($newPassword)) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères, dont une majuscule, une minuscule, un chiffre et un caractère spécial";
    }

    if ($newPassword !== $confirmPassword) {
        $errors[] = "Les nouveaux mots de passe ne correspondent pas";
    }

    if (empty($errors)) {
        // Vérifier le mot de passe actuel
        $user = $userModel->getUserById($userId);
        if ($user && password_verify($currentPassword, $user['password'])) {
            // Mettre à jour le mot de passe
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            if ($userModel->updatePassword($userId, $hashedPassword)) {
                $activityLog->logAction(
                    $userId,
                    'password_changed',
                    'Mot de passe changé',
                    $_SERVER['REMOTE_ADDR']
                );

                $_SESSION['success'] = "Mot de passe changé avec succès";
                header('Location: /?page=profile');
                exit;
            } else {
                $errors[] = "Une erreur est survenue lors de la mise à jour du mot de passe";
            }
        } else {
            $errors[] = "Mot de passe actuel incorrect";
        }
    }
}

// Stocker les erreurs et rediriger
$_SESSION['password_errors'] = $errors;
header('Location: /?page=profile');
exit;