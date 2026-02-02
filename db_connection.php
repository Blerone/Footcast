<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';

function footcast_db(): mysqli
{
    return Database::getConnection();
}

function footcast_db_close($conn = null): void
{
    Database::closeConnection($conn instanceof mysqli ? $conn : null);
}
