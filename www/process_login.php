<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;

    // Validation
    $errors = [];

    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur ou l'email est requis.";
    }

    if (empty($password)) {
        $errors[] = "Le mot de passe est requis.";
    }

    // Si pas d'erreurs, vérifier les identifiants
    if (empty($errors)) {
        $db = new Database();
        $conn = $db->getConnection();

        // Rechercher l'utilisateur par username ou email
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && verifyPassword($password, $user['password'])) {
            // Authentification réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Si "Se souvenir de moi" est coché
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30); // 30 jours

                $stmt = $conn->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE id = ?");
                $stmt->execute([$token, $expiry, $user['id']]);

                setcookie('remember_token', $token, time() + 60 * 60 * 24 * 30, '/');
            }

            // Rediriger vers la page demandée ou la page d'accueil
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
            header("Location: $redirect");
            exit();
        } else {
            $errors[] = "Identifiants incorrects.";
        }
    }

    // Si erreurs, stocker en session et rediriger
    $_SESSION['login_errors'] = $errors;
    $_SESSION['form_data'] = ['username' => $username];
    header('Location: login.php' . (isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''));
    exit();
} else {
    // Si la méthode n'est pas POST, rediriger
    header('Location: login.php');
    exit();
}