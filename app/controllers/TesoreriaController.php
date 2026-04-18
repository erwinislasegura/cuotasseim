<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class TesoreriaController extends Controller
{
    public function index(): void
    {
        $this->renderModule('tesoreria');
    }

    public function crear(): void
    {
        echo 'Tesoreria: formulario crear.';
    }
}
