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
    return Database::getConnection();
}

function footcast_db_close($conn) {
    Database::closeConnection($conn);
}

final class Database
{
    private static ?mysqli $connection = null;

    private function __construct()
    {
    }

    public static function getConnection(): mysqli
    {
        if (function_exists('getDBConnection')) {
            return getDBConnection();
        }

        if (self::$connection instanceof mysqli) {
            return self::$connection;
        }

        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            http_response_code(500);
            echo 'Database connection failed.';
            exit;
        }
        $conn->set_charset('utf8mb4');
        self::$connection = $conn;
        return $conn;
    }

    public static function closeConnection($conn = null): void
    {
        if (function_exists('closeDBConnection')) {
            closeDBConnection($conn);
            return;
        }

        if ($conn && $conn instanceof mysqli) {
            $conn->close();
            if ($conn === self::$connection) {
                self::$connection = null;
            }
            return;
        }

        if (self::$connection instanceof mysqli) {
            self::$connection->close();
            self::$connection = null;
        }
    }
}
