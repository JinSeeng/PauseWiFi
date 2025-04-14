<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User(Database::getInstance());
    }
    
    public function register() {
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];
            
            if (empty($username)) $errors[] = "Le nom d'utilisateur est requis";
            if (empty($email)) $errors[] = "L'email est requis";
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'email n'est pas valide";
            if (empty($password)) $errors[] = "Le mot de passe est requis";
            if ($password !== $confirmPassword) $errors[] = "Les mots de passe ne correspondent pas";
            
            if (empty($errors)) {
                if ($this->userModel->createUser($username, $email, $password)) {
                    $_SESSION['success'] = "Inscription réussie! Vous pouvez maintenant vous connecter.";
                    header('Location: /login');
                    exit;
                } else {
                    $errors[] = "Une erreur est survenue lors de l'inscription";
                }
            }
        }
        
        require_once __DIR__ . '/../views/register.php';
    }
    
    public function login() {
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            
            $user = $this->userModel->getUserByEmail($email);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                header('Location: /');
                exit;
            } else {
                $errors[] = "Email ou mot de passe incorrect";
            }
        }
        
        require_once __DIR__ . '/../views/login.php';
    }
    
    public function logout() {
        session_destroy();
        header('Location: /');
        exit;
    }
}
?>