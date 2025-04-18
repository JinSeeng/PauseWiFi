<?php
/**
 * Classe pour gérer les logs d'activité des utilisateurs
 */
class ActivityLog {
    private $db; // Connexion à la base de données
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Enregistre une action dans les logs
     * @param int $userId - ID de l'utilisateur
     * @param string $action - Type d'action (ex: 'login', 'logout')
     * @param string|null $details - Détails supplémentaires
     * @param string|null $ipAddress - Adresse IP de l'utilisateur
     * @return bool - True si l'insertion a réussi
     */
    public function logAction($userId, $action, $details = null, $ipAddress = null) {
        $query = "INSERT INTO activity_logs (user_id, action, details, ip_address) 
                 VALUES (:user_id, :action, :details, :ip_address)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':ip_address', $ipAddress);
        
        return $stmt->execute();
    }
    
    /**
     * Récupère les logs d'un utilisateur spécifique
     * @param int $userId - ID de l'utilisateur
     * @param int $limit - Nombre maximum de logs à retourner
     * @return array - Liste des logs
     */
    public function getUserLogs($userId, $limit = 10) {
        $query = "SELECT * FROM activity_logs 
                 WHERE user_id = :user_id 
                 ORDER BY created_at DESC 
                 LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les logs récents de tous les utilisateurs
     * @param int $limit - Nombre maximum de logs à retourner
     * @return array - Liste des logs avec infos utilisateur
     */
    public function getRecentLogs($limit = 50) {
        $query = "SELECT al.*, u.username 
                 FROM activity_logs al
                 LEFT JOIN users u ON al.user_id = u.id
                 ORDER BY al.created_at DESC 
                 LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>