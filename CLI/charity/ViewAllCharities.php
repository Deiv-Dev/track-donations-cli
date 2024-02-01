<?php

namespace CLI\charity;

require_once '../../controller/CharityController.php';

class ViewAllCharities
{
    const ERROR_PREFIX = "Error: ";

    public static function getAllCharities(\controller\CharityController $charityController): array
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
    $charityController = new \controller\CharityController();
    $result = ViewAllCharities::getAllCharities($charityController);

    if (!empty($result)) {
        print_r($result);
    } else {
        echo "No charities found.\n";
    }
} catch (\Exception $e) {
    die('An error occurred: ' . $e->getMessage());
}
