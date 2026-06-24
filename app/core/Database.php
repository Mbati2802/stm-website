<?php
class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(array $config = null): PDO
    {
        if (self::$instance === null) {
            $cfg = $config ?? ($GLOBALS['config']['db'] ?? null);
            if (!is_array($cfg) || empty($cfg['database'] ?? '') ) {
                throw new RuntimeException('Database configuration not provided to Database::getInstance');
            }

            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $cfg['host'], $cfg['port'], $cfg['database'], $cfg['charset']);
            self::$instance = new PDO($dsn, $cfg['username'], $cfg['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            // Set connection collation to match table defaults
            self::$instance->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        }

        return self::$instance;
    }
}
