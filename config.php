<?php 
    if (file_exists(__DIR__ . '/.env')) {
        $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;
            if (strpos($line, '=') !== false) {
                list($k, $v) = explode('=', $line, 2);
                $k = trim($k);
                $v = trim($v, " \t\n\r\0\x0B\"'");
                if (!array_key_exists($k, $_SERVER) && !array_key_exists($k, $_ENV)) {
                    putenv("$k=$v");
                    $_ENV[$k] = $v;
                    $_SERVER[$k] = $v;
                }
            }
        }
    }

    class Config{
        protected $conn = null;
        public function __construct(){
            $host = getenv('DB_HOST') ?: "localhost";
            $user = getenv('DB_USER') ?: "root";
            $pass = getenv('DB_PASS') ?: "";
            $name = getenv('DB_NAME') ?: "php_crud_pdo_oop";
            $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";
            try{
                $this->conn = new PDO($dsn, $user, $pass);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }catch(PDOException $e){
                 error_log("Database connection failed: " . $e->getMessage());
                 http_response_code(500);
                 die("A database error occurred. Please try again later.");
              }
            }
        }
?>
