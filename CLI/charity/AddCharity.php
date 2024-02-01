<?php

namespace CLI\charity;

require_once '../../models/Charity.php';
require_once '../../controller/CharityController.php';

class AddCharityCommand
{
    public static function runCommand(array $args): void
    {
        if (count($args) < 3) {
            die("Usage: php AddCharityCommand.php <name> <representative_email>\n");
        }

        $name = $args[1];
        $representativeEmail = $args[2];

        $charityController = new \controller\CharityController();

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
    $addCharityCommand = new AddCharityCommand();
    AddCharityCommand::runCommand($argv);
} catch (\Exception $e) {
    die('Something went wrong ' . $e->getMessage());
}
