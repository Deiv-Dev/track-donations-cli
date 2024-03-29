<?php

namespace database;

class DatabaseConnection
{
    private static $pdo;

    public function getConnection(): \PDO
    {
        global $rootDsn, $rootUsername, $rootPassword, $dbName;

        require_once __DIR__ . '/../config.php';

        if (!isset(self::$pdo)) {
            try {
                $dsn = $rootDsn . ';dbname=' . $dbName;

                self::$pdo = new \PDO($dsn, $rootUsername, $rootPassword);
                self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage());
            }
        }
        return self::$pdo;
    }
}
