<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class UsuariosController extends Controller
{
    public function index(): void
    {
        echo 'Usuarios: módulo en construcción con base MVC y DB preparada.';
    }

    public function crear(): void
    {
        echo 'Usuarios: formulario crear.';
    }
}
