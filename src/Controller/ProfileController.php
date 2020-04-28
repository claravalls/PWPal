<?php

namespace SallePW\SlimApp\Controller;

use DateTime;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SallePW\SlimApp\Model\User;

final class ProfileController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showProfile(Request $request, Response $response):Response
    {
        /*if (!isset($_SESSION['user'])){

            header("Location: /sign-in");
        }*/
        //$user = $_SESSION['user'];
        $dateBirthday = DateTime::createFromFormat('Y-m-d', "1997-07-12");
        $date = DateTime::createFromFormat('Y-m-d', "2020-05-12");
        $create = DateTime::createFromFormat('Y-m-d',  "2020-05-12");
        $user = new User("daniel@salle.url.edu", "123345Dmez", "+34612123123", $dateBirthday,  $date,  $create,'https://moonvillageassociation.org/wp-content/uploads/2018/06/default-profile-picture1.jpg', 20, "1232312312313", true);
        return $this->container->get('view')->render(
            $response,
            'profile.twig',
            [
                'email' => $user->email(),
                'birthday' => $user->birthday()->format('Y-m-d'),
                'phone' => $user->telefon(),
                'photo' => $user->photo()
            ]
        );
    }

    public function updateProfile(Request $request, Response $response):Response
    {
        /*if (!isset($_SESSION['user'])){

            header("Location: /sign-in");
        }*/
        //$user = $_SESSION['user'];
        return $this->container->get('view')->render(
            $response,
            'profile.twig',
            []
        );
    }

    public function showProfileSecurity(Request $request, Response $response):Response
    {
        /*if (!isset($_SESSION['user'])){

            header("Location: /sign-in");
        }*/
        //$user = $_SESSION['user'];
        return $this->container->get('view')->render(
            $response,
            'security.twig',
            []
        );
    }

    public function updateProfileSecurity(Request $request, Response $response):Response
    {
        /*if (!isset($_SESSION['user'])){

            header("Location: /sign-in");
        }*/
        //$user = $_SESSION['user'];
        return $this->container->get('view')->render(
            $response,
            'security.twig',
            []
        );
    }


}