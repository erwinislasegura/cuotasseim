<?php

declare(strict_types=1);

use App\Core\Csrf;

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') . '">';
}
