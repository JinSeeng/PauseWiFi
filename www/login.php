<?php
$pageTitle = "Connexion";
require_once 'includes/header.php';

if (isLoggedIn()) {
    header('Location: profile.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/db.php';
    
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    // Vérifier les identifiants
    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        loginUser($user['id'], $user['username'], $user['role']);
        header('Location: profile.php');
        exit;
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>

<section class="auth-form">
    <div class="container">
        <h1>Connexion</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Se connecter</button>
        </form>
        
        <p class="auth-link">Pas encore de compte ? <a href="register.php">Inscrivez-vous</a></p>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>