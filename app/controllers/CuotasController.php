<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class CuotasController extends Controller
{
    public function index(): void
    {
        $this->renderModule('cuotas');
    }

    public function crear(): void
    {
        echo 'Cuotas: formulario crear.';
    }
}
