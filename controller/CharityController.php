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
            $pdo = DatabaseConnection::getConnection();
            $stmt = $pdo->query("SELECT * FROM charities");
            return $stmt->fetchAll(\PDO::FETCH_OBJ);

        } catch (\PDOException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
            return [];
        } finally {
            $pdo = null;
        }
    }

    public function update(Charity $charity): void
    {
        try {
            $id = $charity->getId();
            $pdo = DatabaseConnection::getConnection();

            $stmtCheckId = $pdo->prepare("SELECT COUNT(*) FROM charities WHERE id = ?");
            $stmtCheckId->execute([$id]);

            if ($stmtCheckId->fetchColumn() == 0) {
                echo "Error: Charity with ID $id does not exist in the database.";
                return;
            }

            $name = $charity->getName();
            $email = $charity->getRepresentativeEmail();

            if (empty($name) || strlen($name) > 40 || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Error: Invalid input. Invalid email or name is longer then 40 characters.";
                return;
            }

            $stmtUpdate = $pdo->prepare("UPDATE charities SET name = ?, representative_email = ? WHERE id = ?");
            $stmtUpdate->execute([$name, $email, $id]);
            echo "Charity with ID $id updated successfully.";
        } catch (\PDOException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
        } finally {
            $pdo = null;
        }
    }

    public function delete(int $charityId): void
    {
        try {
            $pdo = DatabaseConnection::getConnection();

            $stmtValidate = $pdo->prepare("SELECT id FROM charities WHERE id = ?");
            $stmtValidate->execute([$charityId]);

            if ($stmtValidate->fetch() === false) {
                echo self::ERROR_PREFIX . "Charity with ID $charityId not found.\n";
                return;
            }

            $stmtDonations = $pdo->prepare("DELETE FROM donations WHERE charity_id = ?");
            $stmtDonations->execute([$charityId]);

            $stmtCharity = $pdo->prepare("DELETE FROM charities WHERE id = ?");
            $stmtCharity->execute([$charityId]);

            echo "Charity with ID $charityId deleted successfully.\n";
        } catch (\PDOException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
        } finally {
            $pdo = null;
        }
    }
}
