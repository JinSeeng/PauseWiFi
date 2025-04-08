<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/header.php';
?>

<main class="container">
    <section class="hero">
        <h1>Bienvenue sur <?= APP_NAME ?></h1>
        <p class="lead">Trouvez des spots WiFi gratuits à Paris près des lieux d'intérêt</p>
        
        <form action="search.php" method="get" class="search-form">
            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label for="arrondissement">Arrondissement</label>
                    <select class="form-control" id="arrondissement" name="arrondissement">
                        <option value="">Tous les arrondissements</option>
                        <?php for ($i = 1; $i <= 20; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?>e arrondissement</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="type">Type de lieu</label>
                    <select class="form-control" id="type" name="type">
                        <option value="">Tous les types</option>
                        <option value="bibliotheque">Bibliothèque</option>
                        <option value="parc">Parc/Jardin</option>
                        <option value="musee">Musée</option>
                        <option value="mairie">Mairie</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>
    </section>
    
    <section class="mt-5">
        <h2>Derniers spots ajoutés</h2>
        <div class="row" id="latest-spots">
            <!-- Les spots seront chargés en AJAX -->
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chargement des derniers spots
    fetch('api/spots.php?limit=3')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('latest-spots');
            if (data.success && data.data.length > 0) {
                data.data.forEach(spot => {
                    container.innerHTML += `
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">${spot.site_name}</h5>
                                    <p class="card-text">${spot.address}, ${spot.postal_code}</p>
                                    <a href="spot.php?id=${spot.id}" class="btn btn-sm btn-primary">Voir détails</a>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                container.innerHTML = '<p class="col-12">Aucun spot disponible pour le moment.</p>';
            }
        })
        .catch(error => console.error('Error:', error));
});
</script>

<?php
require_once 'includes/footer.php';
?>