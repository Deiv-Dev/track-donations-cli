<?php

namespace controller;

require_once __DIR__ . '/../database/DatabaseConnection.php';
use database\DatabaseConnection;

require_once __DIR__ . '/../validation/CharityValidator.php';
use validation\CharityValidator;

require_once __DIR__ . '/../models/Charity.php';
use models\Charity;

require_once __DIR__ . '/../repository/CharityRepository.php';
use repository\CharityRepository;

class CharityController
{
    const ERROR_PREFIX = "Error: ";
    private $pdo;
    private $repository;
    private $validator;

    public function __construct(
        DatabaseConnection $databaseConnection,
        CharityValidator $validator,
        CharityRepository $repository
    ) {
        $this->pdo = $databaseConnection->getConnection();
        $this->repository = $repository;
        $this->validator = $validator;
    }

    public function create(Charity $charity): void
    {
        try {
            $this->validator->validateInput($charity->getName());
            $this->validator->validateEmailFormat($charity->getRepresentativeEmail());

            $name = $charity->getName();
            $email = $charity->getRepresentativeEmail();

            $this->repository->insertCharity($name, $email);
            echo "Charity added successfully: $name, $email\n";
        } catch (\PDOException $e) {
            echo self::ERROR_PREFIX . $e->getMessage() . "\n";
        }
    }

    public function getAllCharities(): array
    {
        try {
            return $this->repository->getAllCharities();
        } catch (\PDOException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
            return [];
        }
    }

    public function update(Charity $charity): void
    {
        try {
            $charityId = $charity->getId();

            $this->validator->validateInput($charity->getName());
            $this->validator->validateEmailFormat($charity->getRepresentativeEmail());
            $rowCount = $this->repository->checkCharityId($charityId);
            $this->validator->validateCharity($rowCount, $charityId);

            $name = $charity->getName();
            $email = $charity->getRepresentativeEmail();

            $this->repository->updateCharity($charityId, $name, $email);
            echo "Charity with ID $charityId updated successfully.";
        } catch (\PDOException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
        }
    }

    public function delete(int $charityId): void
    {
        try {
            $rowCount = $this->repository->checkCharityId($charityId);
            $this->validator->validateCharity($rowCount, $charityId);

            $this->repository->deleteCharity($charityId);

            echo "Charity with ID $charityId deleted successfully.\n";
        } catch (\PDOException $e) {
            echo self::ERROR_PREFIX . $e->getMessage();
        }
    }
}
