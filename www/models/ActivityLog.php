<?php

class ActivityLog {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
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