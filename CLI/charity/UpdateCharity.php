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

class UpdateCharity
{
    const ERROR_PREFIX = "Error: ";

    public function runCommand(array $args): void
    {
        if (count($args) !== 4) {
            throw new \InvalidArgumentException(
                "Usage: php UpdateCharity.php <charityId> <name> <representativeEmail>\n"
            );
        }

        $charityId = (int) $args[1];
        $name = $args[2];
        $representativeEmail = $args[3];

        $databaseConnection = new DatabaseConnection();
        $validator = new CharityValidator();
        $repository = new CharityRepository($databaseConnection);
        $charityController = new CharityController($databaseConnection, $validator, $repository);

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
    $updateCharityCommand->runCommand($argv);
} catch (\Exception $e) {
    die('Something went wrong: ' . $e->getMessage());
}
