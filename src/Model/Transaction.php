<?php


namespace SallePW\SlimApp\Model;


final class Transaction
{
    private int $id;
    private String $email_sender;
    private String $email_receiver;
    private float $quantity;

    public function __construct(
        String $email_sender,
        String $email_receiver,
        float $quantity

    ){
        $this->email_sender = $email_sender;
        $this->email_receiver = $email_receiver;
        $this->quantity = $quantity;

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

    public function email_sender(): String
    {
        return $this->email_sender;
    }
    public function email_receiver(): String
    {
        return $this->email_receiver;
    }
    public function quantity(): float
    {
        return $this->quantity;
    }

}