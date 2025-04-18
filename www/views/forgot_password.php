<?php 
// Inclure l'en-tête de la page
require_once __DIR__ . '/partials/header.php';

// Récupérer les messages d'erreur/succès de la session
$errors = $_SESSION['forgot_errors'] ?? [];
$success = $_SESSION['forgot_success'] ?? false;
$resetErrors = $_SESSION['reset_errors'] ?? [];
$token = $_SESSION['reset_token'] ?? ($_GET['token'] ?? '');

// Nettoyer les données de session après les avoir récupérées
unset($_SESSION['forgot_errors']);
unset($_SESSION['forgot_success']);
unset($_SESSION['reset_errors']);
unset($_SESSION['reset_token']);
?>

<div class="auth">
    <?php if (isset($_GET['token']) && $token): ?>
        <!-- Formulaire de réinitialisation de mot de passe -->
        <h1 class="auth__title">Réinitialiser votre mot de passe</h1>
        
        <!-- Afficher les erreurs de réinitialisation -->
        <?php if (!empty($resetErrors)): ?>
            <div class="auth__alert auth__alert--error">
                <?php foreach ($resetErrors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulaire de réinitialisation -->
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
        <!-- Formulaire de demande de réinitialisation -->
        <h1 class="auth__title">Mot de passe oublié</h1>
        
        <!-- Afficher le message de succès -->
        <?php if ($success): ?>
            <div class="auth__alert auth__alert--success">
                <p>Un email de réinitialisation a été envoyé à votre adresse.</p>
                <p>Veuillez vérifier votre boîte de réception.</p>
            </div>
        <?php elseif (!empty($errors)): ?>
            <!-- Afficher les erreurs -->
            <div class="auth__alert auth__alert--error">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Afficher le formulaire seulement si pas de succès -->
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
    
    <!-- Lien vers la page de connexion -->
    <div class="auth__links">
        <p><a href="/?page=login" class="auth__link">Retour à la connexion</a></p>
    </div>
</div>

<?php 
// Inclure le pied de page
require_once __DIR__ . '/partials/footer.php'; 
?>