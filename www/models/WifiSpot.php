<?php
class WifiSpot {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAllSpots() {
        $query = "SELECT * FROM wifi_spots ORDER BY site_name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getSpotById($id) {
        $query = "SELECT * FROM wifi_spots WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function searchSpots($searchTerm = null, $arrondissement = null) {
        $query = "SELECT * FROM wifi_spots WHERE 1=1";
        $params = [];
        
        if ($searchTerm) {
            $query .= " AND (site_name LIKE :search OR address LIKE :search)";
            $params[':search'] = "%$searchTerm%";
        }
        
        if ($arrondissement) {
            $query .= " AND arrondissement = :arrondissement";
            $params[':arrondissement'] = $arrondissement;
        }
        
        $query .= " ORDER BY site_name ASC";
        $stmt = $this->db->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function createSpot($data) {
        $query = "INSERT INTO wifi_spots 
                 (site_name, address, postal_code, site_code, num_bornes, status, latitude, longitude, arrondissement) 
                 VALUES 
                 (:site_name, :address, :postal_code, :site_code, :num_bornes, :status, :latitude, :longitude, :arrondissement)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute($data);
    }
    
    public function updateSpot($id, $data) {
        $query = "UPDATE wifi_spots SET 
                 site_name = :site_name, 
                 address = :address, 
                 postal_code = :postal_code, 
                 site_code = :site_code, 
                 num_bornes = :num_bornes, 
                 status = :status, 
                 latitude = :latitude, 
                 longitude = :longitude, 
                 arrondissement = :arrondissement 
                 WHERE id = :id";
        
        $data[':id'] = $id;
        $stmt = $this->db->prepare($query);
        return $stmt->execute($data);
    }
    
    public function deleteSpot($id) {
        $query = "DELETE FROM wifi_spots WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>