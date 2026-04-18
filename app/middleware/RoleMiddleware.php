<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;

class RoleMiddleware
{
    public static function handle(array $allowedRoles): bool
    {
        $user = Auth::user();
        return $user !== null && in_array($user['rol'] ?? '', $allowedRoles, true);
    }
}
