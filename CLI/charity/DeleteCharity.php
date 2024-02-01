<?php

namespace CLI\charity;

require_once '../../controller/CharityController.php';
require_once '../../database/DatabaseConnection.php';

class DeleteCharityCommand
{
    const ERROR_PREFIX = "Error: ";

    public static function runCommand(array $args): void
    {
        if (count($args) !== 2) {
            die("Usage: php DeleteCharityCommand.php <charityId>\n");
        }

        $charityId = (int) $args[1];

        $charityController = new \controller\CharityController();

        try {
            $charityController->delete($charityId);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), "Charity with ID $charityId not found.") !== false) {
                echo self::ERROR_PREFIX . $e->getMessage() . "\n";
            } else {
                die(self::ERROR_PREFIX . $e->getMessage() . "\n");
            }
        }
    }
}

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

try {
    $deleteCharityCommand = new DeleteCharityCommand();
    DeleteCharityCommand::runCommand($argv);
} catch (\PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
