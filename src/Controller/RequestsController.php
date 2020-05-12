<?php


namespace SallePW\SlimApp\Controller;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class RequestsController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showRequestMoneyPage (Request $request, Response $response): Response
    {
        if (!isset($_SESSION['user'])){
            echo "<script>
            alert('Log in to access to your bank account');
            window.location.href='/sign-in';
            </script>";
        }
        return $this->container->get('view')->render(
            $response,
            'requestMoney.twig',
            [
            ]
        );
    }

    public function showPendingRequestsPage (Request $request, Response $response): Response
    {
        if (!isset($_SESSION['user'])){
            echo "<script>
            alert('Log in to access to your bank account');
            window.location.href='/sign-in';
            </script>";
        }
        return $this->container->get('view')->render(
            $response,
            'pending.twig',
            [
            ]
        );
    }

    public function requestMoney(Request $request, Response $response): Response{
        if (!isset($_SESSION['user'])){
            echo "<script>
            alert('Log in to access to your bank account');
            window.location.href='/sign-in';
            </script>";
        }
        $user = $_SESSION['user'];
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

}