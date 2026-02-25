<?php

namespace App\Controllers;

use App\Application\Actions\User\ListUsersAction;
use App\Models\BooksModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class BookController
{
    private Twig $view;
    private BooksModel $history;

    public function __construct(Twig $view, BooksModel $history)
    {
        $this->view  = $view;
        $this->history = $history;
    }

    public function historyBook(Request $request, Response $response, array $args): Response
    {
        $id = (int)($args['id'] ?? 0);

        if (!$id) {
            return $response->withStatus(404);
        }

        $history = $this->history->history($id);

        return $this->view->render($response, 'history.twig', [
            'history' => $history
        ]);
    }

    public function create(Request $request, Response $response, array $args): Response
    {

    }
}