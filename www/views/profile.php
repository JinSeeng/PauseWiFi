<?php 
require_once __DIR__ . '/partials/header.php';

// Récupération des erreurs et anciennes valeurs depuis la session
$profileErrors = $_SESSION['profile_errors'] ?? [];
$passwordErrors = $_SESSION['password_errors'] ?? [];
$oldProfile = $_SESSION['old_profile'] ?? [];
unset($_SESSION['profile_errors']);
unset($_SESSION['password_errors']);
unset($_SESSION['old_profile']);
?>

<div class="profile">
    <h1 class="profile__title">Mon Profil</h1>
    
    <!-- Affichage des messages de succès -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="profile__alert profile__alert--success">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <div class="profile__content">
        <div class="profile__info">
            
            <!-- Affichage des erreurs de profil -->
            <?php if (!empty($profileErrors)): ?>
                <div class="profile__alert profile__alert--error">
                    <?php foreach ($profileErrors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Section photo de profil -->
            <div class="profile__picture-section">
                <h3 class="profile__picture-title">Photo de profil</h3>
                <div class="profile__current-picture">
                    <?php
                    // Récupération de la photo de profil
                    $profilePicture = $userModel->getProfilePicture($_SESSION['user_id']);
                    $picturePath = $profilePicture 
                        ? '/uploads/profiles/' . htmlspecialchars($profilePicture)
                        : '/assets/img/default-profile.jpg';
                    ?>
                    <img src="<?= $picturePath ?>" alt="Photo de profil" class="profile__img">
                </div>
                
                <!-- Formulaire de changement de photo avec input stylisé -->
                <form action="/actions/upload-profile-picture.php" method="POST" enctype="multipart/form-data" class="profile__upload-form">
                    <div class="profile__form-group">
                        <input type="file" id="profile_picture" name="profile_picture" class="profile__file-input" accept="image/jpeg,image/png,image/gif">
                        <label for="profile_picture">Choisir une photo</label>
                        <small class="profile__file-hint">Formats acceptés : JPG, PNG, GIF (max 2MB)</small>
                    </div>
                    <button type="submit" class="profile__submit">Mettre à jour</button>
                </form>
            </div>

            <!-- Formulaire de modification du profil -->
            <form action="/actions/edit-profile.php" method="POST" class="profile__form">
                <h3 class="profile__picture-title">Informations personnelles</h3>
                <div class="profile__form-group">
                    <label for="username" class="profile__label">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" class="profile__input" 
                           value="<?= htmlspecialchars($oldProfile['username'] ?? $user['username']) ?>" required>
                </div>
                
                <div class="profile__form-group">
                    <label for="email" class="profile__label">Email</label>
                    <input type="email" id="email" name="email" class="profile__input" 
                           value="<?= htmlspecialchars($oldProfile['email'] ?? $user['email']) ?>" required>
                </div>
                
                <button type="submit" class="profile__submit">Mettre à jour</button>
            </form>
            
            <!-- Section changement de mot de passe -->
            <div class="profile__password">
                <h3 class="profile__password-title">Changer le mot de passe</h3>
                
                <!-- Affichage des erreurs de mot de passe -->
                <?php if (!empty($passwordErrors)): ?>
                    <div class="profile__alert profile__alert--error">
                        <?php foreach ($passwordErrors as $error): ?>
                            <p><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Formulaire de changement de mot de passe -->
                <form action="/actions/change-password.php" method="POST" class="profile__password-form">
                    <div class="profile__form-group">
                        <label for="current_password" class="profile__label">Mot de passe actuel</label>
                        <input type="password" id="current_password" name="current_password" class="profile__input" required>
                    </div>
                    
                    <div class="profile__form-group">
                        <label for="new_password" class="profile__label">Nouveau mot de passe</label>
                        <input type="password" id="new_password" name="new_password" class="profile__input" required>
                        <small class="profile__hint">
                            Le nouveau mot de passe doit contenir au moins 8 caractères, dont :
                            <ul class="profile__hint-list">
                                <li class="profile__hint-item">Une lettre majuscule</li>
                                <li class="profile__hint-item">Une lettre minuscule</li>
                                <li class="profile__hint-item">Un chiffre</li>
                                <li class="profile__hint-item">Un caractère spécial (!@#$%^&*)</li>
                            </ul>
                        </small>
                    </div>
                    
                    <div class="profile__form-group">
                        <label for="confirm_password" class="profile__label">Confirmer le nouveau mot de passe</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="profile__input" required>
                    </div>
                    
                    <button type="submit" class="profile__submit">Changer le mot de passe</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>