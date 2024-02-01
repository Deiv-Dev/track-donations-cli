<?php

namespace repository;

require_once __DIR__ . '/../database/DatabaseConnection.php';

use database\DatabaseConnection;

class DonationRepository
{
    private $pdo;

    public function __construct(DatabaseConnection $databaseConnection)
    {
        $this->pdo = $databaseConnection->getConnection();
    }

    public function createDonation(string $donorName, float $amount, int $charityId, string $dateTime): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO donations (donor_name, amount, charity_id, date_time) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$donorName, $amount, $charityId, $dateTime]);
    }
}
