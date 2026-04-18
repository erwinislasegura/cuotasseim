<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;

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
            $this->redirect('/panel');
        }

        $this->view('auth/login', [
            'title' => 'Iniciar sesión',
            'token' => Csrf::token(),
            'error' => 'Credenciales inválidas.',
        ], 'auth');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/');
    }
}
