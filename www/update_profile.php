<?php
// www/update_profile.php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';

checkAuthentication();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profile.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$email = trim($_POST['email']);
$current_password = $_POST['current_password'];

// Validation
$errors = [];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "L'adresse email n'est pas valide.";
}

if (empty($current_password)) {
    $errors[] = "Le mot de passe actuel est requis pour confirmer les changements.";
}

if (empty($errors)) {
    try {
        // Vérifier le mot de passe actuel
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if ($user && verifyPassword($current_password, $user['password'])) {
            // Mettre à jour l'email
            $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            
            $_SESSION['success_message'] = "Votre profil a été mis à jour.";
            header("Location: profile.php?updated=1");
            exit();
        } else {
            $errors[] = "Le mot de passe actuel est incorrect.";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            $errors[] = "Cette adresse email est déjà utilisée.";
        } else {
            $errors[] = "Une erreur est survenue lors de la mise à jour.";
        }
    }
}

$_SESSION['form_errors'] = $errors;
header("Location: profile.php");
exit();