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

class AddCharity
{
    public function runCommand(array $args): void
    {
        if (count($args) < 2) {
            throw new \InvalidArgumentException("Usage: php AddCharityCommand.php <name> <representative_email>");
        }

        $name = $args[1];
        $representativeEmail = $args[2];

        $databaseConnection = new DatabaseConnection();
        $validator = new CharityValidator();
        $repository = new CharityRepository($databaseConnection);
        $charityController = new CharityController($databaseConnection, $validator, $repository);

        $charity = new \models\Charity();
        $charity->setName($name);
        $charity->setRepresentativeEmail($representativeEmail);
        $charityController->create($charity);
    }
}

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

try {
    $addCharityCommand = new AddCharity();
    $addCharityCommand->runCommand($argv);
} catch (\Exception $e) {
    die('Something went wrong ' . $e->getMessage());
}
