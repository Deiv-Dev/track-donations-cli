<?php

namespace CLI\charity;


require_once __DIR__ . '/../../models/Charity.php';
require_once __DIR__ . '/../../controller/CharityController.php';
require_once __DIR__ . '/../../validation/CharityValidator.php';

use controller\CharityController;
use database\DatabaseConnection;
use validation\CharityValidator;

class ViewAllCharities
{
    const ERROR_PREFIX = "Error: ";

    public static function getAllCharities(CharityController $charityController): array
    {
        try {
            return $charityController->getAllCharities();
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

    $charityController = new CharityController($databaseConnection, $validator);
    $result = ViewAllCharities::getAllCharities($charityController);

    if (!empty($result)) {
        print_r($result);
    } else {
        echo "No charities found.\n";
    }
} catch (\Exception $e) {
    die('An error occurred: ' . $e->getMessage());
}
