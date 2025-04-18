<?php 
require_once __DIR__ . '/partials/header.php';

$errors = $_SESSION['login_errors'] ?? [];
$oldInput = $_SESSION['old_login'] ?? [];
unset($_SESSION['login_errors']);
unset($_SESSION['old_login']);
?>

<div class="auth">
    <h1 class="auth__title">Connexion</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="auth__alert auth__alert--success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="auth__alert auth__alert--error">
            <?php foreach ($errors as $error): ?>
                <p><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form action="/actions/login.php" method="POST" class="auth__form">
        <div class="auth__form-group">
            <label for="email" class="auth__label">Adresse email</label>
            <input type="email" id="email" name="email" class="auth__input" placeholder="Entrez votre email" required 
                   value="<?= htmlspecialchars($oldInput['email'] ?? '') ?>">
        </div>
        
        <div class="auth__form-group">
            <label for="password" class="auth__label">Mot de passe</label>
            <input type="password" id="password" name="password" class="auth__input" placeholder="Entrez votre mot de passe" required>
            <small class="auth__forgot"><a href="/?page=forgot-password" class="auth__forgot-link">Mot de passe oublié ?</a></small>
        </div>
        
        <button type="submit" class="auth__submit">Se connecter</button>
    </form>
    
    <div class="auth__links">
        <p class="auth__links-text">Pas encore de compte ? <a href="/?page=register" class="auth__links-link">Créer un compte</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>