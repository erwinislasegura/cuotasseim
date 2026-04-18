<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class TiposSocioController extends Controller
{
    public function index(): void
    {
        $this->renderModule('tipos_socio');
    }

    public function crear(): void
    {
        echo 'TiposSocio: formulario crear.';
    }
}
