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
        INSERT INTO user(email, password, telefon, birthday, created_at, updated_at, photo, wallet, token, activated)
        VALUES(:email, :password, :telefon, :birthday, :created_at, :updated_at, :photo, :wallet, :token, :activated)
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
        $token = $user->token();
        $wallet = $user->wallet();

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('telefon', $telefon, PDO::PARAM_STR);
        $statement->bindParam('birthday', $birthday, PDO::PARAM_STR);
        $statement->bindParam('created_at', $createdAt, PDO::PARAM_STR);
        $statement->bindParam('updated_at', $updatedAt, PDO::PARAM_STR);
        $statement->bindParam('activated', $activated, PDO::PARAM_STR);
        $statement->bindParam('photo', $photo, PDO::PARAM_STR);
        $statement->bindParam('token', $token, PDO::PARAM_STR);
        $statement->bindParam('wallet', $wallet, PDO::PARAM_STR);

        $statement->execute();
    }

    public function search(String $param, String $value): User
    {
        if ($value == "email") {
            $query = <<<'QUERY'
        SELECT * FROM user WHERE email=?
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        } else if ($value == "token") {
            $query = <<<'QUERY'
        SELECT * FROM user WHERE token=?
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        } else {
            $query = <<<'QUERY'
        SELECT * FROM user
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        }
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam(1, $param, PDO::PARAM_STR);
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
                $result[0]['photo'],
                (int)$result[0]['wallet'],
                $result[0]['token'],
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
            0,
            "",
            false
        );
        $user->setId(-1);
        return $user;

    }

    public function activateUser(String $token): void
    {
        $query = <<<'QUERY'
        UPDATE user set activated = 1, wallet = 20 WHERE token=:token
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('token', $token, PDO::PARAM_STR);
        $statement->execute();
    }

     public function findBankAccount (int $id):int
     {
         $query = <<<'QUERY'
        SELECT id FROM bank WHERE user_id=?
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.

         $statement = $this->database->connection()->prepare($query);

         $statement->bindParam(1, $id, PDO::PARAM_STR);
         $statement->execute();

         $result = $statement->fetchAll();

         if (sizeof($result)) {
             return $result[0]['id'];
         }
         return -1;
     }

    public function changePassword(String $password, String $email): void
    {
        $query = <<<'QUERY'
        UPDATE user set password=:password WHERE email=:email
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->execute();
    }

    public function editProfile(String $phone, String $photo, String $email): void
    {
        $query = <<<'QUERY'
        UPDATE user set telefon=:telefon, photo=:photo WHERE email=:email
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('telefon', $phone, PDO::PARAM_STR);
        $statement->bindParam('photo', $photo, PDO::PARAM_STR);
        $statement->execute();
    }

    public function editProfileNotPhoto(String $phone, String $email): void
    {
        $query = <<<'QUERY'
        UPDATE user set telefon=:telefon WHERE email=:email
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('telefon', $phone, PDO::PARAM_STR);
        $statement->execute();
    }
}