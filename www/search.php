<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Récupération des paramètres de recherche
$arrondissement = isset($_GET['arrondissement']) ? sanitizeInput($_GET['arrondissement']) : '';
$type = isset($_GET['type']) ? sanitizeInput($_GET['type']) : '';
$query = isset($_GET['query']) ? sanitizeInput($_GET['query']) : '';

// Construction de la requête
$sql = "SELECT * FROM wifi_spots WHERE 1=1";
$params = [];

if (!empty($arrondissement)) {
    $sql .= " AND postal_code LIKE ?";
    $params[] = "750" . str_pad($arrondissement, 2, '0', STR_PAD_LEFT);
}

// Exécution de la requête
try {
    $db = Database::getConnectionStatic();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $spots = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Search error: " . $e->getMessage());
    $spots = [];
}
?>

<main class="container">
    <h1>Résultats de recherche</h1>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Filtres</h5>
                    <form method="get" action="search.php">
                        <div class="form-group">
                            <label for="arrondissement">Arrondissement</label>
                            <select class="form-control" id="arrondissement" name="arrondissement">
                                <option value="">Tous</option>
                                <?php for ($i = 1; $i <= 20; $i++): ?>
                                    <option value="<?= $i ?>" <?= $arrondissement == $i ? 'selected' : '' ?>>
                                        <?= $i ?>e
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Appliquer</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <?php if (empty($spots)): ?>
                <div class="alert alert-info">Aucun résultat trouvé pour votre recherche.</div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($spots as $spot): ?>
                        <a href="spot.php?id=<?= $spot['id'] ?>" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1"><?= htmlspecialchars($spot['site_name']) ?></h5>
                                <small><?= htmlspecialchars($spot['postal_code']) ?></small>
                            </div>
                            <p class="mb-1"><?= htmlspecialchars($spot['address']) ?></p>
                            <small>Statut: <?= htmlspecialchars($spot['status']) ?></small>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
require_once 'includes/footer.php';
?>