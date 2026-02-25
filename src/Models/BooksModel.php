<?php

namespace App\Models;

use ORM;

class BooksModel
{
    public function getAll(): array
    {
        $books = ORM::for_table('books')
            ->select('books.id')
            ->select('books.available')
            ->select('books.name', 'book_name')
            ->select('authors.name', 'author_name')
            ->select('authors.id', 'author_id')
            ->join('authors_book', 'books.id = authors_book.book_id')
            ->join('authors', 'authors.id = authors_book.author_id')
            ->find_many();

        return $books;
    }

    public function getById(int $id): ?array
    {
        $book = ORM::for_table('books')
            ->select('books.id')
            ->select('books.available')
            ->select('books.name', 'book_name')
            ->select('authors.name', 'author_name')
            ->select('authors.id', 'author_id')
            ->join('authors_book', 'books.id = authors_book.book_id')
            ->join('authors', 'authors.id = authors_book.author_id')
            ->find_one($id);
        return $book->as_array();
    }

    public function history(int $id): array
    {
        $history = ORM::for_table('history')
        ->select('history.*')
        ->select('books.name', 'name')
        ->join('books', 'books.id = history.book_id')
        ->where('history.book_id', $id)
        ->order_by_desc('history.date_begin')
        ->find_array();

        return $history;
    }

    public function getAuthors(){
        return ORM::for_table('authors')
            ->select('authors.id')
            ->select('authors.name')
            ->order_by_asc('name')
            ->find_array();
    }

    public function creation(string $name, int $authorId)
    {
        $book = ORM::for_table('books')->create();
        $book->name = $name;
        $book->available = 1;
        $book->save();

        $authorBook = ORM::for_table('authors_book')->create();
        $authorBook->book_id = $book->id;
        $authorBook->author_id = $authorId;
        $authorBook->save();
    }

    public function update(int $id, string $name, int $authorId, int $available)
    {
        $books = ORM::for_table('books')->find_one($id);
        $books->set(array(
            'name' => $name,
            'available' => $available,
        ));
        $books->save();

        $authorBook = ORM::for_table('authors_book')
            ->where('book_id', $id)
            ->find_one();

        $authorBook->author_id = $authorId;
        $authorBook->save();

            return true;
        }

    public function delete(int $id): bool
    {
        $book = ORM::for_table('books')->find_one($id);

        if (!$book) {
            return false;
        }

        $activeHistory = ORM::for_table('history')
            ->where('book_id', $id)
            ->where_raw('date_end >= CURDATE()')
            ->count();

        if ($activeHistory > 0) {
            return false;
        }

        ORM::for_table('authors_book')
            ->where('book_id', $id)
            ->delete_many();

        ORM::for_table('history')
            ->where('book_id', $id)
            ->delete_many();

        $book->delete();

        return true;
    }
}