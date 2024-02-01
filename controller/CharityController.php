<?php

namespace controller;

require_once __DIR__ . '/../database/DatabaseConnection.php';
use database\DatabaseConnection;

require_once __DIR__ . '/../validation/CharityValidator.php';
use validation\CharityValidator;

require_once __DIR__ . '/../models/Charity.php';
use models\Charity;

class CharityController
{
    const ERROR_PREFIX = "Error: ";
    private $pdo;
    private $validator;

    public function __construct(DatabaseConnection $databaseConnection, CharityValidator $validator)
    {
        $this->pdo = $databaseConnection->getConnection();
        $this->validator = $validator;
    }

    public function create(Charity $charity): void
    {
        try {
            $this->validator->validateInput($charity->getName());
            $this->validator->validateEmailFormat($charity->getRepresentativeEmail());

            $name = $charity->getName();
            $email = $charity->getRepresentativeEmail();

            $stmt = $this->pdo->prepare("INSERT INTO charities (name, representative_email) VALUES (?, ?)");
            $stmt->execute([$name, $email]);
            echo "Charity added successfully: $name, $email\n";
        } catch (\PDOException $e) {
            echo self::ERROR_PREFIX . $e->getMessage() . "\n";
        } finally {
            $this->pdo = null;
        }
    }

    public function getAllCharities(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM charities");
            return $stmt->fetchAll(\PDO::FETCH_OBJ);

        } catch (\PDOException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
            return [];
        } finally {
            $this->pdo = null;
        }
    }

    public function update(Charity $charity): void
    {
        try {

            $charityId = $charity->getId();

            $stmtCheckId = $this->pdo->prepare("SELECT COUNT(*) FROM charities WHERE id = ?");
            $stmtCheckId->execute([$charityId]);

            $this->validator->validateInput($charity->getName());
            $this->validator->validateEmailFormat($charity->getRepresentativeEmail());
            $this->validator->validateCharity($stmtCheckId, $charityId);

            $name = $charity->getName();
            $email = $charity->getRepresentativeEmail();

            $stmtUpdate = $this->pdo->prepare("UPDATE charities SET name = ?, representative_email = ? WHERE id = ?");
            $stmtUpdate->execute([$name, $email, $charityId]);
            echo "Charity with ID $charityId updated successfully.";
        } catch (\PDOException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
        } finally {
            $this->pdo = null;
        }
    }

    public function delete(int $charityId): void
    {
        try {

            $stmtValidate = $this->pdo->prepare("SELECT id FROM charities WHERE id = ?");
            $stmtValidate->execute([$charityId]);


            $this->validator->validateCharity($stmtValidate, $charityId);

            $stmtDonations = $this->pdo->prepare("DELETE FROM donations WHERE charity_id = ?");
            $stmtDonations->execute([$charityId]);

            $stmtCharity = $this->pdo->prepare("DELETE FROM charities WHERE id = ?");
            $stmtCharity->execute([$charityId]);

            echo "Charity with ID $charityId deleted successfully.\n";
        } catch (\PDOException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
        } finally {
            $this->pdo = null;
        }
    }
}
