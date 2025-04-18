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
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation des champs
    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'email invalide";
    }

    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    }

    // Si aucune erreur de validation
    if (empty($errors)) {
        try {
            // Récupération de l'utilisateur par email
            $user = $userModel->getUserByEmail($email);
            
            // Vérification du mot de passe
            if ($user && password_verify($password, $user['password'])) {
                // Connexion réussie - mise en session des données utilisateur
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];

                // Journalisation de la connexion
                $activityLog->logAction(
                    $user['id'],
                    'login',
                    'Connexion réussie',
                    $_SERVER['REMOTE_ADDR']
                );

                // Régénération de l'ID de session pour la sécurité
                session_regenerate_id(true);

                // Redirection vers la page demandée ou le profil par défaut
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header("Location: $redirect");
                } else {
                    header('Location: /?page=profile');
                }
                exit;
            } else {
                $errors[] = "Email ou mot de passe incorrect";
                // Journalisation en cas d'échec de connexion
                if ($user) {
                    $activityLog->logAction(
                        $user['id'],
                        'login_failed',
                        'Tentative de connexion échouée',
                        $_SERVER['REMOTE_ADDR']
                    );
                }
            }
        } catch (PDOException $e) {
            // Gestion des erreurs de base de données
            error_log("Database error during login: " . $e->getMessage());
            $errors[] = "Une erreur technique est survenue";
        }
    }
}

// Stockage des erreurs et redirection vers la page de login
$_SESSION['login_errors'] = $errors;
$_SESSION['old_login'] = ['email' => $email];
header('Location: /?page=login');
exit;