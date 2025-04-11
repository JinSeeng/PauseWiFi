<?php
require_once 'db.php';

/**
 * Calcule la distance entre deux points géographiques
 */
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Rayon de la Terre en km

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat/2) * sin($dLat/2) + 
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
         sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));

    return $earthRadius * $c;
}

/**
 * Nettoie les données utilisateur
 */
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Récupère tous les spots Wi-Fi
 */
function getAllWifiSpots() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM wifi_spots WHERE etat = 'Opérationnel'");
    return $stmt->fetchAll();
}

/**
 * Récupère un spot Wi-Fi par son ID
 */
function getWifiSpotById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM wifi_spots WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}
?>