<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class AuditoriaController extends Controller
{
    public function index(): void
    {
        echo 'Auditoria: módulo en construcción con base MVC y DB preparada.';
    }

    public function crear(): void
    {
        echo 'Auditoria: formulario crear.';
    }
}
