<?php

declare(strict_types=1);

namespace App\Core;

class Csrf
{
    public static function token(): string
    {
        Session::start();
        if (!isset($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_token'];
    }

    public static function validate(?string $token): bool
    {
        Session::start();
        return is_string($token) && hash_equals($_SESSION['_token'] ?? '', $token);
    }
}
