<?php

declare(strict_types=1);

namespace App\Core;

class Validator
{
    public static function required(array $input, array $fields): array
    {
        $errors = [];

        foreach ($fields as $field) {
            if (!isset($input[$field]) || trim((string) $input[$field]) === '') {
                $errors[$field] = 'El campo es obligatorio.';
            }
        }

        return $errors;
    }
}
