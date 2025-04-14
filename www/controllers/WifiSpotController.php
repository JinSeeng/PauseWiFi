<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/WifiSpot.php';
require_once __DIR__ . '/../models/Favorite.php';

class WifiSpotController {
    private $wifiSpotModel;
    private $favoriteModel;
    
    public function __construct($wifiSpotModel, $favoriteModel) {
        $this->wifiSpotModel = $wifiSpotModel;
        $this->favoriteModel = $favoriteModel;
    }

    public function mapView() {
        $spots = $this->wifiSpotModel->getAllSpots();
        require_once __DIR__ . '/../views/home.php'; // Nouveau fichier pour la carte
    }
    
    public function index() {
        $spots = $this->wifiSpotModel->getAllSpots();
        require_once __DIR__ . '/../views/list.php';
    }
    
    public function show($id) {
        $spot = $this->wifiSpotModel->getSpotById($id);
        
        if ($spot) {
            $isFavorite = isset($_SESSION['user_id']) 
                ? $this->favoriteModel->isFavorite($_SESSION['user_id'], $spot['id'])
                : false;
                
            require_once __DIR__ . '/../views/wifi_detail.php';
        } else {
            header("Location: /not-found");
            exit;
        }
    }
    
    public function search() {
        $arrondissement = $_GET['arrondissement'] ?? null;
        $searchTerm = $_GET['search'] ?? null;
        
        $spots = $this->wifiSpotModel->searchSpots($searchTerm, $arrondissement);
        
        // Pour les requêtes AJAX, renvoyer du JSON
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            
            // Ajouter l'information des favoris si l'utilisateur est connecté
            if (isset($_SESSION['user_id'])) {
                $spots = array_map(function($spot) {
                    $spot['isFavorite'] = $this->favoriteModel->isFavorite($_SESSION['user_id'], $spot['id']);
                    return $spot;
                }, $spots);
            }
            
            echo json_encode($spots);
            exit;
        }
        
        // Pour les requêtes normales, charger la vue avec les spots
        require_once __DIR__ . '/../views/wifi_list.php';
    }
    
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
?>