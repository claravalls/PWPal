<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use PDO;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;

final class MySQLUserRepository implements UserRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function save(User $user): void
    {
        $query = <<<'QUERY'
        INSERT INTO user(email, password, telefon, birthday, created_at, updated_at, activated)
        VALUES(:email, :password, :telefon, :birthday, :created_at, :updated_at, :activated)
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $email = $user->email();
        $password = $user->password();
        $telefon = $user->telefon();
        $birthday = $user->birthday()->format(self::DATE_FORMAT);
        $createdAt = $user->createdAt()->format(self::DATE_FORMAT);
        $updatedAt = $user->updatedAt()->format(self::DATE_FORMAT);
        $activated = 0;

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('telefon', $telefon, PDO::PARAM_STR);
        $statement->bindParam('birthday', $birthday, PDO::PARAM_STR);
        $statement->bindParam('created_at', $createdAt, PDO::PARAM_STR);
        $statement->bindParam('updated_at', $updatedAt, PDO::PARAM_STR);
        $statement->bindParam('activated', $activated, PDO::PARAM_STR);

        $statement->execute();
    }

    public function search(String $email): User
    {
        $query = <<<'QUERY'
        SELECT * FROM user WHERE email=:email
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetchAll();
        if (sizeof($result)) {
            return new User (
                $result[0]['id'],
                $result[0]['email'],
                $result[0]['password'],
                $result[0]['birthday'],
                $result[0]['created_at'],
                $result[0]['updated_at'],
                $result[0]['activated']
            );
        }
        return NULL;
    }
}