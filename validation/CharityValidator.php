<?php

namespace validation;

class CharityValidator
{
    public function validateInput(string $name): void
    {
        if (empty($name) || strlen($name) > 40) {
            throw new \InvalidArgumentException("Invalid input max length 40 characters.\n");
        }

    }

    public function validateEmailFormat(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email.\n");
        }
    }

    public function validateCharity(int $rowCount, int $charityId): void
    {
        if ($rowCount === 0) {
            throw new \InvalidArgumentException("Charity with ID $charityId not found.");
        }
    }

}
