<?php
/**
 * Classe pour gérer les utilisateurs
 */
class User {
    private $db; // Connexion à la base de données
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Crée un nouvel utilisateur
     * @param string $username - Nom d'utilisateur
     * @param string $email - Email
     * @param string $password - Mot de passe (non hashé)
     * @return bool - True si la création a réussi
     */
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
    
    /**
     * Récupère un utilisateur par son email
     * @param string $email - Email de l'utilisateur
     * @return array|false - Données de l'utilisateur ou false si non trouvé
     */
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
    
    /**
     * Récupère un utilisateur par son ID
     * @param int $id - ID de l'utilisateur
     * @return array - Données de l'utilisateur
     */
    public function getUserById($id) {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un utilisateur par son nom d'utilisateur
     * @param string $username - Nom d'utilisateur
     * @return array - Données de l'utilisateur
     */
    public function getUserByUsername($username) {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère tous les utilisateurs (sans les mots de passe)
     * @return array - Liste des utilisateurs
     */
    public function getAllUsers() {
        $query = "SELECT id, username, email, role, created_at FROM users ORDER BY username ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Met à jour les informations d'un utilisateur
     * @param int $id - ID de l'utilisateur
     * @param array $data - Nouvelles données
     * @return bool - True si la mise à jour a réussi
     */
    public function updateUser($id, $data) {
        $query = "UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':role', $data['role']);
        
        return $stmt->execute();
    }
    
    /**
     * Supprime un utilisateur
     * @param int $id - ID de l'utilisateur
     * @return bool - True si la suppression a réussi
     */
    public function deleteUser($id) {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Vérifie si un utilisateur est administrateur
     * @param int $userId - ID de l'utilisateur
     * @return bool - True si l'utilisateur est admin
     */
    public function isAdmin($userId) {
        $query = "SELECT role FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result && $result['role'] === 'admin';
    }

    /**
     * Met à jour la photo de profil d'un utilisateur
     * @param int $userId - ID de l'utilisateur
     * @param string $filename - Nom du fichier image
     * @return bool - True si la mise à jour a réussi
     */
    public function updateProfilePicture($userId, $filename) {
        $query = "UPDATE users SET profile_picture = :picture WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':picture', $filename);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Récupère la photo de profil d'un utilisateur
     * @param int $userId - ID de l'utilisateur
     * @return string|null - Nom du fichier image ou null
     */
    public function getProfilePicture($userId) {
        $query = "SELECT profile_picture FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['profile_picture'] ?? null;
    }

    /**
     * Met à jour le profil d'un utilisateur (username et email)
     * @param int $userId - ID de l'utilisateur
     * @param string $username - Nouveau nom d'utilisateur
     * @param string $email - Nouvel email
     * @return bool - True si la mise à jour a réussi
     */
    public function updateUserProfile($userId, $username, $email) {
        $query = "UPDATE users SET username = :username, email = :email 
                WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    /**
     * Met à jour le rôle d'un utilisateur
     * @param int $id - ID de l'utilisateur
     * @param string $role - Nouveau rôle ('admin' ou 'user')
     * @return bool - True si la mise à jour a réussi
     */
    public function updateUserRole($id, $role) {
        $query = "UPDATE users SET role = :role WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':role', $role);
        return $stmt->execute();
    }

    /**
     * Met à jour le mot de passe d'un utilisateur
     * @param int $userId - ID de l'utilisateur
     * @param string $hashedPassword - Mot de passe hashé
     * @return bool - True si la mise à jour a réussi
     */
    public function updatePassword($userId, $hashedPassword) {
        $query = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':password', $hashedPassword);
        return $stmt->execute();
    }

    /**
     * Vérifie si un mot de passe est suffisamment fort
     * @param string $password - Mot de passe à vérifier
     * @return bool - True si le mot de passe est fort
     */
    public function isPasswordStrong($password) {
        // Au moins 8 caractères, 1 minuscule, 1 majuscule, 1 chiffre et 1 caractère spécial
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
    }
}
?>