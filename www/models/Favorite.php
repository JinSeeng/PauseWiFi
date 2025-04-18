<?php
class Favorite {
    private $db;
    private $userModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($db);
    }
    
    public function isFavorite($userId, $spotId) {
        try {
            $query = "SELECT COUNT(*) FROM favorites 
                     WHERE user_id = :user_id AND spot_id = :spot_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':spot_id', $spotId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database error in isFavorite: " . $e->getMessage());
            return false;
        }
    }
    
    public function addFavorite($userId, $spotId) {
        try {
            // Empêcher les admins d'ajouter des favoris
            if ($this->userModel->isAdmin($userId)) {
                return false;
            }

            if ($this->isFavorite($userId, $spotId)) {
                return true;
            }

            $query = "INSERT INTO favorites (user_id, spot_id) 
                     VALUES (:user_id, :spot_id)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':spot_id', $spotId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error in addFavorite: " . $e->getMessage());
            return false;
        }
    }
    
    public function removeFavorite($userId, $spotId) {
        try {
            if ($this->userModel->isAdmin($userId)) {
                return false;
            }

            if (!$this->isFavorite($userId, $spotId)) {
                return true;
            }
            
            $query = "DELETE FROM favorites 
                     WHERE user_id = :user_id AND spot_id = :spot_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':spot_id', $spotId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error in removeFavorite: " . $e->getMessage());
            return false;
        }
    }
    
    public function toggleFavorite($userId, $spotId) {
        try {
            if ($this->userModel->isAdmin($userId)) {
                return false;
            }
            
            if ($this->isFavorite($userId, $spotId)) {
                return $this->removeFavorite($userId, $spotId) ? 'removed' : false;
            } else {
                return $this->addFavorite($userId, $spotId) ? 'added' : false;
            }
        } catch (PDOException $e) {
            error_log("Database error in toggleFavorite: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUserFavorites($userId) {
        try {
            $query = "SELECT ws.* FROM wifi_spots ws
                     JOIN favorites f ON ws.id = f.spot_id
                     WHERE f.user_id = :user_id
                     ORDER BY ws.site_name ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getUserFavorites: " . $e->getMessage());
            return [];
        }
    }

    public function countUserFavorites($userId) {
        try {
            $query = "SELECT COUNT(*) FROM favorites WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database error in countUserFavorites: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getUserFavoritesPaginated($userId, $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            
            $query = "SELECT ws.* FROM wifi_spots ws
                     JOIN favorites f ON ws.id = f.spot_id
                     WHERE f.user_id = :user_id
                     ORDER BY ws.site_name ASC
                     LIMIT :offset, :perPage";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getUserFavoritesPaginated: " . $e->getMessage());
            return [];
        }
    }
}