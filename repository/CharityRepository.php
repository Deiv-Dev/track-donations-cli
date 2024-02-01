<?php

namespace repository;

require_once __DIR__ . '/../database/DatabaseConnection.php';
use database\DatabaseConnection;

class CharityRepository
{
    private $pdo;

    public function __construct(DatabaseConnection $databaseConnection, )
    {
        $this->pdo = $databaseConnection->getConnection();
    }

    public function insertCharity(string $name, string $representativeEmail): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO charities (name, representative_email) VALUES (?, ?)");
        $stmt->execute([$name, $representativeEmail]);
    }

    public function getAllCharities(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM charities");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function checkCharityId(int $charityId): int
    {
        $stmtCheckId = $this->pdo->prepare("SELECT COUNT(*) FROM charities WHERE id = ?");
        $stmtCheckId->execute([$charityId]);
        return (int) $stmtCheckId->fetchColumn();
    }

    public function updateCharity(int $charityId, string $name, string $representativeEmail): void
    {
        $stmtUpdate = $this->pdo->prepare("UPDATE charities SET name = ?, representative_email = ? WHERE id = ?");
        $stmtUpdate->execute([$name, $representativeEmail, $charityId]);
    }

    public function validateCharity(int $charityId): bool
    {
        $stmtValidate = $this->pdo->prepare("SELECT id FROM charities WHERE id = ?");
        $stmtValidate->execute([$charityId]);
        return $stmtValidate->fetchColumn() === 1;
    }

    public function deleteCharity(int $charityId): void
    {
        $stmtDonations = $this->pdo->prepare("DELETE FROM donations WHERE charity_id = ?");
        $stmtDonations->execute([$charityId]);

        $stmtCharity = $this->pdo->prepare("DELETE FROM charities WHERE id = ?");
        $stmtCharity->execute([$charityId]);
    }
}
