<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class EgresosController extends Controller
{
    public function index(): void
    {
        $this->renderModule('egresos');
    }

    public function crear(): void
    {
        echo 'Egresos: formulario crear.';
    }
}
