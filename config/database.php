<?php
/**
 * Sahil Tech - Resilient Database Connection
 * Supports MySQL via PDO with graceful fallback to JSON store if MySQL is not yet configured.
 */

require_once __DIR__ . '/site.php';

class Database {
    private static ?PDO $pdo = null;
    private static bool $connectionAttempted = false;
    private static bool $isMySQL = false;
    private static string $jsonFile = __DIR__ . '/../data/posts.json';

    public static function getConnection(): ?PDO {
        if (self::$connectionAttempted) {
            return self::$pdo;
        }
        self::$connectionAttempted = true;

        // Ultra-fast 0.2s socket test to see if MySQL daemon is listening before attempting PDO
        $errno = 0;
        $errstr = '';
        $socket = @fsockopen(DB_HOST, (int)DB_PORT, $errno, $errstr, 0.2);
        if (!$socket) {
            // MySQL server is not active on this host/port. Fast instant fallback.
            self::$pdo = null;
            self::$isMySQL = false;
            return null;
        }
        fclose($socket);

        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_TIMEOUT            => 1,
            ];

            self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            self::$isMySQL = true;
            return self::$pdo;
        } catch (PDOException $e) {
            // MySQL port is open but database/auth not ready. Fallback to JSON.
            self::$pdo = null;
            self::$isMySQL = false;
            return null;
        }
    }

    public static function isMySQL(): bool {
        self::getConnection();
        return self::$isMySQL;
    }

    public static function getJsonFile(): string {
        return self::$jsonFile;
    }
}
