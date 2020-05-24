<?php

declare(strict_types=1); //Recommended. Canvia la manera com php fa comprovacions. Obligues a utilitzar els tipus definits, sense conversions.

namespace SallePW\SlimApp\Model;


final class TransactionList
{
    private int $id;
    private array $transactions;
    private array $sign;            //positive_trans || negative_trans || neutral_trans (default)
    private array $other_user;      //email of the user who sent/receive transaction, or Income for load money

    public function __construct(
        $transactions = [1=>0.0, 2=>0.0, 3=> 0.0, 4=>0.0, 5=>0.0],
        $sign = [1=>"neutral_trans", 2=>"neutral_trans", 3=> "neutral_trans", 4=>"neutral_trans", 5=>"neutral_trans"],
        $other_user = [1=>"", 2=>"", 3=> "", 4=>"", 5=>""]

    ){
        $this->transactions = $transactions;
        $this->sign = $sign;
        $this->other_user = $other_user;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function getTransaction(int $i): float
    {
        return $this->transactions[$i];
    }

    public function setTransaction(int $i, float $quantity): void
    {
        $this->transactions[$i] = $quantity;
    }

    public function getSign(int $i): String
    {
        return $this->sign[$i];
    }

    public function setSign(int $i, String $trans_sign): void
    {
        $this->sign[$i] = $trans_sign;
    }

    public function getOtherUser(int $i): String
    {
        return $this->other_user[$i];
    }

    public function setOtherUser(int $i, String $email): void
    {
        $this->other_user[$i] = $email;
    }

    public function getAllTrans(): array
    {
        return $this->transactions;
    }

    public function getAllSigns(): array
    {
        return $this->sign;
    }

    public function getAllOtherUsers(): array
    {
        return $this->other_user;
    }
}