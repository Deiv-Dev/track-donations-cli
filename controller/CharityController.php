<?php

namespace controller;

require_once __DIR__ . '/../database/DatabaseConnection.php';
use database\DatabaseConnection;

require_once __DIR__ . '/../models/Charity.php';
use models\Charity;

class CharityController
{
    const ERROR_PREFIX = "Error: ";

    public function create(Charity $charity): void
    {
        if (empty($charity->getName()) || empty($charity->getRepresentativeEmail())) {
            throw new \InvalidArgumentException("Invalid input for update");
        }

        $name = $charity->getName();
        if (strlen($name) > 40) {
            throw new \InvalidArgumentException("Error: Charity name cannot be longer than 40 characters.");
        }

        $email = $charity->getRepresentativeEmail();
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Error: Invalid email format for representative.");
        }

        try {
            $pdo = DatabaseConnection::getConnection();
            $stmt = $pdo->prepare("INSERT INTO charities (name, representative_email) VALUES (?, ?)");
            $stmt->execute([$name, $email]);
        } catch (\PDOException | \InvalidArgumentException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
        } finally {
            $pdo = null;
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

    public function read(int $charityId): array
    {
        try {
            $pdo = DatabaseConnection::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM charities WHERE id = ?");
            $stmt->execute([$charityId]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);

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
            if (empty($charity->getName()) || empty($charity->getRepresentativeEmail()) || $charity->getId() <= 0) {
                throw new \InvalidArgumentException("Invalid input for update");
            }

            $name = $charity->getName();
            if (strlen($name) > 40) {
                throw new \InvalidArgumentException("Error: Charity name cannot be longer than 40 characters.");
            }

            $email = $charity->getRepresentativeEmail();
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException("Error: Invalid email format for representative.");
            }

            $pdo = DatabaseConnection::getConnection();
            $stmt = $pdo->prepare("UPDATE charities SET name = ?, representative_email = ? WHERE id = ?");
            $stmt->execute([$charity->getName(), $charity->getRepresentativeEmail(), $charity->getId()]);
        } catch (\PDOException | \InvalidArgumentException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
        } finally {
            $pdo = null;
        }
    }


    public function delete(int $charityId): void
    {
        try {
            $pdo = DatabaseConnection::getConnection();

            $stmtDonations = $pdo->prepare("DELETE FROM donations WHERE charity_id = ?");
            $stmtDonations->execute([$charityId]);

            $stmtCharity = $pdo->prepare("DELETE FROM charities WHERE id = ?");
            $stmtCharity->execute([$charityId]);

        } catch (\PDOException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
        } finally {
            $pdo = null;
        }
    }
}
