<?php 
require_once __DIR__ . '/partials/header.php';

$errors = $_SESSION['register_errors'] ?? [];
$oldInput = $_SESSION['old_register'] ?? [];
unset($_SESSION['register_errors']);
unset($_SESSION['old_register']);
?>

<div class="auth">
    <h1 class="auth__title">Créer un compte</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="auth__alert auth__alert--error">
            <?php foreach ($errors as $error): ?>
                <p><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form action="/actions/register.php" method="POST" class="auth__form">
        <div class="auth__form-group">
            <label for="username" class="auth__label">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" class="auth__input" placeholder="Choisissez un nom d'utilisateur" required 
                   value="<?= htmlspecialchars($oldInput['username'] ?? '') ?>">
            <small class="auth__hint">3 caractères minimum, lettres, chiffres et underscores seulement</small>
        </div>
        
        <div class="auth__form-group">
            <label for="email" class="auth__label">Adresse email</label>
            <input type="email" id="email" name="email" class="auth__input" placeholder="Entrez votre email" required 
                   value="<?= htmlspecialchars($oldInput['email'] ?? '') ?>">
        </div>
        
        <div class="auth__form-group">
            <label for="password" class="auth__label">Mot de passe</label>
            <input type="password" id="password" name="password" class="auth__input" placeholder="Créez un mot de passe sécurisé" required>
            <small class="auth__hint">
                Le mot de passe doit contenir au moins 8 caractères, dont :
                <ul class="auth__hint-list">
                    <li class="auth__hint-item">Une lettre majuscule</li>
                    <li class="auth__hint-item">Une lettre minuscule</li>
                    <li class="auth__hint-item">Un chiffre</li>
                    <li class="auth__hint-item">Un caractère spécial (!@#$%^&*)</li>
                </ul>
            </small>
        </div>
        
        <div class="auth__form-group">
            <label for="confirm_password" class="auth__label">Confirmer le mot de passe</label>
            <input type="password" id="confirm_password" name="confirm_password" class="auth__input" placeholder="Confirmez votre mot de passe" required>
        </div>
        
        <button type="submit" class="auth__submit">S'inscrire</button>
    </form>
    
    <div class="auth__links">
        <p>Vous avez déjà un compte ? <a href="/?page=login" class="auth__link">Se connecter</a></p>
    </div>
</div>

<script src="/assets/js/form-validation.js"></script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>