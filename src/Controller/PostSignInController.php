<?php

namespace SallePW\SlimApp\Controller;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use SallePW\SlimApp\Controller\ValidateController;

final class PostSignInController
{
    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody(); //Array associatiu, reusltat de llegir el JSON rebut

        $email=$data['email'];
        $password=$data['password'];
        $errors = ValidateController::class . ":isValid";
        //$errors = isValid($email, $password);

        return $response->withStatus(201);
    }


}