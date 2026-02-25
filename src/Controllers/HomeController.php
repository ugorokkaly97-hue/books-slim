<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\AuthorsModel;
use App\Models\BooksModel;
use app\Models\UsersModel;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController
{
    private Twig $view;
    private BooksModel $books;

    private UsersModel $users;

    public function __construct(Twig $view, BooksModel $books, UsersModel $users)
    {
        $this->view  = $view;
        $this->books = $books;
        $this->users = $users;
    }

    public function index(Request $request, Response $response): Response
    {
        $books = $this->books->getAll();

        $currentUser = null;

        // Получаем пользователя из сессии, если есть
        if (isset($_SESSION['user_id'])) {
            $currentUser = $this->users->getById($_SESSION['user_id']);
        }

        // Выбираем шаблон по роли
        $template = 'index.twig';
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1) {
            $template = 'admin.twig';
        }

        return $this->view->render($response, $template, [
            'books' => $books,
            'user' => $currentUser,
        ]);
    }

    public function createForm(Request $request, Response $response, $args): Response
    {
        $authors = $this->books->getAuthors();

        return $this->view->render($response, 'create.twig', [
            'authors' => $authors
        ]);
    }

    public function create(Request $request, Response $response, $args): Response
    {
        $data = $request->getParsedBody();

        $name = $data['name'] ?? '';
        $authorId = (int)($data['author_id'] ?? 0);

        if (empty($name) || $authorId <= 0) {
            return $response->withHeader('Location', '/admin/books/create')
                ->withStatus(302);
        }

        $this->books->creation($name, $authorId);

        return $response->withHeader('Location', '/')
            ->withStatus(302);
    }

    public function updateForm(Request $request, Response $response, $args): Response
    {
        $id = (int)($args['id'] ?? 0);

        $book = $this->books->getById($id);
        $authors = $this->books->getAuthors();

        return $this->view->render($response, 'update.twig', [
            'book' => $book,
            'authors' => $authors
        ]);
    }

    public function update(Request $request, Response $response, $args): Response
    {
        $data = $request->getParsedBody();

        $id = (int)($args['id'] ?? 0);
        $name = $data['name'] ?? '';
        $authorId = (int)($data['author_id'] ?? 0);
        $available = (int) ($data['available'] ?? 0);

        if (empty($name) || $authorId <= 0) {
            return $response->withHeader('Location', '/admin/books/update/{id}')
                ->withStatus(302);
        }

        $this->books->update($id, $name, $authorId, $available);

        return $response->withHeader('Location', '/')
            ->withStatus(302);
    }

    public function delete(Request $request, Response $response, $args): Response
    {
        $id = (int)($args['id'] ?? 0);

        if ($id <= 0) {
            return $response->withHeader('Location', '/')
                ->withStatus(302);
        }

        $this->books->delete($id);

        return $response->withHeader('Location', '/')
            ->withStatus(302);
    }
}
