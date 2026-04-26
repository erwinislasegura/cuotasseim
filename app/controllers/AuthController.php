<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\ModuleCatalog;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth/login', ['title' => 'Iniciar sesión', 'token' => Csrf::token()], 'auth');
    }

    public function login(): void
    {
        if (!Csrf::validate($_POST['_token'] ?? null)) {
            http_response_code(419);
            echo 'Token CSRF inválido';
            return;
        }

        $username = trim((string) ($_POST['usuario'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $demoUser = [
            'id' => 1,
            'nombre' => 'Administrador',
            'usuario' => 'admin',
            'correo' => 'admin@demo.local',
            'rol' => 'Administrador',
            'password' => password_hash('Admin123*', PASSWORD_DEFAULT),
        ];

        if (($username === $demoUser['usuario'] || $username === $demoUser['correo']) && password_verify($password, $demoUser['password'])) {
            Auth::login($demoUser);
            ModuleCatalog::registerAudit(
                'auth',
                'login',
                (int) ($demoUser['id'] ?? 0),
                null,
                [
                    'usuario' => (string) ($demoUser['usuario'] ?? ''),
                    'correo' => (string) ($demoUser['correo'] ?? ''),
                ]
            );
            $this->redirect('/panel');
        }

        ModuleCatalog::registerAudit(
            'auth',
            'login_fallido',
            null,
            null,
            ['usuario_ingresado' => $username]
        );

        $this->view('auth/login', [
            'title' => 'Iniciar sesión',
            'token' => Csrf::token(),
            'error' => 'Credenciales inválidas.',
        ], 'auth');
    }

    public function logout(): void
    {
        $user = Auth::user();
        ModuleCatalog::registerAudit(
            'auth',
            'logout',
            (int) ($user['id'] ?? 0),
            ['usuario' => (string) ($user['usuario'] ?? ''), 'correo' => (string) ($user['correo'] ?? '')],
            null
        );
        Auth::logout();
        $this->redirect('/');
    }
}
