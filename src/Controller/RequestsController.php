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
        $user = $_SESSION['user'];

        $pending = $this->container->get('user_repository')->findPendingRequests($user->email());
        return $this->container->get('view')->render(
            $response,
            'pending.twig',
            [
                'pending' => $pending
            ]
        );
    }
}