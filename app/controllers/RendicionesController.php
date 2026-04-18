<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class RendicionesController extends Controller
{
    public function index(): void
    {
        echo 'Rendiciones: módulo en construcción con base MVC y DB preparada.';
    }

    public function crear(): void
    {
        echo 'Rendiciones: formulario crear.';
    }
}
