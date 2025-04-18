<?php
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $host = 'mysql-sellianade.alwaysdata.net';
        $dbname = 'sellianade_pause_wifi';
        $user = '408839';
        $password = 'bExwim-gasdiq-curqy6';

        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $this->connection = new PDO($dsn, $user, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->exec("SET NAMES 'utf8'");
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
        }
    }
    
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }
}
