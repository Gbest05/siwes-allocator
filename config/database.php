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
$sslmode = getenv('DB_SSLMODE') ?: null;

if ($databaseUrl) {
    $parsedUrl = parse_url($databaseUrl);
    if ($parsedUrl) {
        $driver   = 'pgsql';
        $host     = $parsedUrl['host'] ?? $host;
        $port     = $parsedUrl['port'] ?? 5432;
        $username = isset($parsedUrl['user']) ? urldecode($parsedUrl['user']) : $username;
        $password = isset($parsedUrl['pass']) ? urldecode($parsedUrl['pass']) : $password;
        $dbname   = isset($parsedUrl['path']) ? ltrim(urldecode($parsedUrl['path']), '/') : $dbname;

        if (!empty($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
            if (!empty($queryParams['sslmode'])) {
                $sslmode = $queryParams['sslmode'];
            }
        }
    }
}

return [
    'driver'    => $driver,
    'host'      => $host,
    'port'      => $port,
    'dbname'    => $dbname,
    'username'  => $username,
    'password'  => $password,
    'sslmode'   => $sslmode,
    'charset'   => 'utf8',
    'options'   => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
];
