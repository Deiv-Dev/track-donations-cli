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
    public function runMigrations(\PDO $pdo): void
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
    $createDatabase = new CreateDatabase();
    $createDatabase->createDatabase();
    $pdo = $createDb->getConnection();
    $mig = new MigrationRunner();
    $migrationRunner = new MigrationRunner();
    $migrationRunner->runMigrations($pdo);
    $pdo = null;
} catch (\PDOException $e) {
    die('Something went wrong: ' . $e->getMessage());
}
