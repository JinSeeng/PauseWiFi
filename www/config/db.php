<?php
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $host = '127.0.0.1';
        $port = '8889';
        $dbname = 'pause_wifi';
        $username = 'root';
        $password = 'root';
        $socket = '/Applications/MAMP/tmp/mysql/mysql.sock';

        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;unix_socket=$socket";
            $this->connection = new PDO($dsn, $username, $password);
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