<?php
declare(strict_types=1);

namespace App\Models;

use ORM;

class UsersModel
{

    public function getAll(): array
    {
        $users = ORM::for_table('users')->find_many();
        return $users;
    }

    public function getById(int $id): ?array
    {
        $user = ORM::for_table('users')->where('id', $id)->find_one();

        if (!$user) {
            return null;
        }

        return $user->as_array();
    }

    public function registration(string $login, string $password)
    {
        $user = ORM::for_table('users')->create();
        $user->login = $login;
        $user->password = md5($password);
        $user->role = 0;
        $user->save();
    }

    public function findByLogin(string $login): ?array
    {
        error_log("findByLogin called with: $login");

        $user = ORM::for_table('users')
            ->where('login', $login)
            ->find_one();

        if (!$user) {
            error_log("User not found in DB");
            return null;
        }

        error_log("User found: " . $user->login . ", role: " . $user->role);
        error_log("Password hash in DB: " . $user->password);

        return $user->as_array();
    }

    public function login(string $login)
    {
        $user = ORM::for_table('users')->where('login', $login)->find_one();
        return $user;
    }
}
