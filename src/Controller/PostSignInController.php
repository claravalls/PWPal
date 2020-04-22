<?php

namespace SallePW\SlimApp\Controller;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class PostSignInController
{
    public function create(Request $request, Response $response): Response
    {
        return $response->withStatus(201);
    }
}