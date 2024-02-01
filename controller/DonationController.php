<?php

namespace controller;

require_once __DIR__ . '/../validation/DonationValidator.php';
use validation\DonationValidator;
use database\DatabaseConnection;
use models\Donation;

class DonationController
{
    const ERROR_PREFIX = "Error: ";

    private $pdo;
    private $validator;

    public function __construct(DatabaseConnection $databaseConnection, DonationValidator $validator)
    {
        $this->pdo = $databaseConnection->getConnection();
        $this->validator = $validator;
    }

    public function create(Donation $donation): void
    {
        try {
            $this->validator->validateDonationAmount($donation->getAmount());
            $this->validator->validateDonorName($donation->getDonorName());
            $this->validator->validateCharityId($donation->getCharityId());
            $this->validator->validateDateTime($donation->getDateTime());

            $stmt = $this->pdo->prepare(
                "INSERT INTO donations (donor_name, amount, charity_id, date_time) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute(
                [
                    $donation->getDonorName(),
                    $donation->getAmount(),
                    $donation->getCharityId(),
                    $donation->getDateTime()
                ]
            );

            echo "Donation added successfully for Charity ID {$donation->getCharityId()}.\n";
        } catch (\PDOException $e) {
            echo self::ERROR_PREFIX . $e->getMessage() . "\n";
        } finally {
            $this->pdo = null;
        }
    }
}
