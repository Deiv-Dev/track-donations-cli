<?php

namespace controller;


require_once __DIR__ . '/../models/Donation.php';
require_once __DIR__ . '/../validation/DonationValidator.php';
require_once __DIR__ . '/../repository/DonationRepository.php';
require_once __DIR__ . '/../database/DatabaseConnection.php';


use repository\DonationRepository;
use models\Donation;
use database\DatabaseConnection;
use validation\DonationValidator;

class DonationController
{
    const ERROR_PREFIX = "Error: ";

    private $pdo;
    private $repository;
    private $validator;

    public function __construct(
        DatabaseConnection $databaseConnection,
        DonationValidator $validator,
        DonationRepository $repository
    ) {
        $this->pdo = $databaseConnection->getConnection();
        $this->repository = $repository;
        $this->validator = $validator;
    }

    public function create(Donation $donation): void
    {
        try {
            $this->validator->validateDonationAmount($donation->getAmount());
            $this->validator->validateDonorName($donation->getDonorName());
            $this->validator->validateCharityId($donation->getCharityId());
            $this->validator->validateDateTime($donation->getDateTime());

            $this->repository->createDonation(
                $donation->getDonorName(),
                $donation->getAmount(),
                $donation->getCharityId(),
                $donation->getDateTime()
            );

            echo "Donation added successfully for Charity ID {$donation->getCharityId()}.\n";
        } catch (\PDOException $e) {
            echo self::ERROR_PREFIX . $e->getMessage() . "\n";
        } finally {
            $this->pdo = null;
        }
    }
}
