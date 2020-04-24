<?php

declare(strict_types=1); //Recommended. Canvia la manera com php fa comprovacions. Obligues a utilitzar els tipus definits, sense conversions.

namespace SallePW\SlimApp\Model;

use DateTime;

final class User
{
    private int $id;
    private string $email;
    private string $password;
    private string $telefon;
    private DateTime $birthday;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private bool $active;

    public function __construct(
        string $email,
        string $password,
        string $telefon,
        DateTime $birthday,
        DateTime $createdAt,
        DateTime $updatedAt,
        bool $active
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->telefon = $telefon;
        $this->birthday = $birthday;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->active = $active;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function telefon(): string
    {
        return $this->telefon;
    }

    public function birthday(): DateTime
    {
        return $this->birthday;
    }

    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}