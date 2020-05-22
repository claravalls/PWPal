<?php

namespace SallePW\SlimApp\Controller;

use Iban\Validation\Validator;
use Iban\Validation\Iban;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SallePW\SlimApp\Model\TransactionList;

final class BankController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showBankAccount (Request $request, Response $response): Response
    {
        if (!isset($_SESSION['user'])){
            echo "<script>
            alert('Log in to access to your bank account');
            window.location.href='/sign-in';
            </script>";
        }
        $user = $_SESSION['user'];
        $user = $this->container->get('user_repository')->search($user->email(), "email");
        $bank = $this->container->get('user_repository')->findBankAccount($user->id());
        if ($bank->id() > 0){
            return $this->container->get('view')->render(
                $response,
                'bankAccount.twig',
                [
                    'iban' => substr_replace ($bank->iban(), '****************', 6),
                    'bank' => $bank
                ]
            );
        }
        return $this->container->get('view')->render(
            $response,
            'bankAccount.twig',
            [
            ]
        );
    }

    public function addBankAccount (Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $owner = $data['owner'] ?? '';
        $iban = $data['iban'] ?? '';

        $errors = $this->validateIban($iban);

        if(empty($errors)){
            $user = $_SESSION['user'];
            $user = $this->container->get('user_repository')->search($user->email(), "email");
            $this->container->get('user_repository')->addBankAccount($user->id(), $owner, $iban);
            return $this->container->get('view')->render(
                $response,
                'bankAccount.twig',
                [
                    'bank' => 1,
                    'owner' => $owner,
                    'iban' => substr_replace ($iban, '****************', 6),
                ]
            );
        }
        else{
            return $this->container->get('view')->render(
                $response,
                'bankAccount.twig',
                [
                    'errors' => $errors,
                    'owner' => $owner,
                    'iban' => $iban,
                ]
            );
        }
    }

    public function addMoneyToWallet(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $amount = (float)$data['amount'] ?? '';

        $user = $_SESSION['user'];
        $user = $this->container->get('user_repository')->search($user->email(), "email");
        $bank = $this->container->get('user_repository')->findBankAccount($user->id());
        if($amount > 0.0){
            $this->container->get('user_repository')->addMoneyToWallet($user->id(), $amount + $user->wallet());
            $message = 'Money added successfully to wallet. Redirecting...';
            $_SESSION['user'] = $this->container->get('user_repository')->search($user->email(), "email");
            $url = "/account/summary";
        }
        else{
            $message = 'This amount is not valid';
        }
        return $this->container->get('view')->render(
            $response,
            'bankAccount.twig',
            [
                'iban' => substr_replace ($bank->iban(), '****************', 6),
                'bank' => $bank,
                'load_message' => $message,
                'url' => $url
            ]
        );
    }
    public function showSendMoneyPage(Request $request, Response $response): Response
    {
        if (!isset($_SESSION['user'])){
            echo "<script>
            alert('Log in to access to your bank account');
            window.location.href='/sign-in';
            </script>";
        }
        return $this->container->get('view')->render(
            $response,
            'sendmoney.twig',
            [
            ]
        );
    }
    public function sendMoney(Request $request, Response $response): Response{

        $data = $request->getParsedBody();

        if (!isset($_SESSION['user'])){
            echo "<script>
            alert('Log in to access to your bank account');
            window.location.href='/sign-in';
            </script>";
        }
        $user = $_SESSION['user'];
        $user = $this->container->get('user_repository')->search($user->email(), "email");
        if(empty($data['amount']))
        {
            $data['amount'] = 0;
        }
        $errors = $this->isValid($data['email'] ?? "", $data['amount']);
        if(empty($errors)) {
            $exists = $this->container->get('user_repository')->getUserToSend($data['email']);

            if((float)$data['amount'] > $user->wallet()) {
                $errors[] = "Insuficient money for the transaction";
            }else {
                if ($exists == true) {
                    $this->container->get('user_repository')->updateMoney($user->email(), ($user->wallet() - $data['amount']));
                    $this->container->get('user_repository')->newTransaction($user->email(), $data['email'], $data['amount']);
                    $newmoney = $this->container->get('user_repository')->getMoney($data['email']);
                    $this->container->get('user_repository')->updateMoney($data['email'], ($newmoney + $data['amount']));
                    $user->setWallet($user->wallet() - (float)$data['amount']);
                    $_SESSION['user'] = $user;
                    $errors[] = "Transaction is finished successfully";
                    $errors[] = "Redirecting..";
                    $url = "/account/summary";
                } else {
                    $errors[] = "This user email doesn't exist or doesn't have an activated account";
                }
            }
        }

        return $this->container->get('view')->render(
            $response,
            'sendmoney.twig',
            [
                'errors' => $errors,
                'email' => $data['email'],
                'amount' => $data['amount'],
                'url' => $url ?? ''
            ]
        );
    }

    public function isValid(?string $email, ?float $number)
    {
        $errors = [];

        if ($this->validEmail($email)==false) {
            $errors[] = sprintf('The email %s is not valid', $email);
        }

        if (is_numeric($number)) {
            if($number <= 0.0)
                $errors[] = sprintf('The amount is not valid, it must be positive and valid decimal number');
        }

        return $errors;
    }

    public function requestMoney(Request $request, Response $response): Response{
        $data = $request->getParsedBody();

        if (!isset($_SESSION['user'])){
            echo "<script>
            alert('Log in to access to your bank account');
            window.location.href='/sign-in';
            </script>";
        }
        $user = $_SESSION['user'];
        $user = $this->container->get('user_repository')->search($user->email(), "email");
        $errors = $this->isValid($data['email'] ?? "", $data['amount']);
        if(empty($errors)) {
            $exists = $this->container->get('user_repository')->getUserToSend($data['email']);

            if ($exists == true) {
                $this->container->get('user_repository')->newRequest($user->email(), $data['email'], $data['amount']);
                $errors[] = "Request is finished successfully";
                $errors[] = "Redirecting..";
                $url = "/account/summary";
            } else {
                $errors[] = "The user email don't exist or don't have the account activated";
            }
        }

        return $this->container->get('view')->render(
            $response,
            'requestMoney.twig',
            [
                'errors' => $errors,
                'email' => $data['email'],
                'amount' => $data['amount'],
                'url' => $url ?? ''
            ]
        );
    }

    public function validateIban(string $iban)
    {
        $errors = [];
        $validator = new Validator();

        if (!$validator->validate($iban)) {
            foreach ($validator->getViolations() as $violation) {
                $errors [] = $violation;
            }
        }
        return $errors;
    }

    public function validEmail(?string $email){

        if(filter_var($email, FILTER_VALIDATE_EMAIL) === false){
            return FALSE;
        }

        $domain = explode("@", $email, 2);

        if($domain[1] == "salle.url.edu")
        {
            return true;
        }
        return false;
    }

    public function showAllTransactions (Request $request, Response $response): Response
    {
        $user = $_SESSION['user'];
        $user = $this->container->get('user_repository')->search($user->email(), "email");
        $bank = $this->container->get('user_repository')->findBankAccount($user->id());

        $list = $this->container->get('user_repository')->showAllTransactions($user->email());

        $trans = $list->getAllTrans();
        $signs = $list->getAllSigns();
        $from = $list->getAllOtherUsers();
        if ($bank->id() > 0) {
            return $this->container->get('view')->render(
                $response,
                'transactions.twig',
                [
                    'iban' => substr_replace ($bank->iban(), '****************', 6),
                    'bank' => $bank,
                    'wallet' => $user->wallet(),
                    'list' => $trans,
                    'signs' => $signs,
                    'tr_from' => $from
                ]
            );

        }
        return $this->container->get('view')->render(
            $response,
            'transactions.twig',
            [
                'iban' => $bank->iban(),
                'bank' => $bank,
                'wallet' => $user->wallet(),
                'list' => $trans,
                'signs' => $signs,
                'tr_from' => $from
            ]
        );

    }


}
