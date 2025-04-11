<?php
$pageTitle = "Connexion administrateur";
require_once '../includes/header.php';

if (isAdmin()) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/db.php';
    
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    // Vérifier les identifiants admin
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        loginUser($admin['id'], $admin['username'], 'admin');
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Identifiants administrateur incorrects.";
    }
}
?>

<section class="auth-form admin-auth">
    <div class="container">
        <h1>Connexion administrateur</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email administrateur</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Se connecter</button>
        </form>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>