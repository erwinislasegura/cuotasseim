<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class TesoreriaController extends Controller
{
    public function index(): void
    {
        echo 'Tesoreria: módulo en construcción con base MVC y DB preparada.';
    }

    public function crear(): void
    {
        echo 'Tesoreria: formulario crear.';
    }
}
