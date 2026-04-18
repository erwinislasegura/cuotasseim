<?php

declare(strict_types=1);

function normalize_status(mixed $status): string
{
    return strtolower(trim((string) $status));
}

function status_label(mixed $status): string
{
    $normalized = normalize_status($status);

    return match ($normalized) {
        'activo' => 'Activo',
        'inactivo' => 'Inactivo',
        'pagada' => 'Pagada',
        'pendiente' => 'Pendiente',
        'vencida' => 'Vencida',
        'abonada_parcial' => 'Abonada parcial',
        'anulada', 'anulado' => 'Anulada',
        'aplicado' => 'Aplicado',
        'pendiente_revision' => 'Pendiente revisión',
        'conciliado' => 'Conciliado',
        'sin_conciliar' => 'Sin conciliar',
        'rendida' => 'Rendida',
        'cerrada' => 'Cerrada',
        'abierta' => 'Abierta',
        'exenta' => 'Exenta',
        default => ucwords(str_replace('_', ' ', $normalized !== '' ? $normalized : 'Sin estado')),
    };
}

function status_badge_class(mixed $status): string
{
    $normalized = normalize_status($status);

    return match ($normalized) {
        'activo', 'pagada', 'aplicado', 'conciliado', 'rendida', 'cerrada' => 'bg-status-active',
        'inactivo', 'anulada', 'anulado' => 'bg-status-inactive',
        'pendiente', 'pendiente_revision', 'sin_conciliar', 'abierta' => 'bg-status-pendiente',
        'vencida' => 'bg-status-vencida',
        'abonada_parcial', 'exenta' => 'bg-status-abonada_parcial',
        default => 'bg-status-info',
    };
}

function badge_class(string $status): string
{
    return status_badge_class($status);
}
