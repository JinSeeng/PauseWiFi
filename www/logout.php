<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/ActivityLog.php';

session_start();

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

session_unset();
session_destroy();
setcookie(session_name(), '', time() - 3600, '/');

header('Location: /?page=home');
exit;