<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Supprimer le cookie "Se souvenir de moi" s'il existe
if (isset($_COOKIE['remember_token'])) {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("UPDATE users SET remember_token = NULL, token_expiry = NULL WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    setcookie('remember_token', '', time() - 3600, '/');
}

// Détruire la session
$_SESSION = [];
session_destroy();

// Rediriger vers la page d'accueil
header('Location: index.php');
exit();