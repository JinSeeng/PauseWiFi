<?php
// Configuration de l'application
define('APP_NAME', 'Pause WiFi');
define('APP_VERSION', '1.0.0');
define('APP_DEBUG', true);

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_PORT', '8889');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'pause_wifi');
define('DB_SOCKET', '/Applications/MAMP/tmp/mysql/mysql.sock');

// Chemins de l'application
define('BASE_URL', 'http://localhost/pause-wifi/www');
define('UPLOAD_DIR', __DIR__ . '/../uploads');

// Autres configurations
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['csv', 'json']);