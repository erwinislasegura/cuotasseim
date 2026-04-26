<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Session;
use Throwable;

class CuotasController extends Controller
{
    public function index(): void
    {
        Session::start();

        $q = trim((string) ($_GET['q'] ?? $_POST['q'] ?? ''));
        $selectedSocioId = max(0, (int) ($_GET['socio_id'] ?? $_POST['socio_id'] ?? 0));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarRegistroPago($q, $selectedSocioId);
            return;
        }

        $socios = [];
        $socio = null;
        $cuotaPorVencer = null;
        $otrasCuotas = [];
        $mediosPago = [];
        $sinPlanAsociado = false;
        $error = null;

        try {
            $db = Database::connection();
            $socios = $this->buscarSocios($db, $q);
            $mediosPago = $this->obtenerMediosPago($db);

            if ($selectedSocioId <= 0 && $q !== '' && count($socios) === 1) {
                $selectedSocioId = (int) ($socios[0]['id'] ?? 0);
            }

            if ($selectedSocioId > 0) {
                $socio = $this->obtenerSocio($db, $selectedSocioId);

                if ($socio !== null) {
                    $cuotas = $this->obtenerCuotasOrdenadas($db, $selectedSocioId);
                    $cuotasPendientes = array_values(array_filter(
                        $cuotas,
                        static fn(array $cuota): bool => in_array((string) ($cuota['estado_cuota'] ?? ''), ['pendiente', 'vencida', 'abonada_parcial'], true)
                    ));

                    if (!empty($cuotasPendientes)) {
                        $cuotaPorVencer = $cuotasPendientes[0];
                        $otrasCuotas = array_slice($cuotasPendientes, 1);
                    } else {
                        $cuotaPorVencer = $this->obtenerCuotaActualDesdePlan($db, $selectedSocioId);
                        $sinPlanAsociado = $cuotaPorVencer === null;
                    }
                }
            }
        } catch (Throwable) {
            $error = 'No fue posible cargar el registro de cuotas. Revisa la conexión a la base de datos.';
        }

