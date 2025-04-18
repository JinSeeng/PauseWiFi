<?php 
// Inclure l'en-tête et vérifier les privilèges admin
require_once __DIR__ . '/partials/header.php';

// Vérifier si l'utilisateur est connecté et est un admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /?page=login');
    exit;
}

// Déterminer l'onglet actif (par défaut: welcome)
$tab = $_GET['tab'] ?? 'welcome';

// Connexion à la base de données et récupération des modèles
$db = Database::getInstance();
$wifiSpotModel = new WifiSpot($db);
$userModel = new User($db);

// Récupérer les données nécessaires
$spots = $wifiSpotModel->getAllSpots();
$users = $userModel->getAllUsers();
$totalSpots = count($spots);
$totalUsers = count($users);
?>

<div class="admin">
    <h1 class="admin__title">Tableau de bord administrateur</h1>
    
    <!-- Afficher les messages de succès -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="admin__alert admin__alert--success">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <!-- Afficher les messages d'erreur -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="admin__alert admin__alert--error">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <!-- Menu de navigation -->
    <div class="admin__menu">
        <a href="/?page=admin&tab=spots" class="admin__menu-btn <?= $tab === 'spots' ? 'admin__menu-btn--active' : '' ?>">
            <i class="fas fa-wifi"></i> 
            <span>Gestion des spots</span>
            <span class="admin__badge"><?= $totalSpots ?></span>
        </a>
        
        <a href="/?page=admin&tab=users" class="admin__menu-btn <?= $tab === 'users' ? 'admin__menu-btn--active' : '' ?>">
            <i class="fas fa-users"></i>
            <span>Gestion des utilisateurs</span>
            <span class="admin__badge"><?= $totalUsers ?></span>
        </a>
        
        <a href="/?page=admin&tab=add-spot" class="admin__menu-btn <?= $tab === 'add-spot' ? 'admin__menu-btn--active' : '' ?>">
            <i class="fas fa-plus-circle"></i>
            <span>Ajouter un spot</span>
        </a>
    </div>
    
    <!-- Contenu principal -->
    <div class="admin__content">
        <?php if ($tab === 'welcome'): ?>
            <!-- Page d'accueil du dashboard -->
            <div class="admin__welcome">
                <div class="admin__welcome-header">
                    <h2 class="admin__welcome-title">Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?></h2>
                    <p class="admin__welcome-subtitle">Interface d'administration Pause WiFi</p>
                </div>
                
                <!-- Cartes de statistiques -->
                <div class="admin__stats">
                    <div class="admin__stat-card">
                        <div class="admin__stat-icon">
                            <i class="fas fa-wifi"></i>
                        </div>
                        <h3 class="admin__stat-number"><?= $totalSpots ?></h3>
                        <p class="admin__stat-label">Spots WiFi</p>
                        <a href="/?page=admin&tab=spots" class="admin__stat-link">Voir tous</a>
                    </div>
                    
                    <div class="admin__stat-card">
                        <div class="admin__stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="admin__stat-number"><?= $totalUsers ?></h3>
                        <p class="admin__stat-label">Utilisateurs</p>
                        <a href="/?page=admin&tab=users" class="admin__stat-link">Gérer</a>
                    </div>
                    
                    <div class="admin__stat-card">
                        <div class="admin__stat-icon">
                            <i class="fas fa-plus"></i>
                        </div>
                        <h3 class="admin__stat-number">Nouveau</h3>
                        <p class="admin__stat-label">Spot WiFi</p>
                        <a href="/?page=admin&tab=add-spot" class="admin__stat-link">Ajouter</a>
                    </div>
                </div>
                
                <!-- Journal d'activité récente -->
                <div class="admin__activity">
                    <h3 class="admin__activity-title">Activité récente</h3>
                    <?php
                    $activityLog = new ActivityLog($db);
                    $recentLogs = $activityLog->getRecentLogs(5);
                    ?>
                    
                    <?php if (!empty($recentLogs)): ?>
                        <ul class="admin__activity-list">
                            <?php foreach ($recentLogs as $log): ?>
                                <li class="admin__activity-item">
                                    <span class="admin__activity-time">
                                        <?= date('H:i', strtotime($log['created_at'])) ?>
                                    </span>
                                    <span class="admin__activity-details">
                                        <?= htmlspecialchars($log['username'] ?? 'Système') ?> - 
                                        <?= htmlspecialchars($log['action']) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="admin__activity-empty">Aucune activité récente</p>
                    <?php endif; ?>
                </div>
            </div>
            
        <?php elseif ($tab === 'spots'): ?>
            <!-- Gestion des spots WiFi -->
            <div class="admin__spots">
                <div class="admin__section-header">
                    <h2 class="admin__section-title">Gestion des spots WiFi</h2>
                    <div class="admin__search">
                        <input type="text" id="spot-search" class="admin__search-input" placeholder="Rechercher un spot...">
                        <i class="fas fa-search admin__search-icon"></i>
                    </div>
                </div>
                
                <!-- Tableau des spots -->
                <div class="admin__table-container">
                    <table class="admin__table">
                        <thead class="admin__table-head">
                            <tr class="admin__table-row">
                                <th class="admin__table-header">ID</th>
                                <th class="admin__table-header">Nom</th>
                                <th class="admin__table-header">Adresse</th>
                                <th class="admin__table-header">Arrond.</th>
                                <th class="admin__table-header">Statut</th>
                                <th class="admin__table-header">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="admin__table-body">
                            <?php foreach ($spots as $spot): ?>
                                <tr class="admin__table-row">
                                    <td class="admin__table-cell"><?= $spot['id'] ?></td>
                                    <td class="admin__table-cell"><?= htmlspecialchars($spot['site_name']) ?></td>
                                    <td class="admin__table-cell"><?= htmlspecialchars($spot['address']) ?></td>
                                    <td class="admin__table-cell"><?= $spot['arrondissement'] ?>e</td>
                                    <td class="admin__table-cell">
                                        <span class="admin__status admin__status--<?= strtolower(str_replace(' ', '-', $spot['status'])) ?>">
                                            <?= $spot['status'] ?>
                                        </span>
                                    </td>
                                    <td class="admin__table-cell admin__table-cell--actions">
                                        <!-- Bouton d'édition -->
                                        <a href="/?page=edit-spot&id=<?= $spot['id'] ?>" class="admin__action-btn admin__action-btn--edit" title="Éditer">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <!-- Formulaire de suppression -->
                                        <form action="/actions/admin-actions.php?action=delete-spot&id=<?= $spot['id'] ?>" method="POST" class="admin__delete-form">
                                            <button type="submit" class="admin__action-btn admin__action-btn--delete" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce spot ?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        <?php elseif ($tab === 'users'): ?>
            <!-- Gestion des utilisateurs -->
            <div class="admin__users">
                <h2 class="admin__section-title">Gestion des utilisateurs</h2>
                
                <div class="admin__table-container">
                    <table class="admin__table">
                        <thead class="admin__table-head">
                            <tr class="admin__table-row">
                                <th class="admin__table-header">ID</th>
                                <th class="admin__table-header">Nom</th>
                                <th class="admin__table-header">Email</th>
                                <th class="admin__table-header">Rôle</th>
                                <th class="admin__table-header">Inscrit le</th>
                                <th class="admin__table-header">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="admin__table-body">
                            <?php foreach ($users as $user): ?>
                                <tr class="admin__table-row">
                                    <td class="admin__table-cell"><?= $user['id'] ?></td>
                                    <td class="admin__table-cell"><?= htmlspecialchars($user['username']) ?></td>
                                    <td class="admin__table-cell"><?= htmlspecialchars($user['email']) ?></td>
                                    <td class="admin__table-cell">
                                        <!-- Formulaire de modification de rôle -->
                                        <form action="/actions/admin-actions.php?action=update-user-role&id=<?= $user['id'] ?>" method="POST" class="admin__role-form">
                                            <select name="role" class="admin__role-select" onchange="this.form.submit()" <?= $user['id'] === $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Utilisateur</option>
                                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="admin__table-cell"><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                    <td class="admin__table-cell admin__table-cell--actions">
                                        <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                            <!-- Formulaire de suppression d'utilisateur -->
                                            <form action="/actions/admin-actions.php?action=delete-user&id=<?= $user['id'] ?>" method="POST" class="admin__delete-form">
                                                <button type="submit" class="admin__action-btn admin__action-btn--delete" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        <?php elseif ($tab === 'add-spot'): ?>
            <!-- Formulaire d'ajout d'un nouveau spot -->
            <div class="admin__add-spot">
                <h2 class="admin__section-title">Ajouter un nouveau spot WiFi</h2>
                
                <form action="/actions/admin-actions.php?action=create-spot" method="POST" class="admin__form">
                    <!-- Section Informations de base -->
                    <div class="admin__form-section">
                        <h3 class="admin__form-section-title">Informations de base</h3>
                        
                        <div class="admin__form-group">
                            <label for="site_name" class="admin__form-label">Nom du site *</label>
                            <input type="text" id="site_name" name="site_name" class="admin__form-input" required>
                        </div>
                        
                        <div class="admin__form-group">
                            <label for="site_type" class="admin__form-label">Type de lieu *</label>
                            <select id="site_type" name="site_type" class="admin__form-select" required>
                                <option value="Bibliothèque">Bibliothèque</option>
                                <option value="Parc">Parc</option>
                                <option value="Centre sportif">Centre sportif</option>
                                <option value="Mairie">Mairie</option>
                                <option value="Musée">Musée</option>
                                <option value="Hotel">Hôtel</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        
                        <div class="admin__form-group">
                            <label for="address" class="admin__form-label">Adresse *</label>
                            <input type="text" id="address" name="address" class="admin__form-input" required>
                        </div>
                        
                        <div class="admin__form-row">
                            <div class="admin__form-group">
                                <label for="postal_code" class="admin__form-label">Code postal *</label>
                                <input type="text" id="postal_code" name="postal_code" class="admin__form-input" required>
                            </div>
                            
                            <div class="admin__form-group">
                                <label for="arrondissement" class="admin__form-label">Arrondissement *</label>
                                <select id="arrondissement" name="arrondissement" class="admin__form-select" required>
                                    <?php for ($i = 1; $i <= 20; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?>e</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section Détails techniques -->
                    <div class="admin__form-section">
                        <h3 class="admin__form-section-title">Détails techniques</h3>
                        
                        <div class="admin__form-row">
                            <div class="admin__form-group">
                                <label for="site_code" class="admin__form-label">Code site</label>
                                <input type="text" id="site_code" name="site_code" class="admin__form-input">
                            </div>
                            
                            <div class="admin__form-group">
                                <label for="num_bornes" class="admin__form-label">Nombre de bornes</label>
                                <input type="number" id="num_bornes" name="num_bornes" min="1" value="1" class="admin__form-input">
                            </div>
                        </div>
                        
                        <div class="admin__form-group">
                            <label for="status" class="admin__form-label">Statut *</label>
                            <select id="status" name="status" class="admin__form-select" required>
                                <option value="Opérationnel">Opérationnel</option>
                                <option value="Fermé pour travaux">Fermé pour travaux</option>
                                <option value="En déploiement">En déploiement</option>
                                <option value="En étude">En étude</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Section Coordonnées géographiques -->
                    <div class="admin__form-section">
                        <h3 class="admin__form-section-title">Coordonnées géographiques</h3>
                        
                        <div class="admin__form-row">
                            <div class="admin__form-group">
                                <label for="latitude" class="admin__form-label">Latitude *</label>
                                <input type="text" id="latitude" name="latitude" class="admin__form-input" required>
                            </div>
                            
                            <div class="admin__form-group">
                                <label for="longitude" class="admin__form-label">Longitude *</label>
                                <input type="text" id="longitude" name="longitude" class="admin__form-input" required>
                            </div>
                        </div>
                        
                        <div class="admin__form-note">
                            <p><small>* Les coordonnées doivent être au format décimal (ex: 48.8566 pour Paris)</small></p>
                        </div>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="admin__form-actions">
                        <button type="submit" class="admin__submit-btn">
                            <i class="fas fa-save"></i> Créer le spot
                        </button>
                        <a href="/?page=admin" class="admin__cancel-btn">Annuler</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Scripts JavaScript pour le tableau de bord admin
document.addEventListener('DOMContentLoaded', function() {
    // Recherche de spots en temps réel
    if (document.getElementById('spot-search')) {
        document.getElementById('spot-search').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.admin__table-body tr');
            
            // Filtrer les lignes du tableau
            rows.forEach(row => {
                const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const address = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                
                if (name.includes(searchTerm) || address.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Confirmation avant suppression
    document.querySelectorAll('.admin__delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir effectuer cette action ?')) {
                e.preventDefault();
            }
        });
    });
});
</script>

<?php 
// Inclure le pied de page
require_once __DIR__ . '/partials/footer.php'; 
?>