<?php
// Inclusion des fichiers nécessaires
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/ActivityLog.php';

// Démarrage de la session
session_start();

// Journalisation de la déconnexion si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    $db = Database::getInstance();
    $activityLog = new ActivityLog($db);
    
    $activityLog->logAction(
        $_SESSION['user_id'],
        'logout',
        'Déconnexion',
        $_SERVER['REMOTE_ADDR']
    );
}

// Destruction de la session
session_unset();
session_destroy();
setcookie(session_name(), '', time() - 3600, '/');

// Redirection vers la page d'accueil
header('Location: /?page=home');
exit;