        $flashSuccess = $_SESSION['flash_success'] ?? null;
        $flashError = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $this->view('cuotas/registro', [
            'title' => 'Registro de cuotas',
            'description' => 'Busca al socio por nombre o RUT, revisa la cuota actual y registra su pago.',
            'q' => $q,
            'socios' => $socios,
            'selectedSocioId' => $selectedSocioId,
            'socio' => $socio,
            'cuotaPorVencer' => $cuotaPorVencer,
            'otrasCuotas' => $otrasCuotas,
            'mediosPago' => $mediosPago,
            'sinPlanAsociado' => $sinPlanAsociado,
            'token' => Csrf::token(),
            'flashSuccess' => $flashSuccess,
            'flashError' => $flashError,
            'error' => $error,
        ]);
    }

    private function procesarRegistroPago(string $q, int $selectedSocioId): void
    {
        if (!Csrf::validate($_POST['_token'] ?? null)) {
            $_SESSION['flash_error'] = 'Token CSRF inválido.';
            $this->redirect('/cuotas?q=' . urlencode($q) . '&socio_id=' . $selectedSocioId);
        }

        $socioId = max(0, (int) ($_POST['socio_id'] ?? 0));
        $cuotaId = max(0, (int) ($_POST['cuota_id'] ?? 0));
        $medioPagoId = max(0, (int) ($_POST['medio_pago_id'] ?? 0));
        $fechaPago = trim((string) ($_POST['fecha_pago'] ?? ''));
        $monto = (float) ($_POST['monto_pago'] ?? 0);

        if ($socioId <= 0 || $medioPagoId <= 0 || $fechaPago === '' || $monto <= 0) {
            $_SESSION['flash_error'] = 'Completa socio, medio de pago, fecha y monto válido.';
            $this->redirect('/cuotas?q=' . urlencode($q) . '&socio_id=' . $socioId);
        }

        try {
            $db = Database::connection();

            if ($cuotaId <= 0) {
                $cuotaId = $this->crearCuotaDesdePlanActual($db, $socioId);
                if ($cuotaId <= 0) {
                    throw new \RuntimeException('No se pudo determinar una cuota para registrar el pago.');
                }
            }

            $this->registrarPagoCuota($db, $socioId, $cuotaId, $medioPagoId, $fechaPago, $monto);
            $_SESSION['flash_success'] = 'Pago registrado correctamente.';
        } catch (Throwable $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        $this->redirect('/cuotas?q=' . urlencode($q) . '&socio_id=' . $socioId);
    }

    /** @return array<int,array<string,mixed>> */
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
              AND (nombre_completo LIKE :term OR rut LIKE :term OR CONCAT(COALESCE(nombres, ''), ' ', COALESCE(apellidos, '')) LIKE :term)
            ORDER BY nombre_completo ASC
            LIMIT 50");
        $stmt->bindValue(':term', '%' . $q . '%');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** @return array<int,array<string,mixed>> */
    private function obtenerMediosPago(\PDO $db): array
    {
        $stmt = $db->query('SELECT id, nombre FROM medios_pago WHERE activo = 1 ORDER BY nombre ASC');
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

    /** @return array<int,array<string,mixed>> */
    private function obtenerCuotasOrdenadas(\PDO $db, int $socioId): array
    {
        $stmt = $db->prepare("SELECT c.id,c.fecha_vencimiento,c.estado_cuota,c.monto_total,c.monto_pagado,c.saldo_pendiente,c.observacion,
                COALESCE(p.nombre_periodo, CONCAT('Plan #', c.periodo_id)) AS nombre_periodo,
                COALESCE(p.tipo_periodo, 'mensual') AS tipo_periodo,
                COALESCE(cc.nombre, 'Cuota') AS concepto,
                CASE
                    WHEN p.tipo_periodo = 'mensual' AND DATE_FORMAT(c.fecha_vencimiento, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m') THEN 1
                    WHEN p.tipo_periodo = 'trimestral' AND YEAR(c.fecha_vencimiento)=YEAR(CURDATE()) AND QUARTER(c.fecha_vencimiento)=QUARTER(CURDATE()) THEN 1
                    WHEN p.tipo_periodo = 'semestral' AND YEAR(c.fecha_vencimiento)=YEAR(CURDATE())
                         AND (CASE WHEN MONTH(c.fecha_vencimiento)<=6 THEN 1 ELSE 2 END)=(CASE WHEN MONTH(CURDATE())<=6 THEN 1 ELSE 2 END) THEN 1
                    WHEN p.tipo_periodo = 'anual' AND YEAR(c.fecha_vencimiento)=YEAR(CURDATE()) THEN 1
                    ELSE 0
                END AS es_periodo_actual
            FROM cuotas c
            LEFT JOIN periodos p ON p.id = c.periodo_id
            LEFT JOIN conceptos_cobro cc ON cc.id = c.concepto_cobro_id
            WHERE c.socio_id = :socio_id
              AND c.deleted_at IS NULL
              AND c.estado_cuota <> 'anulada'
            ORDER BY es_periodo_actual DESC,
                     CASE WHEN c.estado_cuota IN ('pendiente','vencida','abonada_parcial') THEN 1 ELSE 2 END ASC,
                     ABS(DATEDIFF(c.fecha_vencimiento, CURDATE())) ASC,
                     c.fecha_vencimiento ASC,
                     c.id ASC");
        $stmt->bindValue(':socio_id', $socioId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function registrarPagoCuota(\PDO $db, int $socioId, int $cuotaId, int $medioPagoId, string $fechaPago, float $monto): void
    {
        $stmtCuota = $db->prepare("SELECT
                c.id,
                c.estado_cuota,
                c.saldo_pendiente,
                c.monto_pagado,
                c.fecha_vencimiento,
                p.tipo_periodo,
                p.anio,
                p.mes,
                p.fecha_inicio
            FROM cuotas c
            LEFT JOIN periodos p ON p.id = c.periodo_id
            WHERE c.id = :id
              AND c.socio_id = :socio_id
              AND c.deleted_at IS NULL
            LIMIT 1
            FOR UPDATE");
        $db->beginTransaction();
        try {
            $stmtCuota->bindValue(':id', $cuotaId, \PDO::PARAM_INT);
            $stmtCuota->bindValue(':socio_id', $socioId, \PDO::PARAM_INT);
            $stmtCuota->execute();
            $cuota = $stmtCuota->fetch();

            if (!$cuota) {
                throw new \RuntimeException('La cuota seleccionada no existe para el socio.');
            }

            $saldoPendiente = (float) ($cuota['saldo_pendiente'] ?? 0);
            if ($saldoPendiente <= 0) {
                throw new \RuntimeException('La cuota ya está pagada, no tiene saldo pendiente.');
            }
            if ($monto > $saldoPendiente) {
                throw new \RuntimeException('El monto no puede superar el saldo pendiente de la cuota.');
            }

            $stmtMedio = $db->prepare('SELECT id FROM medios_pago WHERE id = :id AND activo = 1 LIMIT 1');
            $stmtMedio->bindValue(':id', $medioPagoId, \PDO::PARAM_INT);
            $stmtMedio->execute();
            if (!$stmtMedio->fetch()) {
                throw new \RuntimeException('El medio de pago seleccionado no está disponible.');
            }

            $usuario = Auth::user();
            $usuarioId = (int) ($usuario['id'] ?? $_SESSION['user_id'] ?? 1);
            $numeroComprobante = 'CUO-' . date('Ymd-His') . '-' . random_int(100, 999);
            $periodoAPagar = $this->formatearPeriodoAPagar($cuota);

            $stmtPago = $db->prepare("INSERT INTO pagos (socio_id, fecha_pago, monto_total, medio_pago_id, numero_comprobante, observacion, periodo_a_pagar, estado_pago, usuario_id)
                VALUES (:socio_id, :fecha_pago, :monto_total, :medio_pago_id, :numero_comprobante, :observacion, :periodo_a_pagar, 'aplicado', :usuario_id)");
            $stmtPago->bindValue(':socio_id', $socioId, \PDO::PARAM_INT);
            $stmtPago->bindValue(':fecha_pago', $fechaPago);
            $stmtPago->bindValue(':monto_total', $monto);
            $stmtPago->bindValue(':medio_pago_id', $medioPagoId, \PDO::PARAM_INT);
            $stmtPago->bindValue(':numero_comprobante', $numeroComprobante);
            $stmtPago->bindValue(':observacion', 'Pago registrado desde Registro de cuotas');
            $stmtPago->bindValue(':periodo_a_pagar', $periodoAPagar);
            $stmtPago->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
            $stmtPago->execute();

            $pagoId = (int) $db->lastInsertId();

            $stmtDetalle = $db->prepare('INSERT INTO pago_detalle (pago_id, cuota_id, monto_aplicado) VALUES (:pago_id, :cuota_id, :monto_aplicado)');
            $stmtDetalle->bindValue(':pago_id', $pagoId, \PDO::PARAM_INT);
            $stmtDetalle->bindValue(':cuota_id', $cuotaId, \PDO::PARAM_INT);
            $stmtDetalle->bindValue(':monto_aplicado', $monto);
            $stmtDetalle->execute();

            $nuevoPagado = (float) ($cuota['monto_pagado'] ?? 0) + $monto;
            $nuevoSaldo = max(0, $saldoPendiente - $monto);
            $nuevoEstado = $nuevoSaldo <= 0 ? 'pagada' : 'abonada_parcial';

            $stmtUpdate = $db->prepare('UPDATE cuotas SET monto_pagado = :monto_pagado, saldo_pendiente = :saldo_pendiente, estado_cuota = :estado_cuota WHERE id = :id');
            $stmtUpdate->bindValue(':monto_pagado', $nuevoPagado);
            $stmtUpdate->bindValue(':saldo_pendiente', $nuevoSaldo);
            $stmtUpdate->bindValue(':estado_cuota', $nuevoEstado);
            $stmtUpdate->bindValue(':id', $cuotaId, \PDO::PARAM_INT);
            $stmtUpdate->execute();

            $db->commit();
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    private function crearCuotaDesdePlanActual(\PDO $db, int $socioId): int
    {
        $stmt = $db->prepare("SELECT sp.periodo_id, p.nombre_periodo, p.tipo_periodo, p.monto_a_pagar
            FROM socio_planes sp
            INNER JOIN periodos p ON p.id = sp.periodo_id
            WHERE sp.socio_id = :socio_id
            ORDER BY sp.id DESC
            LIMIT 1");
        $stmt->bindValue(':socio_id', $socioId, \PDO::PARAM_INT);
        $stmt->execute();
        $plan = $stmt->fetch();

        if (!$plan) {
            return 0;
        }

        $monto = (float) ($plan['monto_a_pagar'] ?? 0);
        $tipo = (string) ($plan['tipo_periodo'] ?? 'mensual');
        $periodoId = (int) ($plan['periodo_id'] ?? 0);
        $fechaVencimiento = $this->obtenerFechaVencimientoProgresiva($db, $socioId, $periodoId, $tipo);

        $stmtExiste = $db->prepare("SELECT id FROM cuotas
            WHERE socio_id = :socio_id
              AND periodo_id = :periodo_id
              AND fecha_vencimiento = :fecha_vencimiento
              AND deleted_at IS NULL
              AND estado_cuota IN ('pendiente','vencida','abonada_parcial')
            LIMIT 1");
        $stmtExiste->bindValue(':socio_id', $socioId, \PDO::PARAM_INT);
        $stmtExiste->bindValue(':periodo_id', (int) $plan['periodo_id'], \PDO::PARAM_INT);
        $stmtExiste->bindValue(':fecha_vencimiento', $fechaVencimiento);
        $stmtExiste->execute();
        $existente = $stmtExiste->fetch();
        if ($existente) {
            return (int) ($existente['id'] ?? 0);
        }

        $stmtInsert = $db->prepare("INSERT INTO cuotas
            (socio_id, periodo_id, concepto_cobro_id, monto_base, saldo_arrastre, monto_total, monto_pagado, saldo_pendiente, estado_cuota, fecha_vencimiento, observacion, generada_automaticamente)
            VALUES (:socio_id, :periodo_id, NULL, :monto_base, 0, :monto_total, 0, :saldo_pendiente, 'pendiente', :fecha_vencimiento, :observacion, 1)");
        $stmtInsert->bindValue(':socio_id', $socioId, \PDO::PARAM_INT);
        $stmtInsert->bindValue(':periodo_id', (int) $plan['periodo_id'], \PDO::PARAM_INT);
        $stmtInsert->bindValue(':monto_base', $monto);
        $stmtInsert->bindValue(':monto_total', $monto);
        $stmtInsert->bindValue(':saldo_pendiente', $monto);
        $stmtInsert->bindValue(':fecha_vencimiento', $fechaVencimiento);
        $stmtInsert->bindValue(':observacion', 'Generada automáticamente desde Registro de cuotas');
        $stmtInsert->execute();

        return (int) $db->lastInsertId();
    }

    private function formatearPeriodoAPagar(array $cuota): string
    {
        $tipoPeriodo = trim((string) ($cuota['tipo_periodo'] ?? 'mensual'));
        $fechaBaseTexto = (string) ($cuota['fecha_vencimiento'] ?? $cuota['fecha_inicio'] ?? 'now');
        $mesBase = (int) date('n', strtotime($fechaBaseTexto));
        if ($mesBase < 1 || $mesBase > 12) {
            $mesBase = (int) date('n', strtotime((string) ($cuota['fecha_inicio'] ?? $cuota['fecha_vencimiento'] ?? 'now')));
        }
        $anioBase = (int) date('Y', strtotime($fechaBaseTexto));
        if ($anioBase <= 0) {
            $anioBase = (int) date('Y', strtotime((string) ($cuota['fecha_inicio'] ?? $cuota['fecha_vencimiento'] ?? 'now')));
        }

        $meses = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
            7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
        ];
        $trimestres = [1 => 'uno', 2 => 'dos', 3 => 'tres', 4 => 'cuatro'];
        $semestres = [1 => 'uno', 2 => 'dos'];

        if ($tipoPeriodo === 'trimestral') {
            $trimestre = (int) ceil(max(1, min(12, $mesBase)) / 3);
            return 'Trimestre ' . ($trimestres[$trimestre] ?? (string) $trimestre) . ' ' . $anioBase;
        }
        if ($tipoPeriodo === 'semestral') {
            $semestre = $mesBase <= 6 ? 1 : 2;
            return 'Semestre ' . ($semestres[$semestre] ?? (string) $semestre) . ' ' . $anioBase;
        }
        if ($tipoPeriodo === 'anual') {
            return 'Año ' . $anioBase;
        }

        return 'Mes ' . ($meses[$mesBase] ?? (string) $mesBase) . ' ' . $anioBase;
    }

    private function obtenerCuotaActualDesdePlan(\PDO $db, int $socioId): ?array
    {
        $stmt = $db->prepare("SELECT p.id AS periodo_id, p.nombre_periodo, p.tipo_periodo, p.monto_a_pagar
            FROM socio_planes sp
            INNER JOIN periodos p ON p.id = sp.periodo_id
            WHERE sp.socio_id = :socio_id
            ORDER BY sp.id DESC
            LIMIT 1");
        $stmt->bindValue(':socio_id', $socioId, \PDO::PARAM_INT);

        try {
            $stmt->execute();
            $plan = $stmt->fetch();
        } catch (Throwable) {
            return null;
        }

        if (!$plan) {
            return null;
        }

        $tipo = (string) ($plan['tipo_periodo'] ?? 'mensual');
        $monto = (float) ($plan['monto_a_pagar'] ?? 0);

        $fechaVencimiento = $this->obtenerFechaVencimientoProgresiva(
            $db,
            $socioId,
            (int) ($plan['periodo_id'] ?? 0),
            $tipo
        );

        return [
            'id' => null,
            'fecha_vencimiento' => $fechaVencimiento,
            'estado_cuota' => 'pendiente',
            'monto_total' => $monto,
            'monto_pagado' => 0,
            'saldo_pendiente' => $monto,
            'observacion' => 'Cuota referencial del plan actual (aún no generada en cuotas).',
            'nombre_periodo' => (string) ($plan['nombre_periodo'] ?? 'Plan actual'),
            'tipo_periodo' => $tipo,
            'concepto' => 'Cuota actual',
            'es_referencia_plan' => 1,
        ];
    }

    private function fechaVencimientoPeriodoActual(string $tipoPeriodo): string
    {
        $year = (int) date('Y');
        $month = (int) date('n');

        if ($tipoPeriodo === 'trimestral') {
            $quarterEndMonth = (int) (ceil($month / 3) * 3);
            return date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $year, $quarterEndMonth)));
        }

        if ($tipoPeriodo === 'semestral') {
            return $month <= 6 ? sprintf('%04d-06-30', $year) : sprintf('%04d-12-31', $year);
        }

        if ($tipoPeriodo === 'anual') {
            return sprintf('%04d-12-31', $year);
        }

        return date('Y-m-t');
    }

    private function obtenerFechaVencimientoProgresiva(\PDO $db, int $socioId, int $periodoId, string $tipoPeriodo): string
    {
        if ($socioId <= 0 || $periodoId <= 0) {
            return $this->fechaVencimientoPeriodoActual($tipoPeriodo);
        }

        $stmtSocio = $db->prepare('SELECT fecha_ingreso FROM socios WHERE id = :id LIMIT 1');
        $stmtSocio->bindValue(':id', $socioId, \PDO::PARAM_INT);
        $stmtSocio->execute();
        $fechaIngreso = (string) ($stmtSocio->fetchColumn() ?: '');

        $base = $fechaIngreso !== '' ? new \DateTimeImmutable($fechaIngreso) : new \DateTimeImmutable('today');
        $base = $base->modify('first day of this month');

        $stmtCount = $db->prepare("SELECT COUNT(*)
            FROM cuotas
            WHERE socio_id = :socio_id
              AND periodo_id = :periodo_id
              AND deleted_at IS NULL
              AND estado_cuota <> 'anulada'");
        $stmtCount->bindValue(':socio_id', $socioId, \PDO::PARAM_INT);
        $stmtCount->bindValue(':periodo_id', $periodoId, \PDO::PARAM_INT);
        $stmtCount->execute();
        $cuotasRegistradas = (int) ($stmtCount->fetchColumn() ?: 0);

        $saltoMeses = 1;
        if ($tipoPeriodo === 'trimestral') {
            $saltoMeses = 3;
        } elseif ($tipoPeriodo === 'semestral') {
            $saltoMeses = 6;
        } elseif ($tipoPeriodo === 'anual') {
            $saltoMeses = 12;
        }

        $inicioPeriodo = $base->modify('+' . ($cuotasRegistradas * $saltoMeses) . ' months');
        $finPeriodo = $inicioPeriodo->modify('+' . ($saltoMeses - 1) . ' months')->modify('last day of this month');

        return $finPeriodo->format('Y-m-d');
    }

    public function crear(): void
    {
        echo 'Cuotas: formulario crear.';
    }
}
