<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class TiposAporteController extends Controller
{
    public function index(): void
    {
        $this->renderModule('tipos_aporte');
    }

    public function crear(): void
    {
        echo 'TiposAporte: formulario crear.';
    }
}
