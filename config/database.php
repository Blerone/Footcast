<?php
/**
 * Database Configuration
 * Update these values to match your MySQL setup
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'footcast');

/**
 * Create database connection
 */
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

/**
 * Close database connection
 */
function closeDBConnection($conn) {
    if ($conn && $conn instanceof mysqli && !$conn->connect_error) {
        // Check if connection is still open by trying to ping
        try {
            if (@$conn->ping()) {
                $conn->close();
            }
        } catch (Exception $e) {
            // Connection already closed or error, ignore
        }
    }
}
?>

