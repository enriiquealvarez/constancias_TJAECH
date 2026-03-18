<?php
namespace app\Core;

class Database
{
    private static $pdo;

    public static function connection()
    {
        if (!self::$pdo) {
            $cfg = $GLOBALS['dbConfig'];
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $cfg['host'], $cfg['database']);
            self::$pdo = new \PDO($dsn, $cfg['username'], $cfg['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);
            // Ensure session-level select limits (if set globally) do not truncate result sets.
            self::$pdo->exec('SET SESSION sql_select_limit = DEFAULT');
        }
        return self::$pdo;
    }
}
