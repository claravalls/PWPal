<?php


namespace SallePW\SlimApp\Controller;

use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class DashBoardController
{
    public const DEFAULT_PICTURE = 'https://moonvillageassociation.org/wp-content/uploads/2018/06/default-profile-picture1.jpg';

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showDashBoard(Request $request, Response $response):Response
    {
        if (!isset($_SESSION['user'])){

            header("Location: /sign-in");
        }
        $user = $_SESSION['user'];

        $bank_id = $this->container->get('user_repository')->findBankAccount($user->id());
        $path = basename("/public/uploads/");

        if($bank_id->id() >= 0){
            return $this->container->get('view')->render(
                $response,
                'dash.twig',
                [
                    'photo' => "../../".$path."/".$user->photo(),
                    'bank_account' => $bank_id,
                    'wallet' => $user->wallet()
                ]
            );
        }
        return $this->container->get('view')->render(
            $response,
            'dash.twig',
            [
                'photo' => "../../".$path."/".$user->photo(),
                'wallet' => $user->wallet()
            ]
        );
    }
}

