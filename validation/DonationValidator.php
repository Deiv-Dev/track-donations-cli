<?php

namespace validation;

use database\DatabaseConnection;

class DonationValidator
{
    private $pdo;

    public function __construct(DatabaseConnection $databaseConnection)
    {
        $this->pdo = $databaseConnection->getConnection();
    }

    public function validateDonationAmount(float $amount): void
    {
        if (!is_numeric($amount) || $amount < 0 || $amount > 1000000) {
            throw new \InvalidArgumentException("Amount can't be negative or exceed 1,000,000");
        }
    }

    public function validateDonorName(string $donorName): void
    {
        if (strlen($donorName) > 40) {
            throw new \InvalidArgumentException("Donor name must be 40 characters or less");
        }
    }

    public function validateCharityId(int $charityId): void
    {
        if ($charityId <= 0) {
            throw new \InvalidArgumentException("Invalid charity ID");
        }

        if (!$this->charityExists($charityId)) {
            throw new \InvalidArgumentException("Charity with ID $charityId not found.");
        }
    }

    private function charityExists(int $charityId): bool
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM charities WHERE id = ?");
            $stmt->execute([$charityId]);
            $charityCount = $stmt->fetchColumn();
            return $charityCount > 0;
        } catch (\PDOException $e) {
            echo "Error " . $e->getMessage() . "\n";
            return false;
        } finally {
            $this->pdo = null;
        }
    }

    public function validateDateTime(string $dateTime): void
    {
        $formattedDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);

        if (!$formattedDateTime || $formattedDateTime->format('Y-m-d H:i:s') !== $dateTime) {
            throw new \InvalidArgumentException("Invalid date format. Expected format: 'Y-m-d H:i:s'");
        }
    }
}
