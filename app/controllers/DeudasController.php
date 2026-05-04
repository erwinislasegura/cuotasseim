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
            $where = [
                "c.estado_cuota IN ('pendiente', 'vencida', 'abonada_parcial')",
                'COALESCE(c.saldo_pendiente, 0) > 0',
            ];

            if ($this->columnaExiste($db, 'cuotas', 'deleted_at')) {
                $where[] = 'c.deleted_at IS NULL';
            }
            if ($this->columnaExiste($db, 'socios', 'deleted_at')) {
                $where[] = 's.deleted_at IS NULL';
            }

            $sql = "SELECT c.id, c.fecha_vencimiento, c.estado_cuota, c.monto_total, c.monto_pagado, c.saldo_pendiente,
                    COALESCE(s.numero_socio, '-') AS numero_socio,
                    COALESCE(s.nombre_completo, 'Socio sin nombre') AS nombre_completo,
                    COALESCE(s.rut, '-') AS rut,
                    {$periodoNombreExpr} AS nombre_periodo
                FROM cuotas c
                LEFT JOIN socios s ON s.id = c.socio_id
                LEFT JOIN periodos p ON p.id = c.periodo_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY c.fecha_vencimiento ASC, nombre_completo ASC";

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
        if ($this->columnaExiste($db, 'periodos', 'nombre_periodo')) {
            return 'COALESCE(p.nombre_periodo, CONCAT(\'Plan #\', COALESCE(c.periodo_id, 0)))';
        }

        if ($this->columnaExiste($db, 'periodos', 'nombre')) {
            return 'COALESCE(p.nombre, CONCAT(\'Plan #\', COALESCE(c.periodo_id, 0)))';
        }

        return "CONCAT('Plan #', COALESCE(c.periodo_id, 0))";
    }

    private function columnaExiste(PDO $db, string $tabla, string $columna): bool
    {
        try {
            $stmt = $db->prepare("SHOW COLUMNS FROM {$tabla} LIKE :columna");
            $stmt->bindValue(':columna', $columna);
            $stmt->execute();
            return $stmt->fetch() !== false;
        } catch (Throwable) {
            return false;
        }
    }
}
