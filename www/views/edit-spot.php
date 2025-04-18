<?php
// Inclure l'en-tête et vérifier les permissions
require_once __DIR__ . '/partials/header.php';

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /?page=login');
    exit;
}

// Vérifier si l'ID du spot est valide
if (!isset($_GET['id']) || !$spot = $wifiSpotModel->getSpotById($_GET['id'])) {
    header('Location: /?page=admin');
    exit;
}

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Préparation des données pour la mise à jour
    $data = [
        'site_name' => $_POST['site_name'],
        'site_type' => $_POST['site_type'],
        'address' => $_POST['address'],
        'postal_code' => $_POST['postal_code'],
        'site_code' => $_POST['site_code'] ?? null,
        'num_bornes' => (int)($_POST['num_bornes'] ?? 1),
        'status' => $_POST['status'],
        'latitude' => (float)$_POST['latitude'],
        'longitude' => (float)$_POST['longitude'],
        'arrondissement' => (int)$_POST['arrondissement']
    ];
    
    // Tentative de mise à jour du spot
    if ($wifiSpotModel->updateSpot($spot['id'], $data)) {
        // Journaliser l'activité
        $activityLog->logAction(
            $_SESSION['user_id'],
            'spot_updated',
            'Spot mis à jour ID: ' . $spot['id'],
            $_SERVER['REMOTE_ADDR']
        );
        
        // Redirection avec message de succès
        $_SESSION['success'] = "Spot mis à jour avec succès";
        header('Location: /?page=admin');
        exit;
    } else {
        $error = "Erreur lors de la mise à jour du spot";
    }
}
?>

<div class="edit-spot">
    <h1 class="edit-spot__title">Éditer le spot WiFi</h1>
    
    <!-- Affichage des erreurs -->
    <?php if (isset($error)): ?>
        <div class="edit-spot__alert edit-spot__alert--error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <!-- Formulaire d'édition -->
    <form action="/?page=edit-spot&id=<?= $spot['id'] ?>" method="POST" class="edit-spot__form">
        <!-- Informations de base -->
        <div class="edit-spot__form-group">
            <label for="site_name" class="edit-spot__label">Nom du site *</label>
            <input type="text" id="site_name" name="site_name" class="edit-spot__input" 
                   value="<?= htmlspecialchars($spot['site_name']) ?>" required>
        </div>
        
        <div class="edit-spot__form-group">
            <label for="site_type" class="edit-spot__label">Type de lieu *</label>
            <select id="site_type" name="site_type" class="edit-spot__select" required>
                <option value="Bibliothèque" <?= $spot['site_type'] === 'Bibliothèque' ? 'selected' : '' ?>>Bibliothèque</option>
                <option value="Parc" <?= $spot['site_type'] === 'Parc' ? 'selected' : '' ?>>Parc</option>
                <option value="Centre sportif" <?= $spot['site_type'] === 'Centre sportif' ? 'selected' : '' ?>>Centre sportif</option>
                <option value="Mairie" <?= $spot['site_type'] === 'Mairie' ? 'selected' : '' ?>>Mairie</option>
                <option value="Musée" <?= $spot['site_type'] === 'Musée' ? 'selected' : '' ?>>Musée</option>
                <option value="Hotel" <?= $spot['site_type'] === 'Hotel' ? 'selected' : '' ?>>Hôtel</option>
                <option value="Autre" <?= $spot['site_type'] === 'Autre' ? 'selected' : '' ?>>Autre</option>
            </select>
        </div>
        
        <div class="edit-spot__form-group">
            <label for="address" class="edit-spot__label">Adresse *</label>
            <input type="text" id="address" name="address" class="edit-spot__input" 
                   value="<?= htmlspecialchars($spot['address']) ?>" required>
        </div>
        
        <!-- Code postal et arrondissement -->
        <div class="edit-spot__form-row">
            <div class="edit-spot__form-group">
                <label for="postal_code" class="edit-spot__label">Code postal *</label>
                <input type="text" id="postal_code" name="postal_code" class="edit-spot__input" 
                       value="<?= htmlspecialchars($spot['postal_code']) ?>" required>
            </div>
            
            <div class="edit-spot__form-group">
                <label for="arrondissement" class="edit-spot__label">Arrondissement *</label>
                <select id="arrondissement" name="arrondissement" class="edit-spot__select" required>
                    <?php for ($i = 1; $i <= 20; $i++): ?>
                        <option value="<?= $i ?>" <?= $spot['arrondissement'] == $i ? 'selected' : '' ?>><?= $i ?>e</option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        
        <!-- Détails techniques -->
        <div class="edit-spot__form-row">
            <div class="edit-spot__form-group">
                <label for="site_code" class="edit-spot__label">Code site</label>
                <input type="text" id="site_code" name="site_code" class="edit-spot__input" 
                       value="<?= htmlspecialchars($spot['site_code'] ?? '') ?>">
            </div>
            
            <div class="edit-spot__form-group">
                <label for="num_bornes" class="edit-spot__label">Nombre de bornes</label>
                <input type="number" id="num_bornes" name="num_bornes" min="1" class="edit-spot__input" 
                       value="<?= $spot['num_bornes'] ?>">
            </div>
        </div>
        
        <!-- Coordonnées géographiques -->
        <div class="edit-spot__form-row">
            <div class="edit-spot__form-group">
                <label for="latitude" class="edit-spot__label">Latitude *</label>
                <input type="text" id="latitude" name="latitude" class="edit-spot__input" 
                       value="<?= $spot['latitude'] ?>" required>
            </div>
            
            <div class="edit-spot__form-group">
                <label for="longitude" class="edit-spot__label">Longitude *</label>
                <input type="text" id="longitude" name="longitude" class="edit-spot__input" 
                       value="<?= $spot['longitude'] ?>" required>
            </div>
        </div>
        
        <!-- Statut du spot -->
        <div class="edit-spot__form-group">
            <label for="status" class="edit-spot__label">Statut *</label>
            <select id="status" name="status" class="edit-spot__select" required>
                <option value="Opérationnel" <?= $spot['status'] === 'Opérationnel' ? 'selected' : '' ?>>Opérationnel</option>
                <option value="Fermé pour travaux" <?= $spot['status'] === 'Fermé pour travaux' ? 'selected' : '' ?>>Fermé pour travaux</option>
                <option value="En déploiement" <?= $spot['status'] === 'En déploiement' ? 'selected' : '' ?>>En déploiement</option>
                <option value="En étude" <?= $spot['status'] === 'En étude' ? 'selected' : '' ?>>En étude</option>
            </select>
        </div>
        
        <!-- Boutons d'action -->
        <button type="submit" class="edit-spot__submit">Mettre à jour</button>
        <a href="/?page=admin" class="edit-spot__cancel">Annuler</a>
    </form>
</div>

<?php 
// Inclure le pied de page
require_once __DIR__ . '/partials/footer.php'; 
?>