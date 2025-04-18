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
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $currentUserId = $_SESSION['user_id'];

    // Validation
    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis";
    } elseif (strlen($username) < 3) {
        $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères";
    }

    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }

    if (empty($errors)) {
        // Vérifier si l'email existe déjà pour un autre utilisateur
        $existingUser = $userModel->getUserByEmail($email);
        if ($existingUser && $existingUser['id'] != $currentUserId) {
            $errors[] = "Un compte existe déjà avec cet email";
        } else {
            // Mettre à jour le profil
            if ($userModel->updateUserProfile($currentUserId, $username, $email)) {
                // Mettre à jour les données de session
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;

                $activityLog->logAction(
                    $currentUserId,
                    'profile_updated',
                    'Profil mis à jour',
                    $_SERVER['REMOTE_ADDR']
                );

                $_SESSION['success'] = "Profil mis à jour avec succès";
                header('Location: /?page=profile');
                exit;
            } else {
                $errors[] = "Une erreur est survenue lors de la mise à jour";
            }
        }
    }
}

// Stocker les erreurs et rediriger
$_SESSION['profile_errors'] = $errors;
$_SESSION['old_profile'] = $_POST;
header('Location: /?page=profile');
exit;