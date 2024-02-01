<?php

namespace CLI\donation;

require_once '../../controller/DonationController.php';
use \controller\DonationController;

require_once '../../validation/DonationValidator.php';
use validation\DonationValidator;

require_once '../../database/DatabaseConnection.php';
require_once '../../models/Donation.php';
use models\Donation;

use database\DatabaseConnection;

class AddDonationCommand
{
    const ERROR_PREFIX = "Error: ";

    public function runCommand(array $args): void
    {
        if (count($args) !== 5) {
            throw new \InvalidArgumentException(
                "Usage: php AddDonationCommand.php <charityId> <amount> <donorName> <dateTime>\n"
            );
        }

        $charityId = (int) $args[1];
        $amount = (float) $args[2];
        $donorName = $args[3];
        $dateTime = $args[4];

        $databaseConnection = new DatabaseConnection();
        $donationValidator = new DonationValidator($databaseConnection);
        $donationController = new DonationController($databaseConnection, $donationValidator);

        $donation = new Donation();
        $donation->setCharityId($charityId);
        $donation->setAmount($amount);
        $donation->setDonorName($donorName);
        $donation->setDateTime($dateTime);

        try {
            $donationController->create($donation);
        } catch (\Exception $e) {
            echo self::ERROR_PREFIX . $e->getMessage() . "\n";
        }
    }
}

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

try {
    $addDonationCommand = new AddDonationCommand();
    $addDonationCommand->runCommand($argv);
} catch (\Exception $e) {
    die('Something went wrong: ' . $e->getMessage());
}
