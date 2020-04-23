<?php

use \SallePW\SlimApp\Controller\HomeController;
use SallePW\SlimApp\Controller\PostSignInController;
use SallePW\SlimApp\Controller\SignInController;
use SallePW\SlimApp\Controller\SignUpController;
use \SallePW\SlimApp\Middleware\StartSessionMiddleware;

use \SallePW\SlimApp\Controller\FlashController;

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
    PostSignInController::class . ":signInUser"
)->setName('create_user');

$app->get(
    '/sign-up',
    SignUpController::class . ":showSignUp"
)->setName('sign-up');

$app->post(
    '/sign-up',
    PostSignInController::class . ":signInUser"
)->setName('create_user');

/*
$app->get(
    '/visits',
    VisitsController::class . ":showVisits"
)->setName('visits');

$app->get(
    '/cookies',
    CookieMonsterController::class . ":showAdvice"
)->setName('cookies');

$app->get(
    '/flash',
    FlashController::class . ":addMessage"
)->setName('flash');

$app->post(
    '/users',
    PostUserController::class . ":create"
)->setName('create_user');
*/
