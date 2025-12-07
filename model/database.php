<?php
class Database {
    // Socket preferred for XAMPP; falls back to TCP if needed.
    private static $socketDsn = 'mysql:unix_socket=/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock;dbname=crm54;charset=utf8mb4';
    private static $tcpDsn    = 'mysql:host=127.0.0.1;dbname=crm54;charset=utf8mb4';
    private static $username = 'app_host';
    private static $password = 'tyhvy4-mItnus-tejqyr';
    private static $db;

    private function __construct() {}

    public static function getDB () {
        if (!isset(self::$db)) {
            try {
                self::$db = new PDO(
                    file_exists('/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock') ? self::$socketDsn : self::$tcpDsn,
                    self::$username,
                    self::$password,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                // Fallback to TCP if socket fails (common in CLI).
                if (!isset(self::$db)) {
                    try {
                        self::$db = new PDO(
                            self::$tcpDsn,
                            self::$username,
                            self::$password,
                            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                        );
                    } catch (PDOException $e2) {
                        echo 'Database connection failed: ' . $e2->getMessage() . PHP_EOL;
                        exit(1);
                    }
                }
            }
        }
        return self::$db;
    }
}
?>
