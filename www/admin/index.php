<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

Auth::requireLogin();

// Récupération des statistiques
$db = Database::getConnectionStatic();
$spotsCount = $db->query("SELECT COUNT(*) FROM wifi_spots")->fetchColumn();
$poiCount = $db->query("SELECT COUNT(*) FROM points_of_interest")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <h1 class="h2">Tableau de bord</h1>
                
                <div class="row my-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Spots WiFi</h5>
                                <p class="card-text display-4"><?= $spotsCount ?></p>
                                <a href="spots/read.php" class="text-white">Voir la liste</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Lieux d'intérêt</h5>
                                <p class="card-text display-4"><?= $poiCount ?></p>
                                <a href="#" class="text-white">Voir la liste</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Administrateurs</h5>
                                <p class="card-text display-4">1</p>
                                <a href="#" class="text-white">Gérer</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        Derniers spots ajoutés
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Adresse</th>
                                    <th>Code postal</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $latestSpots = $db->query("SELECT * FROM wifi_spots ORDER BY created_at DESC LIMIT 5")->fetchAll();
                                foreach ($latestSpots as $spot): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($spot['site_name']) ?></td>
                                        <td><?= htmlspecialchars($spot['address']) ?></td>
                                        <td><?= htmlspecialchars($spot['postal_code']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $spot['status'] == 'Opérationnel' ? 'success' : 'warning' ?>">
                                                <?= htmlspecialchars($spot['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>