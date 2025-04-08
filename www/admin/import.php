<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

Auth::requireLogin();

$errors = [];
$success = false;
$importedCount = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['data_file'])) {
    $file = $_FILES['data_file'];
    
    // Validation du fichier
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Erreur lors du téléversement du fichier: " . $file['error'];
    } elseif (!isFileAllowed($file['name'], ALLOWED_FILE_TYPES)) {
        $errors[] = "Type de fichier non autorisé. Seuls CSV et JSON sont acceptés.";
    } elseif ($file['size'] > MAX_UPLOAD_SIZE) {
        $errors[] = "Fichier trop volumineux. Taille maximale: " . (MAX_UPLOAD_SIZE / 1024 / 1024) . "MB";
    } else {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $tempPath = $file['tmp_name'];
        
        try {
            $db = Database::getConnectionStatic();
            $db->beginTransaction();
            
            if ($extension === 'csv') {
                // Import CSV
                $handle = fopen($tempPath, 'r');
                if ($handle !== false) {
                    // Ignorer l'en-tête
                    fgetcsv($handle, 0, ';');
                    
                    while (($data = fgetcsv($handle, 0, ';')) !== false) {
                        if (count($data) < 8) continue;
                        
                        $site_name = sanitizeInput($data[0]);
                        $address = sanitizeInput($data[1]);
                        $postal_code = sanitizeInput($data[2]);
                        $site_code = sanitizeInput($data[3]);
                        $num_terminals = (int)$data[4];
                        $status = sanitizeInput($data[5]);
                        
                        // Extraction des coordonnées
                        $geoPoint = parseGeoPoint($data[7]);
                        if (!$geoPoint) continue;
                        
                        try {
                            $stmt = $db->prepare("INSERT INTO wifi_spots 
                                (site_name, address, postal_code, site_code, num_terminals, status, latitude, longitude) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                            
                            $stmt->execute([
                                $site_name, $address, $postal_code, $site_code, 
                                $num_terminals, $status, $geoPoint['latitude'], $geoPoint['longitude']
                            ]);
                            
                            $importedCount++;
                        } catch (PDOException $e) {
                            if ($e->getCode() != 23000) { // Ignorer les doublons
                                error_log("Import error: " . $e->getMessage());
                            }
                        }
                    }
                    fclose($handle);
                }
            }
            
            $db->commit();
            $success = true;
        } catch (Exception $e) {
            $db->rollBack();
            $errors[] = "Erreur lors de l'import: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importer des données - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <h1 class="h2">Importer des spots WiFi</h1>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        Import terminé avec succès! <?= $importedCount ?> spots ont été importés.
                    </div>
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
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Format attendu</h5>
                        <p>Le fichier CSV doit contenir les colonnes suivantes (séparées par des points-virgules):</p>
                        <ul>
                            <li>Nom du site</li>
                            <li>Adresse</li>
                            <li>Code postal</li>
                            <li>Code Site</li>
                            <li>Nombre de bornes</li>
                            <li>Etat du site</li>
                            <li>geo_shape (ignoré)</li>
                            <li>geo_point_2d (format: "latitude,longitude")</li>
                        </ul>
                    </div>
                </div>
                
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="data_file" class="form-label">Fichier de données</label>
                        <input class="form-control" type="file" id="data_file" name="data_file" accept=".csv,.json" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Importer</button>
                </form>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>