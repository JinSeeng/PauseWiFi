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
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Valider le token
    $query = "SELECT id FROM users WHERE reset_token = :token AND reset_expires > UNIX_TIMESTAMP()";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $errors[] = "Lien invalide ou expiré";
    }
    
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    } elseif (!$userModel->isPasswordStrong($password)) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères, dont une majuscule, une minuscule, un chiffre et un caractère spécial";
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }
    
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Mettre à jour le mot de passe et effacer le token
        $query = "UPDATE users SET password = :password, reset_token = NULL, reset_expires = NULL WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $activityLog->logAction(
                $user['id'],
                'password_reset_success',
                'Mot de passe réinitialisé',
                $_SERVER['REMOTE_ADDR']
            );
            
            $_SESSION['success'] = "Votre mot de passe a été réinitialisé avec succès";
            header('Location: /?page=login');
            exit;
        } else {
            $errors[] = "Erreur lors de la mise à jour du mot de passe";
        }
    }
}

// Stocker les erreurs pour affichage
$_SESSION['reset_errors'] = $errors;
$_SESSION['reset_token'] = $_GET['token'] ?? '';
header('Location: /?page=forgot-password&token=' . urlencode($_GET['token'] ?? ''));
exit;