<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$page_title = "Connexion";
require_once 'includes/header.php';
?>

<section class="auth-section">
    <div class="container">
        <div class="auth-form">
            <h1>Connexion</h1>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> Identifiants incorrects
                </div>
            <?php endif; ?>
            
            <form action="process_login.php" method="post">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur ou email</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Se souvenir de moi</label>
                    </div>
                    
                    <a href="forgot_password.php" class="forgot-password">Mot de passe oublié ?</a>
                </div>
                
                <button type="submit" class="btn btn-large">Se connecter</button>
            </form>
            
            <div class="auth-links">
                <p>Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>