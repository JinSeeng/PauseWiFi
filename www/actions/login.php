<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ActivityLog.php';

session_start();

$db = Database::getInstance();
$userModel = new User($db);
$activityLog = new ActivityLog($db);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'email invalide";
    }

    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    }

    if (empty($errors)) {
        try {
            $user = $userModel->getUserByEmail($email);
            
            if ($user && password_verify($password, $user['password'])) {
                // Connexion réussie
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];

                $activityLog->logAction(
                    $user['id'],
                    'login',
                    'Connexion réussie',
                    $_SERVER['REMOTE_ADDR']
                );

                // Régénération de l'ID de session pour prévenir les attaques de fixation
                session_regenerate_id(true);

                // Redirection
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
            error_log("Database error during login: " . $e->getMessage());
            $errors[] = "Une erreur technique est survenue";
        }
    }
}

// Stockage des erreurs pour affichage
$_SESSION['login_errors'] = $errors;
$_SESSION['old_login'] = ['email' => $email];

// Redirection vers la page de login avec les erreurs
header('Location: /?page=login');
exit;