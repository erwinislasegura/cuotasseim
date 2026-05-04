<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use PDO;
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
            $periodoNombreExpr = $this->resolverColumnaNombrePeriodo($db);

            $sql = "SELECT c.id, c.fecha_vencimiento, c.estado_cuota, c.monto, c.saldo_pendiente,
                    s.numero_socio, s.nombre_completo, s.rut,
                    {$periodoNombreExpr} AS nombre_periodo
                FROM cuotas c
                INNER JOIN socios s ON s.id = c.socio_id
                LEFT JOIN periodos p ON p.id = c.periodo_id
                WHERE c.deleted_at IS NULL
                  AND s.deleted_at IS NULL
                  AND c.estado_cuota IN ('pendiente', 'vencida', 'abonada_parcial')
                  AND COALESCE(c.saldo_pendiente, 0) > 0
                ORDER BY c.fecha_vencimiento ASC, s.nombre_completo ASC";

            $stmt = $db->query($sql);
            $deudas = $stmt->fetchAll() ?: [];

            foreach ($deudas as $deuda) {
                $totalDeuda += (float) ($deuda['saldo_pendiente'] ?? 0);
            }
        } catch (Throwable $e) {
            $error = 'No fue posible cargar el listado de deuda acumulada.';
            error_log('DeudasController@index error: ' . $e->getMessage());
        }

        $this->view('deudas/index', [
            'title' => 'Deuda acumulada',
            'description' => 'Listado de cuotas pendientes, vencidas o abonadas parcialmente.',
            'deudas' => $deudas,
            'totalDeuda' => $totalDeuda,
            'error' => $error,
        ]);
    }

    private function resolverColumnaNombrePeriodo(PDO $db): string
    {
        try {
            $stmt = $db->query("SHOW COLUMNS FROM periodos LIKE 'nombre_periodo'");
            $hasNombrePeriodo = $stmt !== false && $stmt->fetch() !== false;
            if ($hasNombrePeriodo) {
                return 'p.nombre_periodo';
            }

            $stmt = $db->query("SHOW COLUMNS FROM periodos LIKE 'nombre'");
            $hasNombre = $stmt !== false && $stmt->fetch() !== false;
            if ($hasNombre) {
                return 'p.nombre';
            }
        } catch (Throwable) {
            // Fallback silencioso para no romper la vista.
        }

        return "CONCAT('Plan #', COALESCE(c.periodo_id, 0))";
    }
}
