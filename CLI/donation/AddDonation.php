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
    const ERROR_PREFIX = "Error:";
    private $donationController;
    private $donation;

    public function __construct(
        DonationController $donationController,
        Donation $donation,
    ) {
        $this->donationController = $donationController;
        $this->donation = $donation;
    }

    public function runCommand(array $args): void
    {
        if (count($args) !== 5) {
            throw new \InvalidArgumentException(
                "Usage: php AddDonation.php <charityId> <amount> <donorName> <dateTime>\n"
            );
        }

        $charityId = (int) $args[1];
        $amount = (float) $args[2];
        $donorName = $args[3];
        $dateTime = $args[4];

        $this->donation->setCharityId($charityId);
        $this->donation->setAmount($amount);
        $this->donation->setDonorName($donorName);
        $this->donation->setDateTime($dateTime);

        try {
            $this->donationController->create($this->donation);
        } catch (\Exception $e) {
            echo self::ERROR_PREFIX . $e->getMessage() . "\n";
        }
    }
}

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

try {
    $databaseConnection = new DatabaseConnection();
    $donationValidator = new DonationValidator($databaseConnection);
    $repository = new DonationRepository($databaseConnection);
    $donationController = new DonationController($databaseConnection, $donationValidator, $repository);
    $donation = new Donation();
    $addDonationCommand = new AddDonationCommand($donationController, $donation);
    $addDonationCommand->runCommand($argv);
} catch (\Exception $e) {
    die('Something went wrong: ' . $e->getMessage());
}
