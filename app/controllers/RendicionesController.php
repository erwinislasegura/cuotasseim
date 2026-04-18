<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class RendicionesController extends Controller
{
    public function index(): void
    {
        $this->renderModule('rendiciones');
    }

    public function crear(): void
    {
        echo 'Rendiciones: formulario crear.';
    }
}
