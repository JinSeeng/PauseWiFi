<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

checkAdmin();

$page_title = "Gestion des utilisateurs";
require_once '../../includes/header.php';

$db = new Database();
$conn = $db->getConnection();

// Récupérer les utilisateurs avec pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Filtres
$role_filter = isset($_GET['role']) ? $_GET['role'] : null;
$search_query = isset($_GET['q']) ? trim($_GET['q']) : null;

$sql = "SELECT * FROM users WHERE 1=1";
$count_sql = "SELECT COUNT(*) FROM users WHERE 1=1";
$params = [];
$count_params = [];

if ($role_filter) {
    $sql .= " AND role = ?";
    $count_sql .= " AND role = ?";
    $params[] = $role_filter;
    $count_params[] = $role_filter;
}

if ($search_query) {
    $sql .= " AND (username LIKE ? OR email LIKE ?)";
    $count_sql .= " AND (username LIKE ? OR email LIKE ?)";
    $search_param = "%$search_query%";
    $params[] = $search_param;
    $params[] = $search_param;
    $count_params[] = $search_param;
    $count_params[] = $search_param;
}

$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;

// Exécuter les requêtes
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

$count_stmt = $conn->prepare($count_sql);
$count_stmt->execute($count_params);
$total_users = $count_stmt->fetchColumn();
$total_pages = ceil($total_users / $per_page);
?>

<div class="admin-container">
    <?php require_once 'includes/admin-sidebar.php'; ?>
    
    <main class="admin-content">
        <div class="admin-header">
            <h1>Gestion des utilisateurs</h1>
            <a href="users.php?action=add" class="btn"><i class="fas fa-plus"></i> Ajouter un utilisateur</a>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> L'utilisateur a été <?php echo htmlspecialchars($_GET['success']); ?> avec succès.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> Une erreur est survenue lors de la <?php echo htmlspecialchars($_GET['error']); ?> de l'utilisateur.
            </div>
        <?php endif; ?>
        
        <div class="admin-filters">
            <form method="get" class="filter-form">
                <div class="form-group">
                    <label for="role">Rôle :</label>
                    <select id="role" name="role">
                        <option value="">Tous les rôles</option>
                        <option value="user" <?php echo $role_filter === 'user' ? 'selected' : ''; ?>>Utilisateur</option>
                        <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Administrateur</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="q">Recherche :</label>
                    <input type="text" id="q" name="q" placeholder="Nom ou email" value="<?php echo htmlspecialchars($search_query ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn"><i class="fas fa-filter"></i> Filtrer</button>
                    <a href="users.php" class="btn btn-outline"><i class="fas fa-undo"></i> Réinitialiser</a>
                </div>
            </form>
        </div>
        
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nom d'utilisateur</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date d'inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><span class="role-badge <?php echo $user['role']; ?>"><?php echo htmlspecialchars($user['role']); ?></span></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                            <td class="actions">
                                <a href="users.php?action=edit&id=<?php echo $user['id']; ?>" class="btn btn-small"><i class="fas fa-edit"></i></a>
                                <a href="process_user.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if ($total_pages > 1): ?>
                <div class="admin-pagination">
                    <?php if ($page > 1): ?>
                        <a href="users.php?page=<?php echo $page - 1; ?><?php echo $role_filter ? '&role=' . $role_filter : ''; ?><?php echo $search_query ? '&q=' . urlencode($search_query) : ''; ?>" class="btn btn-outline"><i class="fas fa-chevron-left"></i> Précédent</a>
                    <?php else: ?>
                        <span class="btn btn-outline disabled"><i class="fas fa-chevron-left"></i> Précédent</span>
                    <?php endif; ?>
                    
                    <span>Page <?php echo $page; ?> sur <?php echo $total_pages; ?></span>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="users.php?page=<?php echo $page + 1; ?><?php echo $role_filter ? '&role=' . $role_filter : ''; ?><?php echo $search_query ? '&q=' . urlencode($search_query) : ''; ?>" class="btn btn-outline">Suivant <i class="fas fa-chevron-right"></i></a>
                    <?php else: ?>
                        <span class="btn btn-outline disabled">Suivant <i class="fas fa-chevron-right"></i></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once '../../includes/footer.php'; ?>