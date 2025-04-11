<?php
$pageTitle = "Inscription";
require_once 'includes/header.php';

if (isLoggedIn()) {
    header('Location: profile.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/db.php';
    
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $passwordConfirm = $_POST['password_confirm'];
    
    // Validation
    $errors = [];
    
    if (empty($username) {
        $errors[] = "Le nom d'utilisateur est requis.";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide.";
    }
    
    if (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }
    
    if ($password !== $passwordConfirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }
    
    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Cet email est déjà utilisé.";
    }
    
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword]);
        
        $userId = $pdo->lastInsertId();
        loginUser($userId, $username);
        
        header('Location: profile.php');
        exit;
    }
}
?>

<section class="auth-form">
    <div class="container">
        <h1>Inscription</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert error">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe (8 caractères minimum)</label>
                <input type="password" id="password" name="password" minlength="8" required>
            </div>
            
            <div class="form-group">
                <label for="password_confirm">Confirmer le mot de passe</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            
            <button type="submit" class="btn">S'inscrire</button>
        </form>
        
        <p class="auth-link">Déjà un compte ? <a href="login.php">Connectez-vous</a></p>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>