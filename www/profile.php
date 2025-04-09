<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Vérifier si l'utilisateur est connecté
checkAuthentication();

$page_title = "Mon profil";
require_once 'includes/header.php';

// Ici on récupérera les infos de l'utilisateur depuis la base de données
// Pour l'instant, on simule des données
$user = [
    'username' => 'johndoe',
    'email' => 'john.doe@example.com',
    'created_at' => '2023-01-15 10:30:00'
];
?>

<section class="profile-section">
    <div class="container">
        <h1>Mon profil</h1>
        
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Votre profil a été mis à jour avec succès
            </div>
        <?php endif; ?>
        
        <div class="profile-content">
            <div class="profile-info">
                <div class="profile-header">
                    <div class="avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                    <p>Membre depuis <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></p>
                </div>
                
                <div class="profile-details">
                    <h3>Informations personnelles</h3>
                    <ul>
                        <li>
                            <span class="label"><i class="fas fa-envelope"></i> Email :</span>
                            <span class="value"><?php echo htmlspecialchars($user['email']); ?></span>
                        </li>
                        <li>
                            <span class="label"><i class="fas fa-calendar-alt"></i> Dernière connexion :</span>
                            <span class="value">Aujourd'hui à 14:30</span>
                        </li>
                    </ul>
                </div>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-number">12</span>
                        <span class="stat-label">Favoris</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">5</span>
                        <span class="stat-label">Recherches récentes</span>
                    </div>
                </div>
            </div>
            
            <div class="profile-actions">
                <div class="action-card">
                    <h3><i class="fas fa-user-edit"></i> Modifier mon profil</h3>
                    <form action="update_profile.php" method="post">
                        <div class="form-group">
                            <label for="email">Nouvel email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="current_password">Mot de passe actuel</label>
                            <input type="password" id="current_password" name="current_password" placeholder="Pour confirmer les changements">
                        </div>
                        
                        <button type="submit" class="btn">Mettre à jour</button>
                    </form>
                </div>
                
                <div class="action-card">
                    <h3><i class="fas fa-lock"></i> Changer mon mot de passe</h3>
                    <form action="change_password.php" method="post">
                        <div class="form-group">
                            <label for="current_password_p">Mot de passe actuel</label>
                            <input type="password" id="current_password_p" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">Nouveau mot de passe</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_new_password">Confirmer le nouveau mot de passe</label>
                            <input type="password" id="confirm_new_password" name="confirm_new_password" required>
                        </div>
                        
                        <button type="submit" class="btn">Changer le mot de passe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>