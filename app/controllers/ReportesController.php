<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class ReportesController extends Controller
{
    public function index(): void
    {
        echo 'Reportes: módulo en construcción con base MVC y DB preparada.';
    }

    public function crear(): void
    {
        echo 'Reportes: formulario crear.';
    }
}
