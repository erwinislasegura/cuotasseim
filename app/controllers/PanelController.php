<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class PanelController extends Controller
{
    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $kpis = [
            'socios_activos' => 124,
            'recaudado_mes' => 2450000,
            'adeudado_total' => 786000,
            'saldo_estimado' => 1664000,
        ];

        $this->view('panel/index', ['title' => 'Panel de control', 'kpis' => $kpis]);
    }
}
