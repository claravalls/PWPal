<?php


namespace SallePW\SlimApp\Controller;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class SignUpController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showSignUp(Request $request, Response $response): Response
    {
        $messages = $this->container->get('flash')->getMessages();

        $notifications = $messages['notifications'] ?? [];

        return $this->container->get('view')->render(
            $response,
            'signup.twig',
            []
        );
    }
}