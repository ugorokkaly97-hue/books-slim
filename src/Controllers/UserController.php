<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\UsersModel;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    private Twig $view;
    private UsersModel $users;

    public function __construct(Twig $view, UsersModel $users)
    {
        $this->view  = $view;
        $this->users = $users;
    }

    public function registrationForm(Request $request, Response $response, $args): Response
    {
        return $this->view->render($response, 'registration.twig');
    }

    public function registration(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $login = $data['login'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($login) || empty($password)) {
            return $response->withHeader('Location', '/')
                ->withStatus(302);
        }

        $this->users->registration($login, $password);

        return $this->view->render($response, 'registration.twig');
    }

    public function loginForm(Request $request, Response $response, $args): Response
    {
        return $this->view->render($response, 'login.twig');
    }

    public function login(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $login = $data['login'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->users->findByLogin($login);

        if (!$user) {
            error_log("FAIL: User not found");
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        if (md5($password) !== $user['password']) {
            error_log("FAIL: Password mismatch");
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_login'] = $user['login'];
        $_SESSION['user_role'] = $user['role'];

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function logout(Request $request, Response $response, $args): Response
    {
        session_unset();
        session_destroy();

        return $response->withHeader('Location', '/')
            ->withStatus(302);
    }
}