<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class ConceptosCobroController extends Controller
{
    public function index(): void
    {
        $this->renderModule('conceptos_cobro');
    }

    public function crear(): void
    {
        echo 'ConceptosCobro: formulario crear.';
    }
}
