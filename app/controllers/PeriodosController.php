<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class PeriodosController extends Controller
{
    public function index(): void
    {
        $this->renderModule('periodos');
    }

    public function crear(): void
    {
        echo 'Periodos: formulario crear.';
    }
}
