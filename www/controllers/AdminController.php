<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/WifiSpot.php';
require_once __DIR__ . '/../models/User.php';

class AdminController {
    private $wifiSpotModel;
    private $userModel;
    
    public function __construct() {
        $this->wifiSpotModel = new WifiSpot(Database::getInstance());
        $this->userModel = new User(Database::getInstance());
        
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
    }
    
    public function dashboard() {
        $spots = $this->wifiSpotModel->getAllSpots();
        $users = $this->userModel->getAllUsers();
        
        require_once __DIR__ . '/../views/admin_dashboard.php';
    }
    
    public function createSpot() {
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'site_name' => $_POST['site_name'],
                'address' => $_POST['address'],
                'postal_code' => $_POST['postal_code'],
                'site_code' => $_POST['site_code'],
                'num_bornes' => $_POST['num_bornes'],
                'status' => $_POST['status'],
                'latitude' => $_POST['latitude'],
                'longitude' => $_POST['longitude'],
                'arrondissement' => $_POST['arrondissement']
            ];
            
            if ($this->wifiSpotModel->createSpot($data)) {
                $_SESSION['success'] = "Spot créé avec succès";
                header('Location: /admin');
                exit;
            } else {
                $errors[] = "Erreur lors de la création du spot";
            }
        }
        
        $this->dashboard();
    }
    
    public function deleteSpot($id) {
        if ($this->wifiSpotModel->deleteSpot($id)) {
            $_SESSION['success'] = "Spot supprimé avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression du spot";
        }
        
        header('Location: /admin');
        exit;
    }
}
?>