<?php


namespace SallePW\SlimApp\Controller;


use Psr\Container\ContainerInterface;
use DateTime;
use SallePW\SlimApp\Model\User;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class SignUpController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showSignUp(Request $request, Response $response): Response
    {
        //$messages = $this->container->get('flash')->getMessages();

        //$notifications = $messages['notifications'] ?? [];

        return $this->container->get('view')->render(
            $response,
            'signup.twig',
            [
            ]
        );
    }
}