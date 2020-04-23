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
        $messages = $this->container->get('flash')->getMessages();

        $notifications = $messages['notifications'] ?? [];

        return $this->container->get('view')->render(
            $response,
            'signup.twig',
            [
            ]
        );
    }

    public function validateUser(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $messages = $this->container->get('flash')->getMessages();

        $notifications = $messages['notifications'] ?? [];
        $errors = $this->isValid($data['email'], $data['password'], $data['birthday']);
        if(empty($errors))
        {
            $user = new User(
                $data['email'] ?? '',
                $data['password'] ?? '',
                $data['phone'] ?? '',
                new DateTime(),
                new DateTime(),
                new DateTime()
            );
            header("Location: ./sign-in");
        }

        return $this->container->get('view')->render(
            $response,
            'signup.twig',
            [
                'errors' => $errors
            ]
        );
        
    }

    function isValid(?string $email, ?string $password, ?string $birthday)
    {
        $errors = [];

        if ($this->validEmail($email)==false) {
            $errors[] = sprintf('The email %s is not valid', $email);
        }

        if (!preg_match('/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])[0-9A-Za-z!-\/]{1,}$/', $password)) {
            $errors[] = sprintf('The password must contain both numbers and letters', $password);
        }
        if (strlen($password) < 5) {
            $errors[] = sprintf('The password should be longer than or equal to 5 characters', $password);
        }
        if($this->validateAge($birthday) == false)
        {
            $errors[] = 'Only users of legal age (more than 18 years) can be registered';
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

    public function validateAge($birthday, $age = 18)
    {
        if(is_string($birthday)) {
            $birthday = strtotime($birthday);
        }

        if(time() - $birthday < $age * 31536000)  {
            return false;
        }
        return true;
    }

}