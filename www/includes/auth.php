<?php
require_once 'db.php';

class Auth {
    public static function login($username, $password) {
        $db = Database::getConnectionStatic();
        
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['logged_in'] = true;
            
            // Mise à jour du dernier login
            $update = $db->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
            $update->execute([$user['id']]);
            
            return true;
        }
        
        return false;
    }
    
    public static function logout() {
        session_unset();
        session_destroy();
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: /admin/login.php');
            exit;
        }
    }
    
    public static function createUser($username, $password, $email) {
        $db = Database::getConnectionStatic();
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("INSERT INTO admins (username, password_hash, email) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $hashedPassword, $email]);
    }
}