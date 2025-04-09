<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$page_title = "Inscription";
require_once 'includes/header.php';
?>

<section class="auth-section">
    <div class="container">
        <div class="auth-form">
            <h1>Créer un compte</h1>
            
            <form action="process_register.php" method="post">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                    <small>Minimum 8 caractères, avec au moins une majuscule, une minuscule et un chiffre</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-group">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">J'accepte les <a href="#">conditions d'utilisation</a> et la <a href="#">politique de confidentialité</a></label>
                </div>
                
                <button type="submit" class="btn btn-large">S'inscrire</button>
            </form>
            
            <div class="auth-links">
                <p>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>