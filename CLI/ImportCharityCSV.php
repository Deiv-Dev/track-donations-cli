<?php

namespace CLI\charity;

require_once __DIR__ . '/../models/Charity.php';
require_once __DIR__ . '/../database/DatabaseConnection.php';
require_once __DIR__ . '/../controller/CharityController.php';
require_once __DIR__ . '/../validation/CharityValidator.php';

use models\Charity;
use database\DatabaseConnection;
use controller\CharityController;
use validation\CharityValidator;

class ImportCharityCSV
{
    const ERROR_PREFIX = "Error: ";

    public function runCommand(array $args): void
    {
        if (count($args) !== 2) {
            die("Usage: php ImportCharitiesCommand.php <csvFilePath>\n");
        }

        $csvFilePath = $args[1];

        $databaseConnection = new DatabaseConnection();
        $validator = new CharityValidator();
        $charityController = new CharityController($databaseConnection, $validator);

        try {
            $charities = self::readCSV($csvFilePath);

            foreach ($charities as $charityData) {
                $charity = new Charity();
                $charity->setName($charityData['name']);
                $charity->setRepresentativeEmail($charityData['representative_email']);

                $charityController->create($charity);
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
    $importCharitiesCommand = new ImportCharityCSV();
    $importCharitiesCommand->runCommand($argv);
} catch (\Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}
