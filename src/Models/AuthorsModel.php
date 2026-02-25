<?php

namespace App\Models;

use ORM;

class AuthorsModel
{
    public function getById(int $id): ?array
    {
        $author = ORM::for_table('author_books')->where($id)->find_one();
        return $author;
    }

}