<?php

namespace CLI\charity;

require_once __DIR__ . '/../../models/Charity.php';
require_once __DIR__ . '/../../controller/CharityController.php';
require_once __DIR__ . '/../../validation/CharityValidator.php';

use controller\CharityController;
use database\DatabaseConnection;
use validation\CharityValidator;

class AddCharity
{
    public static function runCommand(array $args): void
    {
        if (count($args) < 3) {
            die("Usage: php AddCharityCommand.php <name> <representative_email>\n");
        }

        $name = $args[1];
        $representativeEmail = $args[2];

        $databaseConnection = new DatabaseConnection();
        $validator = new CharityValidator();

        $charityController = new CharityController($databaseConnection, $validator);

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
    AddCharity::runCommand($argv);
} catch (\Exception $e) {
    die('Something went wrong ' . $e->getMessage());
}
