<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class ConfiguracionController extends Controller
{
    public function index(): void
    {
        echo 'Configuracion: módulo en construcción con base MVC y DB preparada.';
    }

    public function crear(): void
    {
        echo 'Configuracion: formulario crear.';
    }
}
