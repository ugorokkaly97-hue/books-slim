<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Controllers\HomeController;
use App\Controllers\BookController;
use App\Controllers\UserController;

return function (App $app) {

    $app->get('/', [HomeController::class, 'index']);
    $app->post('/', [HomeController::class, 'index']);

    $app->get('/login', [UserController::class, 'loginForm']);
    $app->post('/login', [UserController::class, 'login']);

    $app->get('/logout', [UserController::class, 'logout']);

    $app->get('/registration', [UserController::class, 'registrationForm']);
    $app->post('/registration', [UserController::class, 'registration']);


    $app->group('/admin/books', function (Group $group) {

        $group->get('/create', [HomeController::class, 'createForm']);
        $group->post('/create', [HomeController::class, 'create']);

        $group->get('/update/{id}', [HomeController::class, 'updateForm']);
        $group->post('/update/{id}', [HomeController::class, 'update']);

        $group->get('/delete/{id}', [HomeController::class, 'delete']);
        $group->post('/delete/{id}', [HomeController::class, 'delete']);

        $group->get('/{id}', [BookController::class, 'historyBook']);
        $group->post('/{id}', [BookController::class, 'historyBook']);

        $group->get('/', HomeController::class, 'admin');
        $group->post('/', HomeController::class, 'admin');
    });
};
