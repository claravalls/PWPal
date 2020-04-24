<?php


namespace SallePW\SlimApp\Controller;


use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;

class ValidateController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function isValid(?string $email, ?string $password, Response $response):Response
    {
        $errors = [];

        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->container->get('flash')->addMessage(
                'The email is not valid.'
            );
        }
        if (!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $password)) {
            $this->container->get('flash')->addMessage(
                'The password must contain both numbers and letters'
            );

        }
        if (strlen($password) < 5) {

            $this->container->get('flash')->addMessage(
                'The password should be longer than or equal to 5 characters'
            );
        }
        // Split the email address at the @ symbol
        $email_parts = explode('@', $email);

        $domain = array_pop($email_parts);
        if(strcmp($domain, "salle.url.edu")){
            $this->container->get('flash')->addMessage(
                'The domain is not accepted'
            );
        }
        return $response;
    }
}