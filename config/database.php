<?php
/**
 * PostgreSQL Database Connection Configuration
 * -------------------------------------------------------------
 * Configured for Nginx + PHP 8 + PostgreSQL Docker / Render / Cloud environment.
 */

$databaseUrl = getenv('DATABASE_URL');
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '5432';
$dbname = getenv('DB_NAME') ?: 'siwes_db';
$username = getenv('DB_USER') ?: 'postgres';
$password = getenv('DB_PASS') ?: 'postgres';
$driver = getenv('DB_DRIVER') ?: 'pgsql';

if ($databaseUrl) {
    $parsedUrl = parse_url($databaseUrl);
    if ($parsedUrl) {
        $driver   = 'pgsql';
        $host     = $parsedUrl['host'] ?? $host;
        $port     = $parsedUrl['port'] ?? 5432;
        $username = $parsedUrl['user'] ?? $username;
        $password = $parsedUrl['pass'] ?? $password;
        $dbname   = ltrim($parsedUrl['path'] ?? $dbname, '/');
    }
}

return [
    'driver'    => $driver,
    'host'      => $host,
    'port'      => $port,
    'dbname'    => $dbname,
    'username'  => $username,
    'password'  => $password,
    'charset'   => 'utf8',
    'options'   => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
];

