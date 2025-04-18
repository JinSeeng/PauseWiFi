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

$errors = []; // Tableau pour stocker les erreurs

// Vérification si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $currentUserId = $_SESSION['user_id'];

    // Validation des champs
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

    // Si aucune erreur de validation
    if (empty($errors)) {
        // Vérification si l'email est déjà utilisé par un autre utilisateur
        $existingUser = $userModel->getUserByEmail($email);
        if ($existingUser && $existingUser['id'] != $currentUserId) {
            $errors[] = "Un compte existe déjà avec cet email";
        } else {
            // Mise à jour du profil
            if ($userModel->updateUserProfile($currentUserId, $username, $email)) {
                // Mise à jour des données en session
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;

                // Journalisation de la modification
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

// Stockage des erreurs et redirection
$_SESSION['profile_errors'] = $errors;
$_SESSION['old_profile'] = $_POST;
header('Location: /?page=profile');
exit;