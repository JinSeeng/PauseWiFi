<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';

Auth::requireLogin();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = sanitizeInput($_POST['site_name']);
    $address = sanitizeInput($_POST['address']);
    $postal_code = sanitizeInput($_POST['postal_code']);
    $site_code = sanitizeInput($_POST['site_code']);
    $num_terminals = sanitizeInput($_POST['num_terminals']);
    $status = sanitizeInput($_POST['status']);
    $latitude = sanitizeInput($_POST['latitude']);
    $longitude = sanitizeInput($_POST['longitude']);
    
    // Validation
    if (empty($site_name)) $errors[] = "Le nom du site est obligatoire";
    if (empty($address)) $errors[] = "L'adresse est obligatoire";
    if (!preg_match('/^750\d{2}$/', $postal_code)) $errors[] = "Code postal invalide";
    if (!validateCoordinates($latitude, $longitude)) $errors[] = "Coordonnées GPS invalides";
    
    if (empty($errors)) {
        try {
            $db = Database::getConnectionStatic();
            $stmt = $db->prepare("INSERT INTO wifi_spots 
                (site_name, address, postal_code, site_code, num_terminals, status, latitude, longitude) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $site_name, $address, $postal_code, $site_code, 
                $num_terminals, $status, $latitude, $longitude
            ]);
            
            $success = true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = "Ce code site existe déjà";
            } else {
                $errors[] = "Erreur lors de l'ajout: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un spot WiFi - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <h1 class="h2">Ajouter un nouveau spot WiFi</h1>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">Le spot a été ajouté avec succès!</div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="mb-3">
                        <label for="site_name" class="form-label">Nom du site</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Adresse</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="postal_code" class="form-label">Code postal</label>
                            <select class="form-control" id="postal_code" name="postal_code" required>
                                <option value="">Sélectionnez...</option>
                                <?php for ($i = 1; $i <= 20; $i++): ?>
                                    <option value="750<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>">
                                        750<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?> (<?= $i ?>e arrondissement)
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="site_code" class="form-label">Code site</label>
                            <input type="text" class="form-control" id="site_code" name="site_code" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="num_terminals" class="form-label">Nombre de bornes</label>
                            <input type="number" class="form-control" id="num_terminals" name="num_terminals" min="1">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="Opérationnel">Opérationnel</option>
                                <option value="Fermé pour travaux">Fermé pour travaux</option>
                                <option value="En déploiement">En déploiement</option>
                                <option value="En étude">En étude</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                    <a href="read.php" class="btn btn-secondary">Annuler</a>
                </form>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script pour géolocalisation automatique
        document.getElementById('address').addEventListener('blur', function() {
            const address = this.value + ', ' + (document.getElementById('postal_code').value || 'Paris');
            
            if (address.length > 5) {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            document.getElementById('latitude').value = data[0].lat;
                            document.getElementById('longitude').value = data[0].lon;
                        }
                    });
            }
        });
    </script>
</body>
</html>