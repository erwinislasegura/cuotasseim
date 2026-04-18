<?php

declare(strict_types=1);

namespace App\Core;

class Auth
{
    public static function check(): bool
    {
        Session::start();
        return Session::get('user_id') !== null;
    }

    public static function user(): ?array
    {
        Session::start();
        return Session::get('user');
    }

    public static function login(array $user): void
    {
        Session::start();
        Session::put('user_id', $user['id']);
        Session::put('user', $user);
    }

    public static function logout(): void
    {
        Session::start();
        Session::destroy();
    }
}
