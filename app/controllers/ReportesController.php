<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class ReportesController extends Controller
{
    public function index(): void
    {
        $this->renderModule('reportes');
    }

    public function crear(): void
    {
        echo 'Reportes: formulario crear.';
    }
}
