<?php
declare(strict_types=1);

if (file_exists(__DIR__ . '/php/config/database.php')) {
    require_once __DIR__ . '/php/config/database.php';
} else {
    if (!defined('DB_HOST')) {
        define('DB_HOST', 'localhost');
    }
    if (!defined('DB_NAME')) {
        define('DB_NAME', 'footcast');
    }
    if (!defined('DB_USER')) {
        define('DB_USER', 'root');
    }
    if (!defined('DB_PASS')) {
        define('DB_PASS', '');
    }
}

/**
 * Legacy function for backward compatibility
 * @return mysqli
 */
function footcast_db(): mysqli{ 
    if (function_exists('getDBConnection')) {
        return getDBConnection();
    }
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        http_response_code(500);
        echo 'Database connection failed.';
        exit;
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

function footcast_db_close($conn) {
    if (function_exists('closeDBConnection')) {
        closeDBConnection($conn);
    } else {
        if ($conn && $conn instanceof mysqli) {
            $conn->close();
        }
    }
}
