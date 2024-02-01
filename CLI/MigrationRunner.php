<?php

namespace CLI;

require_once __DIR__ . '/../database/migrations/CreateDatabase.php';
require_once __DIR__ . '/../database/migrations/CreateCharityTable.php';
require_once __DIR__ . '/../database/migrations/CreateDonationTable.php';
require_once __DIR__ . '/../database/DatabaseConnection.php';

use database\migrations\CreateDatabase;
use database\DatabaseConnection;

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
    $createDb = new DatabaseConnection();
    CreateDatabase::createDatabase();
    $pdo = DatabaseConnection::getConnection();
    $mig = new MigrationRunner();
    MigrationRunner::runMigrations($pdo);
    $pdo = null;
} catch (\PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
