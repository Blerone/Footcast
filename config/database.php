<?php
declare(strict_types=1);

if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'footcast');
}

final class Database
{
    private static ?mysqli $connection = null;

    private function __construct()
    {
    }

    public static function getConnection(): mysqli
    {
        if (self::$connection instanceof mysqli) {
            return self::$connection;
        }

        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($conn->connect_error) {
            throw new Exception('Database connection failed: ' . $conn->connect_error);
        }

        $conn->set_charset('utf8mb4');
        self::$connection = $conn;
        return $conn;
    }

    public static function closeConnection(?mysqli $conn = null): void
    {
        if ($conn instanceof mysqli) {
            if (@$conn->ping()) {
                $conn->close();
            }
            if ($conn === self::$connection) {
                self::$connection = null;
            }
            return;
        }

        if (self::$connection instanceof mysqli) {
            if (@self::$connection->ping()) {
                self::$connection->close();
            }
            self::$connection = null;
        }
    }
}

function getDBConnection(): mysqli
{
    return Database::getConnection();
}

function closeDBConnection($conn = null): void
{
    Database::closeConnection($conn instanceof mysqli ? $conn : null);
}
