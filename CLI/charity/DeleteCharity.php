<?php

namespace CLI\charity;

require_once __DIR__ . '/../../controller/CharityController.php';
require_once __DIR__ . '/../../repository/CharityRepository.php';
require_once __DIR__ . '/../../validation/CharityValidator.php';
require_once __DIR__ . '/../../database/DatabaseConnection.php';

use repository\CharityRepository;
use controller\CharityController;
use database\DatabaseConnection;
use validation\CharityValidator;

class DeleteCharity
{
    const ERROR_PREFIX = "Error: ";
    private $charityController;

    public function __construct(CharityController $charityController)
    {
        $this->charityController = $charityController;
    }

    public function runCommand(array $args): void
    {
        if (count($args) < 2) {
            die("Usage: php DeleteCharity.php <charityId>\n");
        }

        $charityId = (int) $args[1];

        try {
            $this->charityController->delete($charityId);
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
    $databaseConnection = new DatabaseConnection();
    $validator = new CharityValidator();
    $repository = new CharityRepository($databaseConnection);
    $charityController = new CharityController($databaseConnection, $validator, $repository);
    $deleteCharity = new DeleteCharity($charityController);
    $deleteCharity->runCommand($argv);
} catch (\PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
