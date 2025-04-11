<?php
$pageTitle = "Favoris";
require_once 'includes/header.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

require_once 'includes/db.php';

// Récupérer les favoris
$stmt = $pdo->prepare("
    SELECT s.* FROM wifi_spots s
    JOIN user_favorites uf ON s.id = uf.spot_id
    WHERE uf.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$favorites = $stmt->fetchAll();
?>

<section class="favorites">
    <div class="container">
        <h1>Vos spots Wi-Fi favoris</h1>
        
        <?php if (empty($favorites)): ?>
            <div class="no-favorites">
                <p>Vous n'avez aucun spot en favoris pour le moment.</p>
                <a href="map.php" class="btn">Explorer les spots</a>
            </div>
        <?php else: ?>
            <div class="favorites-grid">
                <?php foreach ($favorites as $spot): ?>
                    <div class="favorite-spot">
                        <?php include 'templates/spot_card.php'; ?>
                        <button class="btn-remove-favorite" data-spot-id="<?= $spot['id'] ?>">
                            Retirer des favoris
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
                    this.closest('.favorite-spot').remove();
                }
            });
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>