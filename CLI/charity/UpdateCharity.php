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


class UpdateCharity
{
    const ERROR_PREFIX = "Error:";
    private $charityController;
    private $charity;

    public function __construct(CharityController $charityController, Charity $charity)
    {
        $this->charityController = $charityController;
        $this->charity = $charity;
    }

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

        try {
            $this->charity->setId($charityId);
            $this->charity->setName($name);
            $this->charity->setRepresentativeEmail($representativeEmail);

            $this->charityController->update($this->charity);
        } catch (\Exception $e) {
            echo self::ERROR_PREFIX . $e->getMessage() . "\n";
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
    $charity = new Charity();
    $updateCharityCommand = new UpdateCharity($charityController, $charity);
    $updateCharityCommand->runCommand($argv);
} catch (\Exception $e) {
    die('Something went wrong: ' . $e->getMessage());
}