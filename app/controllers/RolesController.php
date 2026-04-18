<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class RolesController extends Controller
{
    public function index(): void
    {
        $this->renderModule('roles');
    }

    public function crear(): void
    {
        echo 'Roles: formulario crear.';
    }
}
