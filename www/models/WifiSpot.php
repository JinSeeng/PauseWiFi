<?php
/**
 * Classe pour gérer les spots WiFi
 */
class WifiSpot {
    private $db; // Connexion à la base de données
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Récupère tous les spots WiFi
     * @return array - Liste des spots
     */
    public function getAllSpots() {
        $query = "SELECT * FROM wifi_spots ORDER BY site_name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère un spot par son ID
     * @param int $id - ID du spot
     * @return array - Données du spot
     */
    public function getSpotById($id) {
        $query = "SELECT * FROM wifi_spots WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Recherche des spots selon différents critères
     * @param array $params - Paramètres de recherche
     * @return array - Liste des spots correspondants
     */
    public function searchSpots($params = []) {
        $query = "SELECT * FROM wifi_spots WHERE 1=1";
        $conditions = [];
        $bindValues = [];
        
        // Filtre par texte de recherche
        if (!empty($params['search'])) {
            $conditions[] = "(site_name LIKE :search OR address LIKE :search)";
            $bindValues[':search'] = '%' . $params['search'] . '%';
        }
        
        // Filtre par arrondissement
        if (!empty($params['arrondissement']) && $params['arrondissement'] !== 'all') {
            $conditions[] = "arrondissement = :arrondissement";
            $bindValues[':arrondissement'] = $params['arrondissement'];
        }
        
        // Filtre par type de site
        if (!empty($params['site_type']) && $params['site_type'] !== 'all') {
            $conditions[] = "site_type = :site_type";
            $bindValues[':site_type'] = $params['site_type'];
        }
        
        // Filtre par statut
        if (!empty($params['status']) && $params['status'] !== 'all') {
            $conditions[] = "status = :status";
            $bindValues[':status'] = $params['status'];
        }
        
        // Ajout des conditions à la requête
        if (!empty($conditions)) {
            $query .= " AND " . implode(" AND ", $conditions);
        }
        
        $query .= " ORDER BY site_name ASC";
        
        $stmt = $this->db->prepare($query);
        
        // Liaison des valeurs
        foreach ($bindValues as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les spots à proximité d'une position géographique
     * @param float $latitude - Latitude de la position
     * @param float $longitude - Longitude de la position
     * @param int|null $excludeId - ID de spot à exclure
     * @param float $radius - Rayon de recherche en km
     * @param int $limit - Nombre maximum de résultats
     * @param array $params - Paramètres de recherche supplémentaires
     * @return array - Liste des spots à proximité
     */
    public function getNearbySpots($latitude, $longitude, $excludeId = null, $radius = 1, $limit = 3, $params = []) {
        // Requête avec calcul de distance (formule haversine)
        $query = "SELECT 
                    id,
                    site_name,
                    site_type,
                    address,
                    arrondissement,
                    status,
                    latitude,
                    longitude,
                    (6371 * ACOS(
                        COS(RADIANS(:latitude)) * 
                        COS(RADIANS(latitude)) * 
                        COS(RADIANS(longitude) - RADIANS(:longitude)) + 
                        SIN(RADIANS(:latitude)) * 
                        SIN(RADIANS(latitude))
                    )) AS distance
                  FROM wifi_spots
                  WHERE (6371 * ACOS(
                        COS(RADIANS(:latitude)) * 
                        COS(RADIANS(latitude)) * 
                        COS(RADIANS(longitude) - RADIANS(:longitude)) + 
                        SIN(RADIANS(:latitude)) * 
                        SIN(RADIANS(latitude))
                    )) <= :radius";
        
        // Exclusion d'un spot spécifique
        if ($excludeId !== null) {
            $query .= " AND id != :exclude_id";
        }
        
        // Filtres supplémentaires
        if (!empty($params['search'])) {
            $query .= " AND (site_name LIKE :search OR address LIKE :search)";
        }
        
        if (!empty($params['arrondissement']) && $params['arrondissement'] !== 'all') {
            $query .= " AND arrondissement = :arrondissement";
        }
        
        if (!empty($params['site_type']) && $params['site_type'] !== 'all') {
            $query .= " AND site_type = :site_type";
        }
        
        if (!empty($params['status']) && $params['status'] !== 'all') {
            $query .= " AND status = :status";
        }
        
        $query .= " ORDER BY distance ASC LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':latitude', $latitude);
        $stmt->bindValue(':longitude', $longitude);
        $stmt->bindValue(':radius', $radius);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        
        if ($excludeId !== null) {
            $stmt->bindValue(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        
        if (!empty($params['search'])) {
            $stmt->bindValue(':search', '%' . $params['search'] . '%');
        }
        
        if (!empty($params['arrondissement']) && $params['arrondissement'] !== 'all') {
            $stmt->bindValue(':arrondissement', $params['arrondissement']);
        }
        
        if (!empty($params['site_type']) && $params['site_type'] !== 'all') {
            $stmt->bindValue(':site_type', $params['site_type']);
        }
        
        if (!empty($params['status']) && $params['status'] !== 'all') {
            $stmt->bindValue(':status', $params['status']);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère des spots aléatoires
     * @param array $excludeIds - IDs de spots à exclure
     * @param int $limit - Nombre maximum de résultats
     * @return array - Liste des spots aléatoires
     */
    public function getRandomSpots($excludeIds = [], $limit = 5) {
        $query = "SELECT * FROM wifi_spots";
        
        // Exclusion des spots spécifiés
        if (!empty($excludeIds)) {
            $query .= " WHERE id NOT IN (" . implode(',', array_fill(0, count($excludeIds), '?')) . ")";
        }
        
        $query .= " ORDER BY RAND() LIMIT ?";
        
        $stmt = $this->db->prepare($query);
        
        // Liaison des valeurs pour les exclusions
        $i = 1;
        if (!empty($excludeIds)) {
            foreach ($excludeIds as $id) {
                $stmt->bindValue($i++, $id, PDO::PARAM_INT);
            }
        }
        $stmt->bindValue($i, $limit, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les spots les plus populaires (avec le plus de favoris)
     * @param int $limit - Nombre maximum de résultats
     * @return array - Liste des spots populaires
     */
    public function getPopularSpots($limit = 5) {
        $query = "SELECT ws.*, COUNT(f.id) as favorite_count
                  FROM wifi_spots ws
                  LEFT JOIN favorites f ON ws.id = f.spot_id
                  GROUP BY ws.id
                  ORDER BY favorite_count DESC, RAND()
                  LIMIT ?";
                  
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crée un nouveau spot WiFi
     * @param array $data - Données du nouveau spot
     * @return bool - True si la création a réussi
     */
    public function createSpot($data) {
        $query = "INSERT INTO wifi_spots 
                 (site_name, address, postal_code, site_code, num_bornes, status, latitude, longitude, arrondissement) 
                 VALUES 
                 (:site_name, :address, :postal_code, :site_code, :num_bornes, :status, :latitude, :longitude, :arrondissement)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute($data);
    }
    
    /**
     * Met à jour un spot WiFi existant
     * @param int $id - ID du spot
     * @param array $data - Nouvelles données
     * @return bool - True si la mise à jour a réussi
     */
    public function updateSpot($id, $data) {
        $query = "UPDATE wifi_spots SET 
            site_name = :site_name,
            site_type = :site_type,
            address = :address,
            postal_code = :postal_code,
            site_code = :site_code,
            num_bornes = :num_bornes,
            status = :status,
            latitude = :latitude,
            longitude = :longitude,
            arrondissement = :arrondissement
            WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':site_name', $data['site_name']);
        $stmt->bindParam(':site_type', $data['site_type']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':postal_code', $data['postal_code']);
        $stmt->bindParam(':site_code', $data['site_code']);
        $stmt->bindParam(':num_bornes', $data['num_bornes'], PDO::PARAM_INT);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':latitude', $data['latitude']);
        $stmt->bindParam(':longitude', $data['longitude']);
        $stmt->bindParam(':arrondissement', $data['arrondissement'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Supprime un spot WiFi
     * @param int $id - ID du spot
     * @return bool - True si la suppression a réussi
     */
    public function deleteSpot($id) {
        $query = "DELETE FROM wifi_spots WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>