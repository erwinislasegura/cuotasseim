<?php

declare(strict_types=1);

function audit_payload(string $modulo, string $accion, array $datos = []): array
{
    return [
        'modulo' => $modulo,
        'accion' => $accion,
        'datos' => $datos,
        'fecha' => date('Y-m-d H:i:s'),
    ];
}
