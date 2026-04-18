<?php

declare(strict_types=1);

use App\Core\Auth;

function auth_user(): ?array
{
    return Auth::user();
}
