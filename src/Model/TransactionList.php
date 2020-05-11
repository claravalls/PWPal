<?php

declare(strict_types=1); //Recommended. Canvia la manera com php fa comprovacions. Obligues a utilitzar els tipus definits, sense conversions.

namespace SallePW\SlimApp\Model;


final class TransactionList
{
    private int $id;
    private array $transactions;
    private array $sign;        //positive_trans or negative_trans

    public function __construct(
        $transactions = [1=>0, 2=>0, 3=> 0, 4=>0, 5=>0],
        $sign = [1=>"positive_trans", 2=>"positive_trans", 3=> "positive_trans", 4=>"positive_trans", 5=>"positive_trans"]

    ){
        $this->transactions = $transactions;
        $this->sign = $sign;
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

    public function getTransaction(int $i): int
    {
        return $this->transactions[$i];
    }

    public function setTransaction(int $i, int $quantity): void
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

}