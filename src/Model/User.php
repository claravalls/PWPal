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
    private string $photo;
    private int $wallet;
    private string $token;
    private bool $active;

    public function __construct(
        string $email,
        string $password,
        string $telefon,
        DateTime $birthday,
        DateTime $createdAt,
        DateTime $updatedAt,
        string $photo,
        int $wallet,
        string $token,
        bool $active
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->telefon = $telefon;
        $this->birthday = $birthday;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->photo = $photo;
        $this->wallet = $wallet;
        $this->token = $token;
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
    public function photo(): string
    {
        return $this->photo;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function wallet(): int
    {
        return $this->wallet;
    }

    public function setWallet($amount): void
    {
        $this->wallet = $amount;
    }
}

/*
 * CREATE TABLE `user` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `email` VARCHAR(255) NOT NULL DEFAULT '',
        `password` VARCHAR(255) NOT NULL DEFAULT '',
        `telefon` VARCHAR(255), `birthday` DATETIME NOT NULL,
        `created_at` DATETIME NOT NULL,
        `updated_at` DATETIME NOT NULL,
        `photo` VARCHAR(255),
        `token` VARCHAR(255),
        `wallet` INT(11) UNSIGNED NOT NULL,
        `activated` BOOLEAN NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `bank` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) UNSIGNED NOT NULL,
        `owner_name` VARCHAR(255) NOT NULL DEFAULT '',
        `IBAN` VARCHAR(255) NOT NULL DEFAULT '',
        PRIMARY KEY (`id`),
        CONSTRAINT `bank_ibfk_1` FOREIGN KEY (`user_id`)
        REFERENCES `user` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */