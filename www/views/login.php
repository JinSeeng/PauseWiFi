<?php require_once __DIR__ . '/partials/header.php'; ?>

<div class="auth-container">
    <h1>Connexion</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form action="/login" method="POST" class="auth-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn-submit">Se connecter</button>
    </form>
    
    <div class="auth-links">
        <p>Pas encore de compte ? <a href="/register">S'inscrire</a></p>
        <p><a href="/forgot-password">Mot de passe oublié ?</a></p>
    </div>
</div>

<script src="/assets/js/form-validation.js"></script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>