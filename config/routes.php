<?php

use SallePW\SlimApp\Controller\BankController;
use SallePW\SlimApp\Controller\DashBoardController;
use \SallePW\SlimApp\Controller\HomeController;
use SallePW\SlimApp\Controller\PostSignInController;
use SallePW\SlimApp\Controller\ProfileController;
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
    '/account/summary',
    DashBoardController::class . ":showDashboard"
)->setName('dashboard');

$app->post(
    '/logout',
    SignInController::class . ":logout"
)->setName('logout');

$app->get(
    '/profile',
    ProfileController::class . ":showProfile"
)->setName('profile');

$app->post(
    '/profile',
    ProfileController::class . ":updateProfile"
)->setName('profile');

$app->get(
    '/profile/security',
    ProfileController::class . ":showProfileSecurity"
)->setName('profile');

$app->post(
    '/profile/security',
    ProfileController::class . ":updateProfileSecurity"
)->setName('profile');

$app->get(
    '/account/bank-account',
    BankController::class . ":showBankAccount"
)->setName('profile');

$app->post(
    '/account/bank-account',
    BankController::class . ":addBankAccount"
)->setName('profile');

$app->post(
    '/account/bank-account/load',
    BankController::class . ":addMoneyToWallet"
)->setName('profile');

$app->get(
    '/account/money/send',
    BankController::class . ":sendMoney"
)->setName('profile');

$app->post(
    '/account/money/send',
    BankController::class . ":sendMoney"
)->setName('profile');
