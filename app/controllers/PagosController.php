<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class PagosController extends Controller
{
    public function index(): void
    {
        $this->renderModule('pagos');
    }

    public function crear(): void
    {
        echo 'Pagos: formulario crear.';
    }
}
