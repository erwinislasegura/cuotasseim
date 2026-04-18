<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class AuditoriaController extends Controller
{
    public function index(): void
    {
        $this->renderModule('auditoria');
    }

    public function crear(): void
    {
        echo 'Auditoria: formulario crear.';
    }
}
