<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use Throwable;

class CuotasController extends Controller
{
    public function index(): void
    {
        $socios = [];
        $selectedSocioId = max(0, (int) ($_GET['socio_id'] ?? 0));
        $socio = null;
        $cuotaPorVencer = null;
        $otrasCuotas = [];
        $error = null;

        try {
            $db = Database::connection();

            $socios = $db->query("SELECT id, numero_socio, nombre_completo, rut, correo, telefono FROM socios WHERE deleted_at IS NULL AND activo = 1 ORDER BY nombre_completo ASC")
                ->fetchAll();

            if ($selectedSocioId > 0) {
                $stmtSocio = $db->prepare('SELECT id, numero_socio, nombre_completo, rut, correo, telefono, direccion, comuna, ciudad, fecha_ingreso FROM socios WHERE id = :id AND deleted_at IS NULL LIMIT 1');
                $stmtSocio->bindValue(':id', $selectedSocioId, \PDO::PARAM_INT);
                $stmtSocio->execute();
                $socio = $stmtSocio->fetch() ?: null;

                if ($socio !== null) {
                    $stmtCuota = $db->prepare("SELECT c.id, c.fecha_vencimiento, c.estado_cuota, c.monto_total, c.monto_pagado, c.saldo_pendiente, c.observacion,
                            COALESCE(p.nombre_periodo, CONCAT('Plan #', c.periodo_id)) AS nombre_periodo,
                            COALESCE(cc.nombre, 'Cuota') AS concepto
                        FROM cuotas c
                        LEFT JOIN periodos p ON p.id = c.periodo_id
                        LEFT JOIN conceptos_cobro cc ON cc.id = c.concepto_cobro_id
                        WHERE c.socio_id = :socio_id
                          AND c.deleted_at IS NULL
                          AND c.estado_cuota IN ('pendiente', 'vencida', 'abonada_parcial')
                        ORDER BY c.fecha_vencimiento ASC, c.id ASC");
                    $stmtCuota->bindValue(':socio_id', $selectedSocioId, \PDO::PARAM_INT);
                    $stmtCuota->execute();
                    $cuotasPendientes = $stmtCuota->fetchAll();

                    if (!empty($cuotasPendientes)) {
                        $cuotaPorVencer = $cuotasPendientes[0];
                        $otrasCuotas = array_slice($cuotasPendientes, 1);
                    }
                }
            }
        } catch (Throwable) {
            $error = 'No fue posible cargar el registro de cuotas. Revisa la conexión a la base de datos.';
        }

        $this->view('cuotas/registro', [
            'title' => 'Registro de cuotas',
            'description' => 'Selecciona un socio para visualizar su próxima cuota por vencer y otras cuotas pendientes.',
            'socios' => $socios,
            'selectedSocioId' => $selectedSocioId,
            'socio' => $socio,
            'cuotaPorVencer' => $cuotaPorVencer,
            'otrasCuotas' => $otrasCuotas,
            'error' => $error,
        ]);
    }

    public function crear(): void
    {
        echo 'Cuotas: formulario crear.';
    }
}
