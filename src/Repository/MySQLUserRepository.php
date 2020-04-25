<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use DateTime;
use PDO;
use SallePW\SlimApp\Controller\DashBoardController;
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
        INSERT INTO user(email, password, telefon, birthday, created_at, updated_at, photo, activated)
        VALUES(:email, :password, :telefon, :birthday, :created_at, :updated_at, :photo, :activated)
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $email = $user->email();
        $password = $user->password();
        $telefon = $user->telefon();
        $birthday = $user->birthday()->format(self::DATE_FORMAT);
        $createdAt = $user->createdAt()->format(self::DATE_FORMAT);
        $updatedAt = $user->updatedAt()->format(self::DATE_FORMAT);
        $activated = 0;
        $photo = DashBoardController::DEFAULT_PICTURE;

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('telefon', $telefon, PDO::PARAM_STR);
        $statement->bindParam('birthday', $birthday, PDO::PARAM_STR);
        $statement->bindParam('created_at', $createdAt, PDO::PARAM_STR);
        $statement->bindParam('updated_at', $updatedAt, PDO::PARAM_STR);
        $statement->bindParam('activated', $activated, PDO::PARAM_STR);
        $statement->bindParam('photo', $photo, PDO::PARAM_STR);

        $statement->execute();
    }

    public function search(String $email): User
    {
        $query = <<<'QUERY'
        SELECT * FROM user WHERE email=?
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam(1, $email, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetchAll();
        if (sizeof($result)) {
            $birthday = DateTime::createFromFormat('Y-m-d H:m:s', $result[0]['birthday']);
            $created = DateTime::createFromFormat('Y-m-d H:m:s', $result[0]['created_at']);
            $updated = DateTime::createFromFormat('Y-m-d H:m:s', $result[0]['updated_at']);

            $user = new User (
                $result[0]['email'],
                $result[0]['password'],
                $result[0]['telefon'],
                $birthday,
                $created,
                $updated,
                DashBoardController::DEFAULT_PICTURE,
                (bool)$result[0]['activated']
            );
            $user->setId((int)$result[0]['id']);
            return $user;
        }
        $user = new User (
            "",
            "",
            "",
            new DateTime(),
            new DateTime(),
            new DateTime(),
            "",
            false
        );
        $user->setId(-1);
        return $user;

    }

    public function activateUser(String $token) : void{
        $query = <<<'QUERY'
        UPDATE user set activated = 1 WHERE token=:token
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('token', $token, PDO::PARAM_STR);
        $statement->execute();

    }
}