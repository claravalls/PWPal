<?php

namespace SallePW\SlimApp\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class HomeController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showHomePage(Request $request, Response $response): Response
    {
        $messages = $this->container->get('flash')->getMessages();

        if (!isset($_SESSION['user'])){
            return $this->container->get('view')->render(
                $response,
                'home.twig',
                [
                ]
            );
        }else {
            $user = $_SESSION['user'];
            return $this->container->get('view')->render(
                $response,
                'home.twig',
                [
                    'user' => $user,
                    'photo' => $user->photo(),
                    'mail' => $user->email()
                ]
            );
        }
    }
}
