<?php

declare(strict_types=1);

function money(float|int $value): string
{
    return '$' . number_format((float) $value, 0, ',', '.');
}
