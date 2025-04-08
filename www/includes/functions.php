<?php
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function displayError($message) {
    echo "<div class='alert alert-danger'>$message</div>";
}

function displaySuccess($message) {
    echo "<div class='alert alert-success'>$message</div>";
}

function validateCoordinates($lat, $lng) {
    return is_numeric($lat) && is_numeric($lng) && 
           $lat >= -90 && $lat <= 90 && 
           $lng >= -180 && $lng <= 180;
}

function parseGeoPoint($geoPoint) {
    if (preg_match('/^(\d+\.\d+),\s*(\d+\.\d+)$/', $geoPoint, $matches)) {
        return [
            'latitude' => $matches[1],
            'longitude' => $matches[2]
        ];
    }
    return false;
}

function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // km
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    $a = sin($dLat/2) * sin($dLat/2) + 
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
         sin($dLon/2) * sin($dLon/2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
    return $earthRadius * $c;
}

function isFileAllowed($filename, $allowedTypes) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, $allowedTypes);
}