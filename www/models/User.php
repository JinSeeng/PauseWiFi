<?php
class User {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function createUser($username, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (username, email, password, role) 
                 VALUES (:username, :email, :password, 'user')";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        
        return $stmt->execute();
    }
    
    public function getUserByEmail($email) {
        try {
            $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getUserByEmail: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUserById($id) {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByUsername($username) {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAllUsers() {
        $query = "SELECT id, username, email, role, created_at FROM users ORDER BY username ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateUser($id, $data) {
        $query = "UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':role', $data['role']);
        
        return $stmt->execute();
    }
    
    public function deleteUser($id) {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function isAdmin($userId) {
        $query = "SELECT role FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result && $result['role'] === 'admin';
    }

    public function updateProfilePicture($userId, $filename) {
        $query = "UPDATE users SET profile_picture = :picture WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':picture', $filename);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function getProfilePicture($userId) {
        $query = "SELECT profile_picture FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['profile_picture'] ?? null;
    }

    public function updateUserProfile($userId, $username, $email) {
        $query = "UPDATE users SET username = :username, email = :email 
                WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    public function updateUserRole($id, $role) {
        $query = "UPDATE users SET role = :role WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':role', $role);
        return $stmt->execute();
    }

    public function updatePassword($userId, $hashedPassword) {
        $query = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':password', $hashedPassword);
        return $stmt->execute();
    }

    public function isPasswordStrong($password) {
        // Au moins 8 caractères, 1 minuscule, 1 majuscule, 1 chiffre et 1 caractère spécial
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
    }
}
?>