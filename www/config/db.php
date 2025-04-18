<?php
class Database {
    // Instance unique de la classe (singleton)
    private static $instance = null;
    // Connexion à la base de données
    private $connection;
    
    // Constructeur privé pour empêcher l'instanciation directe
    private function __construct() {
        // Paramètres de connexion à la base de données
        $host = 'mysql-sellianade.alwaysdata.net';
        $dbname = 'sellianade_pause_wifi';
        $user = '408839';
        $password = 'bExwim-gasdiq-curqy6';

        try {
            // Création de la connexion PDO
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $this->connection = new PDO($dsn, $user, $password);
            // Configuration des options PDO
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->exec("SET NAMES 'utf8'");
        } catch (PDOException $e) {
            // Journalisation des erreurs et message d'arrêt
            error_log("Database connection error: " . $e->getMessage());
            die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
        }
    }
    
    // Méthode pour obtenir l'instance unique de la base de données
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }
}