<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class ConfiguracionController extends Controller
{
    public function index(): void
    {
        $this->renderModule('configuracion');
    }

    public function crear(): void
    {
        echo 'Configuracion: formulario crear.';
    }
}
