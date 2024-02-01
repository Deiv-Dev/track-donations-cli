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
                throw new \InvalidArgumentException("Donor name must be 40 characters or less");
            }

            $amount = $donation->getAmount();
            if (!is_numeric($amount) || $amount < 0 || $amount > 1000000) {
                throw new \InvalidArgumentException("Invalid amount");
            }

            $charityId = $donation->getCharityId();
            $pdo = DatabaseConnection::getConnection();
            $dateTime = $donation->getDateTime();

            $stmt = $pdo->prepare(
                "INSERT INTO donations (donor_name, amount, charity_id, date_time) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$donorName, $amount, $charityId, $dateTime]);
        } catch (\PDOException | \InvalidArgumentException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
        } finally {
            $pdo = null;
        }
    }

    public function getAllDonations(): array
    {
        try {
            $pdo = DatabaseConnection::getConnection();
            $stmt = $pdo->query("SELECT * FROM donations");
            return $stmt->fetchAll(\PDO::FETCH_OBJ);

        } catch (\PDOException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
            return [];
        } finally {
            $pdo = null;
        }
    }

    public function read(int $donationId): array
    {
        try {
            $pdo = DatabaseConnection::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM donations WHERE id = ?");
            $stmt->execute([$donationId]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
            return [];
        } finally {
            $pdo = null;
        }
    }

    public function update(Donation $donation): void
    {
        try {
            $donorName = $donation->getDonorName();
            if (strlen($donorName) > 40) {
                throw new \InvalidArgumentException("Donor name must be 40 characters or less");
            }

            $amount = $donation->getAmount();
            if (!is_numeric($amount) || $amount < 0 || $amount > 1000000) {
                throw new \InvalidArgumentException("Invalid amount");
            }

            $pdo = DatabaseConnection::getConnection();
            $charityId = $donation->getCharityId();
            $dateTime = $donation->getDateTime();

            $stmt = $pdo->prepare(
                "UPDATE donations SET donor_name = ?, amount = ?, charity_id = ?, date_time = ? WHERE id = ?"
            );
            $stmt->execute([$donorName, $amount, $charityId, $dateTime, $donation->getId()]);
        } catch (\PDOException | \InvalidArgumentException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
        } finally {
            $pdo = null;
        }
    }

    public function delete(int $donationId): void
    {
        try {
            $pdo = DatabaseConnection::getConnection();
            $stmt = $pdo->prepare("DELETE FROM donations WHERE id = ?");
            $stmt->execute([$donationId]);
        } catch (\PDOException | \InvalidArgumentException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
        } finally {
            $pdo = null;
        }
    }
}
