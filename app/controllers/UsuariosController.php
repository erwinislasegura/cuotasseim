<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class UsuariosController extends Controller
{
    public function index(): void
    {
        $this->renderModule('usuarios');
    }

    public function crear(): void
    {
        echo 'Usuarios: formulario crear.';
    }
}
