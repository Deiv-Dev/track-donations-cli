<?php

namespace CLI\charity;

require_once __DIR__ . '/../models/Charity.php';
require_once __DIR__ . '/../database/DatabaseConnection.php';
require_once __DIR__ . '/../controller/CharityController.php';
require_once __DIR__ . '/../validation/CharityValidator.php';
require_once __DIR__ . '/../repository/CharityRepository.php';

use repository\CharityRepository;
use models\Charity;
use database\DatabaseConnection;
use controller\CharityController;
use validation\CharityValidator;

class ImportCharityCSV
{
    const ERROR_PREFIX = "Error:";
    private $charityController;

    public function __construct(CharityController $charityController)
    {
        $this->charityController = $charityController;
    }

    public function runCommand(array $args): void
    {
        if (count($args) !== 2) {
            die("Usage: php ImportCharitiesCommand.php <csvFilePath>\n");
        }

        $csvFilePath = $args[1];

        if (!file_exists($csvFilePath)) {
            die("Error: CSV file not found.\n");
        }

        try {
            $charities = self::readCSV($csvFilePath);

            foreach ($charities as $charityData) {
                $charity = new Charity();
                $charity->setName($charityData['name']);
                $charity->setRepresentativeEmail($charityData['representative_email']);

                $this->charityController->create($charity);
            }
        } catch (\Exception $e) {
            die(self::ERROR_PREFIX . $e->getMessage() . "\n");
        }
    }

    private function readCSV(string $csvFilePath): array
    {
        $charities = [];
        $header = null;

        if (($handle = fopen($csvFilePath, 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                if ($header === null) {
                    $header = $data;
                } else {
                    $charities[] = array_combine($header, $data);
                }
            }
            fclose($handle);
        }

        return $charities;
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
    $importCharitiesCommand = new ImportCharityCSV($charityController);
    $importCharitiesCommand->runCommand($argv);
} catch (\Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}
