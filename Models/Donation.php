<?php

namespace models;

class Donation
{
    private $id;
    private $donorName;
    private $amount;
    private $charityId;
    private $dateTime;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getDonorName(): string
    {
        return $this->donorName;
    }

    public function setDonorName(string $donorName): void
    {
        $this->donorName = $donorName;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getCharityId(): int
    {
        return $this->charityId;
    }

    public function setCharityId(int $charityId): void
    {
        $this->charityId = $charityId;
    }

    public function getDateTime(): string
    {
        return $this->dateTime;
    }

    public function setDateTime(string $dateTime): void
    {
        $this->dateTime = $dateTime;
    }
}
