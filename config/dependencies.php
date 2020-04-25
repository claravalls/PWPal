<?php

use DI\Container;
use SallePW\SlimApp\Model\User;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use SallePW\SlimApp\Repository\MySQLUserRepository;
use SallePW\SlimApp\Repository\PDOSingleton;
use Psr\Container\ContainerInterface;

$container = new Container();

$container->set(
    'view',
    function () {
        return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
    }
);

$container->set(
    'flash',
    function () {
        return new Messages();
    }
);

$container->set('db', function () {
    return PDOSingleton::getInstance(
        $_ENV['MYSQL_USER'],
        $_ENV['MYSQL_ROOT_PASSWORD'],
        $_ENV['MYSQL_HOST'],
        $_ENV['MYSQL_PORT'],
        $_ENV['MYSQL_DATABASE']
    );
});

$container->set('user_repository', function (ContainerInterface $container) {
    return new MySQLUserRepository($container->get('db'));
});

/*
 * CREATE TABLE `user` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `email` VARCHAR(255) NOT NULL DEFAULT '',
        `password` VARCHAR(255) NOT NULL DEFAULT '',
        `telefon` VARCHAR(255), `birthday` DATETIME NOT NULL,
        `created_at` DATETIME NOT NULL,
        `updated_at` DATETIME NOT NULL,
        `photo` VARCHAR(255),
        `token` VARCHAR(255),
        `activated` BOOLEAN NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */