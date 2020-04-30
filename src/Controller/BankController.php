<?php


namespace SallePW\SlimApp\Controller;

use Iban\Validation\Validator;
use Iban\Validation\Iban;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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

            header("Location: /sign-in");
        }
        $user = $_SESSION['user'];

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
            $this->container->get('user_repository')->addBankAccount($user->id(), $owner, $iban);
        }
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

    public function addMoneyToWallet(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $amount = $data['amount'] ?? '';

        $user = $_SESSION['user'];
        $bank = $this->container->get('user_repository')->findBankAccount($user->id());
        if($amount > 0){
            $this->container->get('user_repository')->addMoneyToWallet($user->id(), $amount + $user->wallet());
            $message = 'Money added successfully to wallet';
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
                'load_message' => $message
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
}