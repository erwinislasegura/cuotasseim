<?php

declare(strict_types=1);

function badge_class(string $status): string
{
    return match ($status) {
        'pagada', 'aplicado', 'activo' => 'bg-success',
        'pendiente', 'pendiente_revision' => 'bg-warning text-dark',
        'vencida', 'anulado', 'moroso' => 'bg-danger',
        default => 'bg-secondary',
    };
}
