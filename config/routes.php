<?php

use SallePW\SlimApp\Controller\BankController;
use SallePW\SlimApp\Controller\DashBoardController;
use \SallePW\SlimApp\Controller\HomeController;
use SallePW\SlimApp\Controller\PostSignInController;
use SallePW\SlimApp\Controller\ProfileController;
use SallePW\SlimApp\Controller\RequestsController;
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
)->setName('bank');

$app->post(
    '/account/bank-account',
    BankController::class . ":addBankAccount"
)->setName('bank');

$app->post(
    '/account/bank-account/load',
    BankController::class . ":addMoneyToWallet"
)->setName('addMoney');

$app->get(
    '/account/money/send',
    BankController::class . ":showSendMoneyPage"
)->setName('pageSend');

$app->post(
    '/account/money/send',
    BankController::class . ":sendMoney"
)->setName('sendMoney');

$app->get(
    '/account/transactions',
    BankController::class . ":showAllTransactions"
)->setName('bank');

$app->get(
    '/account/money/requests',
    RequestsController::class . ":showRequestMoneyPage"
)->setName('pageRequest');

$app->post(
    '/account/money/requests',
    BankController::class . ":requestMoney"
)->setName('requestMoney');

$app->get(
    '/account/money/requests/pending',
    RequestsController::class . ":showPendingRequestsPage"
)->setName('pendingRequests');

$app->get(
    '/account/money/requests/{id}/accept',
    BankController::class . ":sendMoney"
)->setName('acceptRequest');
