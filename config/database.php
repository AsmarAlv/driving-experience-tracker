<?php
/**
 * Database Configuration File
 * Uses PDO for secure database connection
 */

// Database credentials
define('DB_HOST', 'mysql-asmar.alwaysdata.net'); 
define('DB_NAME', 'asmar_driving_experience_db');  
define('DB_USER', 'asmar_drv_exp');                   
define('DB_PASS', 'DuXyYJ5V6\/<');                        
define('DB_CHARSET', 'utf8mb4');

// Create PDO instance
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
} catch (PDOException $e) {
    // Log error and show user-friendly message
    error_log("Database Connection Error: " . $e->getMessage());
    die("Connection failed. Please try again later.");
}

/**
 * Function to get database connection
 * @return PDO
 */
function getConnection() {
    global $pdo;
    return $pdo;
}
?>
