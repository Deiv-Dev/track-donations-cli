<?php

namespace database\migrations;

class CreateDatabase
{
    public static function createDatabase(): void
    {
        global $rootDsn, $rootUsername, $rootPassword, $dbName;

        require_once __DIR__ . '/../../config.php';

        try {
            $pdoWithoutDB = new \PDO($rootDsn, $rootUsername, $rootPassword);
            $pdoWithoutDB->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $pdoWithoutDB->exec("CREATE DATABASE IF NOT EXISTS $dbName");

            echo "Database '$dbName' created successfully.\n";
        } catch (\PDOException $e) {
            die('Database creation failed: ' . $e->getMessage());
        }
    }
}
