<?php 
require_once __DIR__ . '/partials/header.php';

$errors = $_SESSION['forgot_errors'] ?? [];
$success = $_SESSION['forgot_success'] ?? false;
$resetErrors = $_SESSION['reset_errors'] ?? [];
$token = $_SESSION['reset_token'] ?? ($_GET['token'] ?? '');
unset($_SESSION['forgot_errors']);
unset($_SESSION['forgot_success']);
unset($_SESSION['reset_errors']);
unset($_SESSION['reset_token']);
?>

<div class="auth">
    <?php if (isset($_GET['token']) && $token): ?>
        <h1 class="auth__title">Réinitialiser votre mot de passe</h1>
        
        <?php if (!empty($resetErrors)): ?>
            <div class="auth__alert auth__alert--error">
                <?php foreach ($resetErrors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form action="/actions/reset-password.php" method="POST" class="auth__form">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            
            <div class="auth__form-group">
                <label for="password" class="auth__label">Nouveau mot de passe</label>
                <input type="password" id="password" name="password" class="auth__input" required>
            </div>
            
            <div class="auth__form-group">
                <label for="confirm_password" class="auth__label">Confirmer le nouveau mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" class="auth__input" required>
            </div>
            
            <button type="submit" class="auth__submit">Réinitialiser le mot de passe</button>
        </form>
        
    <?php else: ?>
        <h1 class="auth__title">Mot de passe oublié</h1>
        
        <?php if ($success): ?>
            <div class="auth__alert auth__alert--success">
                <p>Un email de réinitialisation a été envoyé à votre adresse.</p>
                <p>Veuillez vérifier votre boîte de réception.</p>
            </div>
        <?php elseif (!empty($errors)): ?>
            <div class="auth__alert auth__alert--error">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$success): ?>
            <form action="/actions/forgot-password.php" method="POST" class="auth__form">
                <div class="auth__form-group">
                    <label for="email" class="auth__label">Email</label>
                    <input type="email" id="email" name="email" class="auth__input" required>
                </div>
                
                <button type="submit" class="auth__submit">Envoyer le lien de réinitialisation</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
    
    <div class="auth__links">
        <p><a href="/?page=login" class="auth__link">Retour à la connexion</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>