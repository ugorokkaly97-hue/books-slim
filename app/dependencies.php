<?php
declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Controllers\HomeController;
use App\Models\AuthorsModel;
use App\Controllers\BookController;
use App\Models\BooksModel;
use App\Controllers\UserController;
use App\Models\UsersModel;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        // Логгер
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $loggerSettings = $settings->get('logger');

            $logger = new Logger($loggerSettings['name']);
            $logger->pushProcessor(new UidProcessor());
            $logger->pushHandler(new StreamHandler(
                $loggerSettings['path'],
                $loggerSettings['level']
            ));

            return $logger;
        },

        //ORM
      //  ORM::class => function (ContainerInterface $c) {

      //  },

        // Twig
        Twig::class => function () {
            return Twig::create(__DIR__ . '/../templates', [
                'cache' => false,
            ]);
        },

        // Модель
        UsersModel::class => function (ContainerInterface $c) {
            return new UsersModel();
        },

        BooksModel::class => function (ContainerInterface $c) {
            return new BooksModel();
        },

        AuthorsModel::class => function (ContainerInterface $c) {
        return new AuthorsModel();
        },

        // Контроллеры
        HomeController::class => function (ContainerInterface $c) {
            return new HomeController(
                $c->get(Twig::class),
                $c->get(BooksModel::class),
                $c->get(UsersModel::class),
            );
        },

        UserController::class => function (ContainerInterface $c) {
            return new UserController(
                $c->get(Twig::class),
                $c->get(UsersModel::class),
            );
        },

        BookController::class => function (ContainerInterface $c) {
            return new BookController(
                $c->get(Twig::class),
                $c->get(BooksModel::class),
            );
        },

        AuthorController::class => function (ContainerInterface $c) {
            return new AuthorController(
                $c->get(Twig::class),
                $c->get(AuthorController::class),
        );
    }
    ]);
};
