<?php

namespace database\migrations;

require_once './CreateDatabase.php';
require_once './CreateCharityTable.php';
require_once './CreateDonationTable.php';
require_once '../DatabaseConnection.php';

class MigrationRunner
{
    public static function runMigrations(\PDO $pdo): void
    {
        $migrationFiles = [
            'CreateCharityTable',
            'CreateDonationTable',
        ];

        foreach ($migrationFiles as $migrationFile) {
            $className = 'database\\migrations\\' . $migrationFile;

            if (class_exists($className)) {
                $migration = new $className();
                $migration->up($pdo);
                echo "Migration created: $migrationFile\n";
            } else {
                echo "Migration class not found: $migrationFile\n";
            }
        }
    }
}

try {
    $createDb = new DatabaseCreator();
    DatabaseCreator::createDatabase();
    $pdo = \Database\DatabaseConnection::getConnection();
    $mig = new MigrationRunner();
    MigrationRunner::runMigrations($pdo);
    $pdo = null;
} catch (\PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
