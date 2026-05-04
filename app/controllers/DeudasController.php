<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use Throwable;

class DeudasController extends Controller
{
    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $deudas = [];
        $totalDeuda = 0.0;
        $error = null;

        try {
            $db = Database::connection();
            $stmt = $db->query("SELECT c.id, c.fecha_vencimiento, c.estado_cuota, c.monto, c.saldo_pendiente,
                    s.numero_socio, s.nombre_completo, s.rut,
                    p.nombre_periodo
                FROM cuotas c
                INNER JOIN socios s ON s.id = c.socio_id
                LEFT JOIN periodos p ON p.id = c.periodo_id
                WHERE c.deleted_at IS NULL
                  AND s.deleted_at IS NULL
                  AND c.estado_cuota IN ('pendiente', 'vencida', 'abonada_parcial')
                  AND c.saldo_pendiente > 0
                ORDER BY c.fecha_vencimiento ASC, s.nombre_completo ASC");
            $deudas = $stmt->fetchAll() ?: [];

            foreach ($deudas as $deuda) {
                $totalDeuda += (float) ($deuda['saldo_pendiente'] ?? 0);
            }
        } catch (Throwable) {
            $error = 'No fue posible cargar el listado de deuda acumulada.';
        }

        $this->view('deudas/index', [
            'title' => 'Deuda acumulada',
            'description' => 'Listado de cuotas pendientes, vencidas o abonadas parcialmente.',
            'deudas' => $deudas,
            'totalDeuda' => $totalDeuda,
            'error' => $error,
        ]);
    }
}
