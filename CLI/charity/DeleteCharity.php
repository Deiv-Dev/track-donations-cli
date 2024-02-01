<?php

namespace CLI\charity;

require_once __DIR__ . '/../../models/Charity.php';
require_once __DIR__ . '/../../controller/CharityController.php';
require_once __DIR__ . '/../../validation/CharityValidator.php';
require_once __DIR__ . '/../../repository/CharityRepository.php';

use repository\CharityRepository;
use controller\CharityController;
use database\DatabaseConnection;
use validation\CharityValidator;

class DeleteCharity
{
    const ERROR_PREFIX = "Error: ";

    public function runCommand(array $args): void
    {
        if (count($args) < 2) {
            die("Usage: php DeleteCharityCommand.php <charityId>\n");
        }

        $charityId = (int) $args[1];

        $databaseConnection = new DatabaseConnection();
        $validator = new CharityValidator();
        $repository = new CharityRepository($databaseConnection);
        $charityController = new CharityController($databaseConnection, $validator, $repository);

        try {
            $charityController->delete($charityId);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), "Charity with ID $charityId not found.") !== false) {
                echo self::ERROR_PREFIX . $e->getMessage() . "\n";
            } else {
                die(self::ERROR_PREFIX . $e->getMessage() . "\n");
            }
        }
    }
}

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

try {
    $deleteCharity = new DeleteCharity();
    $deleteCharity->runCommand($argv);
} catch (\PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
