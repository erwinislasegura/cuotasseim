<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class MediosPagoController extends Controller
{
    public function index(): void
    {
        $this->renderModule('medios_pago');
    }

    public function crear(): void
    {
        echo 'MediosPago: formulario crear.';
    }
}
