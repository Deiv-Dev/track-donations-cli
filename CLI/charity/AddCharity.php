<?php

namespace CLI\charity;

require_once __DIR__ . '/../../models/Charity.php';
require_once __DIR__ . '/../../controller/CharityController.php';
require_once __DIR__ . '/../../repository/CharityRepository.php';
require_once __DIR__ . '/../../validation/CharityValidator.php';
require_once __DIR__ . '/../../database/DatabaseConnection.php';

use repository\CharityRepository;
use controller\CharityController;
use database\DatabaseConnection;
use validation\CharityValidator;
use models\Charity;

class AddCharity
{
    private $charityController;
    private $charity;

    public function __construct(CharityController $charityController, Charity $charity)
    {
        $this->charityController = $charityController;
        $this->charity = $charity;
    }

    public function runCommand(array $args): void
    {
        if (count($args) < 3) {
            throw new \InvalidArgumentException("Usage: php AddCharity.php <name> <representative_email>");
        }

        $name = $args[1];
        $representativeEmail = $args[2];

        $this->charity->setName($name);
        $this->charity->setRepresentativeEmail($representativeEmail);
        $this->charityController->create($this->charity);
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
    $charity = new Charity();
    $addCharityCommand = new AddCharity($charityController, $charity);
    $addCharityCommand->runCommand($argv);
} catch (\Exception $e) {
    die('Something went wrong ' . $e->getMessage());
}
