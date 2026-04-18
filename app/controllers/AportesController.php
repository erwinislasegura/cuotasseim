<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class AportesController extends Controller
{
    public function index(): void
    {
        $this->renderModule('aportes');
    }

    public function crear(): void
    {
        echo 'Aportes: formulario crear.';
    }
}
