<?php

// Load environment variables from .env
$envFile = __DIR__ . '/.env';

if (!is_file($envFile) || !is_readable($envFile)) {
    error_log('[PHP CRUD] Environment configuration file is missing or unreadable.');
    http_response_code(500);
    exit('A server error occurred. Please try again later.');
}

$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($lines === false) {
    error_log('[PHP CRUD] Unable to read environment configuration.');
    http_response_code(500);
    exit('A server error occurred. Please try again later.');
}

foreach ($lines as $line) {
    $line = trim($line);

    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }

    if (strpos($line, '=') !== false) {
        [$key, $value] = explode('=', $line, 2);

        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");

        if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

class Config
{
    protected $conn = null;

    public function __construct()
    {
        // Require all database credentials.
        $required = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];

        foreach ($required as $variable) {
            $value = getenv($variable);

            if ($value === false || trim($value) === '') {
                error_log('[PHP CRUD] Required database configuration is missing.');
                http_response_code(500);
                exit('A server error occurred. Please try again later.');
            }
        }

        $host = getenv('DB_HOST');
        $name = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');

        $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";

        try {
            $this->conn = new PDO($dsn, $user, $pass, [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            error_log('[PHP CRUD] Database connection failed: ' . $e->getMessage());

            http_response_code(500);
            exit('A server error occurred. Please try again later.');
        }
    }
}
