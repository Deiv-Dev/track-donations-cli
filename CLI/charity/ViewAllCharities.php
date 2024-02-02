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

class ViewAllCharities
{
    const ERROR_PREFIX = "Error:";
    private $charityController;

    public function __construct(CharityController $charityController)
    {
        $this->charityController = $charityController;
    }

    public function getAllCharities(): array
    {
        try {
            return $this->charityController->getAllCharities();
        } catch (\Exception $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
            return [];
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
    $viewAllCharities = new ViewAllCharities($charityController);
    $result = $viewAllCharities->getAllCharities();

    if (!empty($result)) {
        print_r($result);
    } else {
        echo "No charities found.\n";
    }
} catch (\Exception $e) {
    die('An error occurred: ' . $e->getMessage());
}
