<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class SociosController extends Controller
{
    public function index(): void
    {
        $this->renderModule('socios');
    }

    public function crear(): void
    {
        echo 'Socios: formulario crear.';
    }
}
