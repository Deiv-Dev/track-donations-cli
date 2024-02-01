<?php

namespace CLI\charity;

require_once __DIR__ . '/../../models/Charity.php';
require_once __DIR__ . '/../../controller/CharityController.php';
require_once __DIR__ . '/../../validation/CharityValidator.php';

use controller\CharityController;
use database\DatabaseConnection;
use validation\CharityValidator;

class UpdateCharity
{
    const ERROR_PREFIX = "Error: ";

    public static function runCommand(array $args): void
    {
        if (count($args) !== 4) {
            die("Usage: php UpdateCharityCommand.php <charityId> <name> <representativeEmail>\n");
        }

        $charityId = (int) $args[1];
        $name = $args[2];
        $representativeEmail = $args[3];

        $databaseConnection = new DatabaseConnection();
        $validator = new CharityValidator();

        $charityController = new CharityController($databaseConnection, $validator);

        try {
            $charity = new \models\Charity();
            $charity->setId($charityId);
            $charity->setName($name);
            $charity->setRepresentativeEmail($representativeEmail);

            $charityController->update($charity);
        } catch (\Exception $e) {
            echo self::ERROR_PREFIX . $e->getMessage() . "\n";
        }
    }
}

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

try {
    $updateCharityCommand = new UpdateCharity();
    UpdateCharity::runCommand($argv);
} catch (\Exception $e) {
    die('Something went wrong: ' . $e->getMessage());
}
