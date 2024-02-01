<?php

namespace CLI\donation;

require_once '../../controller/DonationController.php';
require_once '../../database/DatabaseConnection.php';
require_once '../../models/Donation.php';

class AddDonationCommand
{
    const ERROR_PREFIX = "Error: ";

    public static function runCommand(array $args): void
    {
        if (count($args) !== 5) {
            die("Usage: php AddDonationCommand.php <charityId> <amount> <donorName> <dateTime>\n");
        }

        $charityId = (int) $args[1];
        $amount = (float) $args[2];
        $donorName = $args[3];
        $dateTime = $args[4];

        $donationController = new \controller\DonationController();
        $donation = new \models\Donation();
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

// CLI script check
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

try {
    $addDonationCommand = new AddDonationCommand();
    AddDonationCommand::runCommand($argv);
} catch (\Exception $e) {
    die('Something went wrong: ' . $e->getMessage());
}
