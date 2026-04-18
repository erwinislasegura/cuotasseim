<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class TiposEgresoController extends Controller
{
    public function index(): void
    {
        $this->renderModule('tipos_egreso');
    }

    public function crear(): void
    {
        echo 'TiposEgreso: formulario crear.';
    }
}
