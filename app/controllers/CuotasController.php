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
        $q = trim((string) ($_GET['q'] ?? ''));
        $selectedSocioId = max(0, (int) ($_GET['socio_id'] ?? 0));

        $socios = [];
        $socio = null;
        $cuotaPorVencer = null;
        $otrasCuotas = [];
        $error = null;

        try {
            $db = Database::connection();

            $socios = $this->buscarSocios($db, $q);

            if ($selectedSocioId <= 0 && $q !== '' && count($socios) === 1) {
                $selectedSocioId = (int) ($socios[0]['id'] ?? 0);
            }

            if ($selectedSocioId > 0) {
                $socio = $this->obtenerSocio($db, $selectedSocioId);

                if ($socio !== null) {
                    $cuotas = $this->obtenerCuotasOrdenadas($db, $selectedSocioId);
                    if (!empty($cuotas)) {
                        $cuotaPorVencer = $cuotas[0];
                    }

                    $otrasCuotas = array_values(array_filter(
                        array_slice($cuotas, 1),
                        static fn(array $cuota): bool => in_array((string) ($cuota['estado_cuota'] ?? ''), ['pendiente', 'vencida', 'abonada_parcial'], true)
                    ));
                }
            }
        } catch (Throwable) {
            $error = 'No fue posible cargar el registro de cuotas. Revisa la conexión a la base de datos.';
        }

        $this->view('cuotas/registro', [
            'title' => 'Registro de cuotas',
            'description' => 'Busca al socio por nombre o RUT para visualizar su cuota del periodo actual y cuotas pendientes.',
            'q' => $q,
            'socios' => $socios,
            'selectedSocioId' => $selectedSocioId,
            'socio' => $socio,
            'cuotaPorVencer' => $cuotaPorVencer,
            'otrasCuotas' => $otrasCuotas,
            'error' => $error,
        ]);
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function buscarSocios(\PDO $db, string $q): array
    {
        if ($q === '') {
            $stmt = $db->query("SELECT id, numero_socio, nombre_completo, rut, correo, telefono FROM socios WHERE deleted_at IS NULL AND activo = 1 ORDER BY nombre_completo ASC LIMIT 30");
            return $stmt->fetchAll();
        }

        $stmt = $db->prepare("SELECT id, numero_socio, nombre_completo, rut, correo, telefono
            FROM socios
            WHERE deleted_at IS NULL
              AND activo = 1
              AND (
                nombre_completo LIKE :term
                OR rut LIKE :term
                OR CONCAT(COALESCE(nombres, ''), ' ', COALESCE(apellidos, '')) LIKE :term
              )
            ORDER BY nombre_completo ASC
            LIMIT 50");
        $stmt->bindValue(':term', '%' . $q . '%');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function obtenerSocio(\PDO $db, int $socioId): ?array
    {
        $stmtSocio = $db->prepare('SELECT id, numero_socio, nombre_completo, rut, correo, telefono, direccion, comuna, ciudad, fecha_ingreso FROM socios WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $stmtSocio->bindValue(':id', $socioId, \PDO::PARAM_INT);
        $stmtSocio->execute();

        $row = $stmtSocio->fetch();

        return $row ?: null;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function obtenerCuotasOrdenadas(\PDO $db, int $socioId): array
    {
        $stmt = $db->prepare("SELECT c.id,
                c.fecha_vencimiento,
                c.estado_cuota,
                c.monto_total,
                c.monto_pagado,
                c.saldo_pendiente,
                c.observacion,
                COALESCE(p.nombre_periodo, CONCAT('Plan #', c.periodo_id)) AS nombre_periodo,
                COALESCE(p.tipo_periodo, 'mensual') AS tipo_periodo,
                COALESCE(cc.nombre, 'Cuota') AS concepto,
                CASE
                    WHEN p.tipo_periodo = 'mensual' AND DATE_FORMAT(c.fecha_vencimiento, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m') THEN 1
                    WHEN p.tipo_periodo = 'trimestral' AND YEAR(c.fecha_vencimiento) = YEAR(CURDATE())
                        AND QUARTER(c.fecha_vencimiento) = QUARTER(CURDATE()) THEN 1
                    WHEN p.tipo_periodo = 'semestral' AND YEAR(c.fecha_vencimiento) = YEAR(CURDATE())
                        AND (CASE WHEN MONTH(c.fecha_vencimiento) <= 6 THEN 1 ELSE 2 END) = (CASE WHEN MONTH(CURDATE()) <= 6 THEN 1 ELSE 2 END) THEN 1
                    WHEN p.tipo_periodo = 'anual' AND YEAR(c.fecha_vencimiento) = YEAR(CURDATE()) THEN 1
                    ELSE 0
                END AS es_periodo_actual,
                CASE
                    WHEN c.estado_cuota IN ('pendiente', 'vencida', 'abonada_parcial') THEN 1
                    WHEN c.estado_cuota = 'pagada' THEN 2
                    WHEN c.estado_cuota = 'exenta' THEN 3
                    ELSE 4
                END AS prioridad_estado
            FROM cuotas c
            LEFT JOIN periodos p ON p.id = c.periodo_id
            LEFT JOIN conceptos_cobro cc ON cc.id = c.concepto_cobro_id
            WHERE c.socio_id = :socio_id
              AND c.deleted_at IS NULL
              AND c.estado_cuota <> 'anulada'
            ORDER BY es_periodo_actual DESC, prioridad_estado ASC, ABS(DATEDIFF(c.fecha_vencimiento, CURDATE())) ASC, c.fecha_vencimiento ASC, c.id ASC");
        $stmt->bindValue(':socio_id', $socioId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function crear(): void
    {
        echo 'Cuotas: formulario crear.';
    }
}
