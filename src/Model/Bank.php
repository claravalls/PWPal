<?php


namespace SallePW\SlimApp\Model;


final class Bank
{
    private int $id;
    private int $user_id;
    private string $owner;
    private string $iban;

    public function __construct(
        int $user_id,
        string $owner,
        string $iban
    ){
        $this->user_id = $user_id;
        $this->owner = $owner;
        $this->iban = $iban;
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
    public function user_id(): int
    {
        return $this->user_id;
    }
    public function owner(): string
    {
        return $this->owner;
    }
    public function iban(): string
    {
        return $this->iban;
    }
}