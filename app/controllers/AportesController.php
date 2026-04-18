<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class AportesController extends Controller
{
    public function index(): void
    {
        echo 'Aportes: módulo en construcción con base MVC y DB preparada.';
    }

    public function crear(): void
    {
        echo 'Aportes: formulario crear.';
    }
}
