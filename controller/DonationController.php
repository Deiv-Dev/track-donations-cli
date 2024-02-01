<?php

namespace controller;

require_once __DIR__ . '/../database/DatabaseConnection.php';
use database\DatabaseConnection;

require_once __DIR__ . '/../models/Donation.php';
use models\Donation;

class DonationController
{
    const ERROR_PREFIX = "Error: ";
    public function create(Donation $donation): void
    {
        try {
            $donorName = $donation->getDonorName();
            if (strlen($donorName) > 40) {
                echo self::ERROR_PREFIX . "Donor name must be 40 characters or less";
                return;
            }

            $amount = $donation->getAmount();
            if (!is_numeric($amount) || $amount < 0 || $amount > 1000000) {
                echo self::ERROR_PREFIX . "Amount cant be negative or over 1,000,000";
                return;
            }

            $charityId = $donation->getCharityId();
            $pdo = DatabaseConnection::getConnection();

            // Check if charity ID exists in the database
            $stmtCharity = $pdo->prepare("SELECT COUNT(*) FROM charities WHERE id = ?");
            $stmtCharity->execute([$charityId]);
            $charityCount = $stmtCharity->fetchColumn();

            if ($charityCount === 0) {
                echo self::ERROR_PREFIX . "Charity with ID $charityId does not exist";
                return;
            }

            $dateTime = $donation->getDateTime();

            $formattedDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);

            if (!$formattedDateTime || $formattedDateTime->format('Y-m-d H:i:s') !== $dateTime) {
                throw new \InvalidArgumentException("Invalid date format. Expected format: 'Y-m-d H:i:s'");
            }

            $dateTime = $donation->getDateTime();

            $stmt = $pdo->prepare(
                "INSERT INTO donations (donor_name, amount, charity_id, date_time) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$donorName, $amount, $charityId, $dateTime]);
            echo "Donation added successfully for Charity ID $charityId.\n";
        } catch (\PDOException | \InvalidArgumentException $e) {
            echo self::ERROR_PREFIX . $e->getMessage() . "\n";
        } finally {
            $pdo = null;
        }
    }
}
