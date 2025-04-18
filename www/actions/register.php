<?php
// Inclusion des fichiers nécessaires
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ActivityLog.php';

// Démarrage de la session
session_start();

// Initialisation des objets
$db = Database::getInstance();
$userModel = new User($db);
$activityLog = new ActivityLog($db);

$errors = []; // Tableau pour stocker les erreurs

// Vérification si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données du formulaire
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validation des champs
    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis";
    } elseif (strlen($username) < 3) {
        $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Le nom d'utilisateur ne peut contenir que des lettres, chiffres et underscores";
    }

    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }

    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    } elseif (!$userModel->isPasswordStrong($password)) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères, dont une majuscule, une minuscule, un chiffre et un caractère spécial";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }

    // Si aucune erreur de validation
    if (empty($errors)) {
        // Vérification si l'email existe déjà
        if ($userModel->getUserByEmail($email)) {
            $errors[] = "Un compte existe déjà avec cet email";
        } else {
            // Vérification si le nom d'utilisateur existe déjà
            if ($userModel->getUserByUsername($username)) {
                $errors[] = "Ce nom d'utilisateur est déjà pris";
            } else {
                // Création de l'utilisateur
                if ($userModel->createUser($username, $email, $password)) {
                    $user = $userModel->getUserByEmail($email);
                    
                    // Journalisation de l'action
                    $activityLog->logAction(
                        $user['id'],
                        'registration',
                        'Nouvel utilisateur enregistré',
                        $_SERVER['REMOTE_ADDR']
                    );

                    // Connexion automatique de l'utilisateur
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['email'] = $user['email'];

                    $_SESSION['success'] = "Inscription réussie ! Bienvenue $username";
                    header('Location: /?page=profile');
                    exit;
                } else {
                    $errors[] = "Une erreur est survenue lors de l'inscription";
                }
            }
        }
    }
}

// Si on arrive ici, c'est qu'il y a eu une erreur
// Stockage des erreurs et redirection
$_SESSION['register_errors'] = $errors;
$_SESSION['old_register'] = $_POST;
header('Location: /?page=register');
exit;