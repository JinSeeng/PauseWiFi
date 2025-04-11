<?php
$pageTitle = "Profil";
require_once 'includes/header.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

require_once 'includes/db.php';

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Récupérer les favoris
$stmt = $pdo->prepare("
    SELECT s.* FROM wifi_spots s
    JOIN user_favorites uf ON s.id = uf.spot_id
    WHERE uf.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$favorites = $stmt->fetchAll();
?>

<section class="profile">
    <div class="container">
        <h1>Profil de <?= htmlspecialchars($user['username']) ?></h1>
        
        <div class="profile-info">
            <div class="info-card">
                <h2>Informations personnelles</h2>
                <p><strong>Nom d'utilisateur:</strong> <?= htmlspecialchars($user['username']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Membre depuis:</strong> <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
                
                <a href="logout.php" class="btn btn-logout">Déconnexion</a>
            </div>
            
            <div class="favorites-card">
                <h2>Vos spots favoris</h2>
                
                <?php if (empty($favorites)): ?>
                    <p>Vous n'avez aucun spot en favoris.</p>
                <?php else: ?>
                    <div class="favorites-list">
                        <?php foreach ($favorites as $spot): ?>
                            <div class="favorite-item">
                                <h3><?= htmlspecialchars($spot['nom_du_site']) ?></h3>
                                <p><?= htmlspecialchars($spot['adresse']) ?>, <?= $spot['code_postal'] ?></p>
                                <a href="spot.php?id=<?= $spot['id'] ?>" class="btn btn-small">Voir le spot</a>
                                <button class="btn btn-small btn-remove-favorite" data-spot-id="<?= $spot['id'] ?>">
                                    Retirer
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la suppression des favoris
    document.querySelectorAll('.btn-remove-favorite').forEach(button => {
        button.addEventListener('click', function() {
            const spotId = this.getAttribute('data-spot-id');
            
            fetch('api/toggle_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ spot_id: spotId, action: 'remove' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.closest('.favorite-item').remove();
                }
            });
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>