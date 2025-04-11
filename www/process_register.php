<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et valider les données
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $terms = isset($_POST['terms']) ? true : false;

    // Validation
    $errors = [];

    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis.";
    } elseif (strlen($username) < 3) {
        $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Une adresse email valide est requise.";
    }

    if (empty($password) || strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (!$terms) {
        $errors[] = "Vous devez accepter les conditions d'utilisation.";
    }

    // Vérifier si l'utilisateur ou l'email existe déjà
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);

    if ($stmt->fetch()) {
        $errors[] = "Ce nom d'utilisateur ou cette adresse email est déjà utilisé.";
    }

    // Si pas d'erreurs, créer le compte
    if (empty($errors)) {
        $hashed_password = hashPassword($password);
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);

        // Connecter automatiquement l'utilisateur
        $user_id = $conn->lastInsertId();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'user';

        // Rediriger vers la page de profil
        header('Location: profile.php');
        exit();
    } else {
        // Stocker les erreurs en session et rediriger
        $_SESSION['register_errors'] = $errors;
        $_SESSION['form_data'] = [
            'username' => $username,
            'email' => $email
        ];
        header('Location: register.php');
        exit();
    }
} else {
    // Si la méthode n'est pas POST, rediriger
    header('Location: register.php');
    exit();
}