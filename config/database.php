<?php
declare(strict_types=1);

function env(string $key, string $default = ''): string {
    $v = getenv($key);
    if ($v !== false && $v !== '') return $v;
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') return (string)$_ENV[$key];
    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return (string)$_SERVER[$key];
    return $default;
}

const DB_HOST = 'localhost';
const DB_NAME = 'keuangan_bagas';
const DB_USER = 'root';
const DB_PASS = '12345678';
const DB_PORT = '3306';

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $host = env('DB_HOST', env('MYSQLHOST', env('MYSQL_HOST', DB_HOST)));
    $port = env('DB_PORT', env('MYSQLPORT', env('MYSQL_PORT', DB_PORT)));
    $name = env('DB_NAME', env('MYSQLDATABASE', env('MYSQL_DATABASE', env('MYSQL_DB', DB_NAME))));
    $user = env('DB_USER', env('MYSQLUSER', env('MYSQL_USER', DB_USER)));
    $pass = env('DB_PASS', env('MYSQLPASSWORD', env('MYSQL_PASSWORD', env('MYSQL_ROOT_PASSWORD', DB_PASS))));

    // Support DATABASE_URL / MYSQL_URL (mysql://user:pass@host:port/db)
    $dbUrl = env('DATABASE_URL', env('MYSQL_URL', env('MYSQL_PUBLIC_URL', '')));
    if ($dbUrl !== '' && str_starts_with($dbUrl, 'mysql://')) {
        $parts = parse_url($dbUrl);
        if ($parts !== false) {
            if (!empty($parts['host'])) $host = $parts['host'];
            if (!empty($parts['port'])) $port = (string)$parts['port'];
            if (!empty($parts['user'])) $user = $parts['user'];
            if (!empty($parts['pass'])) $pass = $parts['pass'];
            if (!empty($parts['path'])) $name = ltrim($parts['path'], '/');
        }
    }

    $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $name . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}