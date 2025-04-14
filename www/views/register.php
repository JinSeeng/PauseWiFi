<?php require_once __DIR__ . '/partials/header.php'; ?>

<div class="auth-container">
    <h1>Inscription</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form action="/register" method="POST" class="auth-form">
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirmer le mot de passe</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <button type="submit" class="btn-submit">S'inscrire</button>
    </form>
    
    <div class="auth-links">
        <p>Déjà un compte ? <a href="/login">Se connecter</a></p>
    </div>
</div>

<script src="/assets/js/form-validation.js"></script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>