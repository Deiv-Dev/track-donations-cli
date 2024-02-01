<?php

namespace CLI\donation;

require_once __DIR__ . '/../../controller/DonationController.php';
require_once __DIR__ . '/../../validation/DonationValidator.php';
require_once __DIR__ . '/../../database/DatabaseConnection.php';
require_once __DIR__ . '/../../models/Donation.php';
require_once __DIR__ . '/../../repository/DonationRepository.php';

use repository\DonationRepository;
use validation\DonationValidator;
use controller\DonationController;
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

        $repository = new DonationRepository($databaseConnection);
        $donationController = new DonationController($databaseConnection, $donationValidator, $repository);

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
