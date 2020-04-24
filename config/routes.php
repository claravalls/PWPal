<?php

use \SallePW\SlimApp\Controller\HomeController;
use SallePW\SlimApp\Controller\PostSignInController;
use SallePW\SlimApp\Controller\SignInController;
use SallePW\SlimApp\Controller\SignUpController;
use SallePW\SlimApp\Controller\ValidateController;
use \SallePW\SlimApp\Middleware\StartSessionMiddleware;

use \SallePW\SlimApp\Controller\FlashController;
use SallePW\SlimApp\Model\User;

$app->add(StartSessionMiddleware::class);

$app->get(
    '/',
    HomeController::class . ":showHomePage"
)->setName('home');

$app->get(
    '/sign-in',
    SignInController::class . ":showSignIn"
)->setName('sign-in');

$app->post(
    '/sign-in',
    ValidateController::class . ":validateUser"
)->setName('create_user');

$app->get(
    '/sign-up',
    SignUpController::class . ":showSignUp"
)->setName('sign-up');

$app->get(
    '/activate',
    ValidateController::class . ":emailActivation"
)->setName('accepted');

$app->post(
    '/sign-up',
    ValidateController::class . ":validateUser"
)->setName('create_user');

$app->get(
    '/flash',
    FlashController::class . ":addMessage"
)->setName('flash');

/*
$app->get(
    '/visits',
    VisitsController::class . ":showVisits"
)->setName('visits');

$app->get(
    '/cookies',
    CookieMonsterController::class . ":showAdvice"
)->setName('cookies');



$app->post(
    '/users',
    PostUserController::class . ":create"
)->setName('create_user');
*/
