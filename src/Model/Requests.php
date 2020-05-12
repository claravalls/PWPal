<?php


namespace SallePW\SlimApp\Model;


final class Requests
{
    private int $id;
    private string $email_sender;
    private float $quantity;
    private bool $paid;

    public function __construct(
        string $email_sender,
        float $quantity,
        bool $paid
    ){
        $this->email_sender = $email_sender;
        $this->quantity = $quantity;
        $this->paid = $paid;
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

    public function email_sender(): string
    {
        return $this->email_sender;
    }

    public function quantity(): float
    {
        return $this->quantity;
    }

    public function paid(): bool
    {
        return $this->paid;
    }

}