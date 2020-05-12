<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use DateTime;
use PDO;
use SallePW\SlimApp\Controller\DashBoardController;
use SallePW\SlimApp\Model\Bank;
use SallePW\SlimApp\Model\Requests;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\TransactionList;
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
                (float)$result[0]['wallet'],
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
            0.0,
            "",
            false
        );
        $user->setId(-1);
        return $user;

    }

    public function activateUser(String $token): void
    {
        $query = <<<'QUERY'
        UPDATE user set activated = 1, wallet = 20.0 WHERE token=:token
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('token', $token, PDO::PARAM_STR);
        $statement->execute();
    }

     public function findBankAccount (int $id):Bank
     {
         $query = <<<'QUERY'
        SELECT * FROM bank WHERE user_id=?
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.

         $statement = $this->database->connection()->prepare($query);

         $statement->bindParam(1, $id, PDO::PARAM_STR);
         $statement->execute();

         $result = $statement->fetchAll();

         if (sizeof($result)) {
             $bank = new Bank(
                 (int)$result[0]['user_id'],
                 $result[0]['owner_name'],
                 $result[0]['IBAN']
             );
             $bank->setId((int)$result[0]['id']);
             return $bank;
         }
        $bank = new Bank(
             -1,
             "",
             ""
         );
         $bank->setId(-1);
         return $bank;
     }

    public function addBankAccount(int $user_id, string $owner, string $iban): void
    {
        $query = <<<'QUERY'
        INSERT INTO bank(user_id, owner_name, iban)
        VALUES(:user_id, :owner, :iban)
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $statement->bindParam('owner', $owner, PDO::PARAM_STR);
        $statement->bindParam('iban', $iban, PDO::PARAM_STR);

        $statement->execute();
    }

    public function addMoneyToWallet(int $user_id, float $money){
        $query = <<<'QUERY'
        UPDATE user set wallet=:wallet WHERE id=:id
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('wallet', $money, PDO::PARAM_STR);
        $statement->bindParam('id', $user_id, PDO::PARAM_STR);
        $statement->execute();
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

    public function getMoney(String $email): float
    {
        $query = <<<'QUERY'
        SELECT * FROM user WHERE email=:email
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetchAll();

        if (sizeof($result)) {
            return (float)$result[0]['wallet'];
        }
    }

    public function getUserToSend(String $email): bool
    {
        $query = <<<'QUERY'
        SELECT * FROM user WHERE activated=1 and email=:email
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetchAll();

        if (sizeof($result)) {
            return true;
        }
        return false;
    }

    public function updateMoney(String $email, float $amount): void
    {
        $query = <<<'QUERY'
        UPDATE user set wallet=:wallet WHERE email=:email
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('wallet', $amount, PDO::PARAM_STR);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();
    }

    public function newTransaction(String $email_sender, String $email_receiver, float $quantity): void
    {
        $query = <<<'QUERY'
        INSERT INTO transaction (email_sender, email_receiver, quantity)
        VALUES(:email_sender, :email_receiver, :quantity)
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email_sender', $email_sender, PDO::PARAM_STR);
        $statement->bindParam('email_receiver', $email_receiver, PDO::PARAM_STR);
        $statement->bindParam('quantity', $quantity, PDO::PARAM_STR);
        $statement->execute();
    }

    public function latestTransactions(String $email): TransactionList
    {
        $query = <<<'QUERY'
        SELECT email_sender, email_receiver, quantity FROM transaction WHERE (email_sender=:email OR email_receiver=:email)
        ORDER BY id DESC LIMIT 5
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('quantity', $quantity, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetchAll();
        $list = new TransactionList();
        if (sizeof($result)) {
            for ($i = 0; $i < sizeof($result); $i++){
                $list->setTransaction($i+1, (float)$result[$i]['quantity']);
                if ($result[$i]['email_sender'] == $email){
                    $list->setSign($i+1, "negative_trans");
                    $list->setOtherUser($i+1, (String)$result[$i]['email_receiver']);
                }else{
                    $list->setSign($i+1, "positive_trans");
                    $list->setOtherUser($i+1, (String)$result[$i]['email_sender']);
                }
            }
        }

        return $list;
    }

    public function showAllTransactions(String $email): TransactionList
    {
        $query = <<<'QUERY'
        SELECT email_sender, email_receiver, quantity FROM transaction WHERE (email_sender=:email OR email_receiver=:email)
        ORDER BY id DESC
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('quantity', $quantity, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetchAll();
        $list = new TransactionList(sizeof($result));
        if (sizeof($result)) {
            for ($i = 0; $i < sizeof($result); $i++){
                $list->setTransaction($i+1, (int)$result[$i]['quantity']);
                if ($result[$i]['email_sender'] == $result[$i]['email_receiver']){      //load money
                    $list->setSign($i+1, "positive_trans");
                    $list->setOtherUser($i+1, (String)'Income');
                } else if ($result[$i]['email_sender'] == $email){                      //send money
                    $list->setSign($i+1, "negative_trans");
                    $list->setOtherUser($i+1, (String)$result[$i]['email_receiver']);
                }else{                                                                  //receive money
                    $list->setSign($i+1, "positive_trans");
                    $list->setOtherUser($i+1, (String)$result[$i]['email_sender']);
                }
            }
        }

        return $list;
    }

    public function newRequest (String $email_sender, String $email_receiver, float $quantity): void
    {
        $query = <<<'QUERY'
        INSERT INTO requests (email_sender, email_receiver, quantity)
        VALUES(:email_sender, :email_receiver, :quantity)
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email_sender', $email_sender, PDO::PARAM_STR);
        $statement->bindParam('email_receiver', $email_receiver, PDO::PARAM_STR);
        $statement->bindParam('quantity', $quantity, PDO::PARAM_STR);
        $statement->execute();
    }

    public function findPendingRequests(string $email){
        $query = <<<'QUERY'
        SELECT id, email_sender, quantity FROM requests WHERE (email_receiver=:email AND paid = 0)
QUERY; //Syntax nowdoc. Important que el tancament no estigui tabulat.
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetchAll();
        if (sizeof($result)) {
            $pending = array();
            foreach ($result as $item) {
                array_push($pending, new Requests((int)$item['id'], $item['email_sender'], (float)$item['quantity'], false));
            }
            return $pending;
        }
        return null;
    }
}