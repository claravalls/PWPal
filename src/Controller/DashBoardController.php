<?php


namespace SallePW\SlimApp\Controller;

use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use SallePW\SlimApp\Model\TransactionList;

final class DashBoardController
{
    public const DEFAULT_PICTURE = 'default.jpg';

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showDashBoard(Request $request, Response $response):Response
    {
        if (!isset($_SESSION['user'])){
            echo "<script>
            alert('Log in to access to your dashboard');
            window.location.href='/sign-in';
            </script>";
        }
        $user = $_SESSION['user'];
        $user = $this->container->get('user_repository')->search($user->email(), "email");

        $bank_id = $this->container->get('user_repository')->findBankAccount($user->id());
        $path = basename("/public/uploads/");

        $list = $this->container->get('user_repository')->latestTransactions($user->email());

        if($bank_id->id() >= 0){
            return $this->container->get('view')->render(
                $response,
                'dash.twig',
                [
                    'photo' => "../../".$path."/".$user->photo(),
                    'bank_account' => $bank_id,
                    'wallet' => $user->wallet(),
                    'other1' => $list->getOtherUser(1),
                    'other2' => $list->getOtherUser(2),
                    'other3' => $list->getOtherUser(3),
                    'other4' => $list->getOtherUser(4),
                    'other5' => $list->getOtherUser(5),
                    'trans1' => $list->getTransaction(1),
                    'trans2' => $list->getTransaction(2),
                    'trans3' => $list->getTransaction(3),
                    'trans4' => $list->getTransaction(4),
                    'trans5' => $list->getTransaction(5),
                    'sign1' => $list->getSign(1),
                    'sign2' => $list->getSign(2),
                    'sign3' => $list->getSign(3),
                    'sign4' => $list->getSign(4),
                    'sign5' => $list->getSign(5)
                ]
            );
        }
        return $this->container->get('view')->render(
            $response,
            'dash.twig',
            [
                'photo' => "../../".$path."/".$user->photo(),
                'wallet' => $user->wallet(),
                'other1' => $list->getOtherUser(1),
                'other2' => $list->getOtherUser(2),
                'other3' => $list->getOtherUser(3),
                'other4' => $list->getOtherUser(4),
                'other5' => $list->getOtherUser(5),
                'trans1' => $list->getTransaction(1),
                'trans2' => $list->getTransaction(2),
                'trans3' => $list->getTransaction(3),
                'trans4' => $list->getTransaction(4),
                'trans5' => $list->getTransaction(5),
                'sign1' => $list->getSign(1),
                'sign2' => $list->getSign(2),
                'sign3' => $list->getSign(3),
                'sign4' => $list->getSign(4),
                'sign5' => $list->getSign(5)
            ]
        );
    }
}

