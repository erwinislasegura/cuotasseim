<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Database;

class FlowCheckoutController extends Controller
{
    public function index(): void
    {
        $rutInput = trim((string) ($_GET['rut'] ?? ''));
        $result = $rutInput !== '' ? $this->findSocioDataByRut($rutInput) : null;

        $this->view('flow/public_checkout', [
            'title' => 'Pago en línea',
            'token' => Csrf::token(),
            'rut' => $rutInput,
            'result' => $result,
            'flowEnabled' => $this->isFlowEnabled(),
            'flashError' => $_SESSION['flow_error'] ?? null,
        ], 'landing');

        unset($_SESSION['flow_error']);
    }

    public function createPayment(): void
    {
        if (!Csrf::validate($_POST['_token'] ?? null)) {
            $_SESSION['flow_error'] = 'Token CSRF inválido.';
            $this->redirect('/pago-flow');
        }

        $rut = trim((string) ($_POST['rut'] ?? ''));
        $result = $this->findSocioDataByRut($rut);

        if ($result === null || empty($result['cuotas_pendientes'])) {
            $_SESSION['flow_error'] = 'No hay cuotas pendientes para este RUT.';
            $this->redirect('/pago-flow?rut=' . urlencode($rut));
        }

        $flowConfig = $this->flowConfig();
        if (!$flowConfig['enabled']) {
            $_SESSION['flow_error'] = 'Flow no está habilitado en la configuración del sistema.';
            $this->redirect('/pago-flow?rut=' . urlencode($rut));
        }

        $selectedIds = array_map('intval', (array) ($_POST['cuotas_ids'] ?? []));
        $selectedIds = array_values(array_filter(array_unique($selectedIds), static fn(int $id): bool => $id > 0));
        if (empty($selectedIds)) {
            $_SESSION['flow_error'] = 'Debes seleccionar al menos una cuota para pagar.';
            $this->redirect('/pago-flow?rut=' . urlencode($rut));
        }

        $cuotasPendientes = $this->pendingCuotasBySocioId((int) ($result['socio']['id'] ?? 0));
        $allowedById = [];
        foreach ($cuotasPendientes as $cuota) {
            $allowedById[(int) ($cuota['id'] ?? 0)] = $cuota;
        }

        $selectedCuotas = [];
        $amount = 0.0;
        foreach ($selectedIds as $cuotaId) {
            if (!isset($allowedById[$cuotaId])) {
                $_SESSION['flow_error'] = 'Una de las cuotas seleccionadas no está disponible para pago.';
                $this->redirect('/pago-flow?rut=' . urlencode($rut));
            }
            $selectedCuotas[] = $allowedById[$cuotaId];
            $amount += (float) ($allowedById[$cuotaId]['saldo_pendiente'] ?? 0);
        }

        if ($amount <= 0) {
            $_SESSION['flow_error'] = 'El monto seleccionado no es válido.';
            $this->redirect('/pago-flow?rut=' . urlencode($rut));
        }

        $socio = $result['socio'];
        $commerceOrder = 'SOCIO-' . (int) ($socio['id'] ?? 0) . '-' . date('YmdHis');
        $subject = 'Pago cuotas socio ' . trim((string) ($socio['nombre_completo'] ?? ''));

        $optionalPayload = [
            'socio_id' => (int) ($socio['id'] ?? 0),
            'rut' => (string) ($socio['rut'] ?? ''),
            'cuotas_ids' => array_values(array_map(static fn(array $item): int => (int) ($item['id'] ?? 0), $selectedCuotas)),
        ];

        $apiResponse = $this->flowRequest('/payment/create', [
            'apiKey' => $flowConfig['api_key'],
            'commerceOrder' => $commerceOrder,
            'subject' => mb_substr($subject, 0, 140),
            'currency' => 'CLP',
            'amount' => (string) (int) round($amount),
            'email' => (string) ($socio['correo'] ?? ''),
            'urlConfirmation' => url('pago-flow/retorno'),
            'urlReturn' => url('pago-flow/retorno'),
            'optional' => json_encode($optionalPayload, JSON_UNESCAPED_UNICODE),
        ], $flowConfig);

        if (!$apiResponse['ok']) {
            $_SESSION['flow_error'] = $apiResponse['message'];
            $this->redirect('/pago-flow?rut=' . urlencode($rut));
        }

        $paymentUrl = (string) ($apiResponse['data']['url'] ?? '');
        $token = (string) ($apiResponse['data']['token'] ?? '');

        if ($paymentUrl === '' || $token === '') {
            $_SESSION['flow_error'] = 'Flow no devolvió una URL de pago válida.';
            $this->redirect('/pago-flow?rut=' . urlencode($rut));
        }

        $separator = str_contains($paymentUrl, '?') ? '&' : '?';
        $this->redirect($paymentUrl . $separator . 'token=' . urlencode($token));
    }

    public function retorno(): void
    {
        $token = trim((string) ($_GET['token'] ?? ''));
        $status = trim((string) ($_GET['status'] ?? ''));
        $flowConfig = $this->flowConfig();

        $data = null;
        if ($token !== '' && $flowConfig['enabled']) {
            $response = $this->flowRequest('/payment/getStatus', [
                'apiKey' => $flowConfig['api_key'],
                'token' => $token,
            ], $flowConfig);

            if ($response['ok']) {
                $data = $response['data'];
                $status = (string) ($data['status'] ?? $status);
            }
        }

        $isAccepted = $status === '2';

        if ($isAccepted && is_array($data)) {
            $this->registrarPagoAprobadoFlow($token, $data);
        }

        $view = $isAccepted ? 'flow/accepted' : 'flow/rejected';

        $this->view($view, [
            'title' => $isAccepted ? 'Pago aceptado' : 'Pago rechazado',
            'status' => $status,
            'payment' => is_array($data) ? $data : [],
        ], 'landing');
    }

    public function rejected(): void
    {
        $this->view('flow/rejected', [
            'title' => 'Pago rechazado',
            'status' => (string) ($_GET['status'] ?? ''),
            'payment' => [],
        ], 'landing');
    }

    private function findSocioDataByRut(string $rutInput): ?array
    {
        $normalizedRut = $this->normalizeRut($rutInput);
        if ($normalizedRut === '') {
            return null;
        }

        $stmtSocio = Database::connection()->prepare('SELECT id, numero_socio, nombre_completo, rut, correo, telefono FROM socios WHERE REPLACE(REPLACE(UPPER(rut), ".", ""), "-", "") = :rut LIMIT 1');
        $stmtSocio->bindValue(':rut', $normalizedRut);
        $stmtSocio->execute();
        $socio = $stmtSocio->fetch();

        if (!$socio) {
            return null;
        }

        $cuotas = $this->pendingCuotasBySocioId((int) ($socio['id'] ?? 0));

        $total = 0.0;
        foreach ($cuotas as $row) {
            $total += (float) ($row['saldo_pendiente'] ?? 0);
        }

        $periodosPendientes = [];
        foreach ($cuotas as $row) {
            $periodo = trim((string) ($row['periodo'] ?? ''));
            if ($periodo !== '' && !in_array($periodo, $periodosPendientes, true)) {
                $periodosPendientes[] = $periodo;
            }
        }

        return [
            'socio' => $socio,
            'cuotas_pendientes' => $cuotas,
            'periodos_pendientes' => $periodosPendientes,
            'total_pendiente' => $total,
        ];
    }

    private function pendingCuotasBySocioId(int $socioId): array
    {
        if ($socioId <= 0) {
            return [];
        }

        $stmtCuotas = Database::connection()->prepare('SELECT c.id, c.estado_cuota, c.saldo_pendiente, c.fecha_vencimiento, COALESCE(cc.nombre, "Cuota") AS concepto, COALESCE(p.nombre_periodo, CONCAT(COALESCE(p.anio, ""), "-", LPAD(COALESCE(p.mes, 0), 2, "0"))) AS periodo FROM cuotas c LEFT JOIN conceptos_cobro cc ON cc.id = c.concepto_cobro_id LEFT JOIN periodos p ON p.id = c.periodo_id WHERE c.socio_id = :socio_id AND c.estado_cuota IN ("pendiente", "vencida", "abonada_parcial") AND COALESCE(c.saldo_pendiente, 0) > 0 ORDER BY c.fecha_vencimiento ASC, c.id ASC');
        $stmtCuotas->bindValue(':socio_id', $socioId, \PDO::PARAM_INT);
        $stmtCuotas->execute();

        return $stmtCuotas->fetchAll() ?: [];
    }

    private function normalizeRut(string $rut): string
    {
        return strtoupper(str_replace(['.', '-'], '', trim($rut)));
    }

    private function isFlowEnabled(): bool
    {
        $config = $this->flowConfig();
        return $config['enabled'];
    }

    private function flowConfig(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM configuracion ORDER BY id ASC LIMIT 1');
        $row = $stmt->fetch() ?: [];

        $apiKey = trim((string) ($row['flow_api_key'] ?? ''));
        $secretKey = trim((string) ($row['flow_secret_key'] ?? ''));
        $sandbox = (int) ($row['flow_modo_sandbox'] ?? 1) === 1;

        return [
            'enabled' => $apiKey !== '' && $secretKey !== '',
            'api_key' => $apiKey,
            'secret_key' => $secretKey,
            'base_url' => $sandbox ? 'https://sandbox.flow.cl/api' : 'https://www.flow.cl/api',
        ];
    }


    private function registrarPagoAprobadoFlow(string $token, array $flowData): void
    {
        if ($token === '') {
            return;
        }

        $db = Database::connection();
        $stmtExiste = $db->prepare('SELECT id FROM pagos WHERE referencia_externa = :ref LIMIT 1');
        $stmtExiste->bindValue(':ref', $token);
        $stmtExiste->execute();
        if ($stmtExiste->fetch()) {
            return;
        }

        $optional = json_decode((string) ($flowData['optional'] ?? ''), true);
        if (!is_array($optional)) {
            return;
        }

        $socioId = (int) ($optional['socio_id'] ?? 0);
        $cuotasIds = array_values(array_filter(array_map('intval', (array) ($optional['cuotas_ids'] ?? [])), static fn(int $id): bool => $id > 0));
        $amount = (float) ($flowData['amount'] ?? 0);

        if ($socioId <= 0 || empty($cuotasIds) || $amount <= 0) {
            return;
        }

        $db->beginTransaction();
        try {
            $medioPagoId = $this->obtenerMedioPagoFlowId($db);

            $placeholders = implode(',', array_fill(0, count($cuotasIds), '?'));
            $sqlCuotas = "SELECT c.id, c.saldo_pendiente, c.monto_pagado, c.periodo_id, p.tipo_periodo, p.anio, p.mes, p.fecha_inicio
                FROM cuotas c
                LEFT JOIN periodos p ON p.id = c.periodo_id
                WHERE c.socio_id = ? AND c.id IN ({$placeholders}) AND c.deleted_at IS NULL
                FOR UPDATE";
            $stmtCuotas = $db->prepare($sqlCuotas);
            $stmtCuotas->bindValue(1, $socioId, \PDO::PARAM_INT);
            foreach ($cuotasIds as $idx => $cuotaId) {
                $stmtCuotas->bindValue($idx + 2, $cuotaId, \PDO::PARAM_INT);
            }
            $stmtCuotas->execute();
            $cuotas = $stmtCuotas->fetchAll() ?: [];
            if (empty($cuotas)) {
                $db->rollBack();
                return;
            }

            $cuotasById = [];
            foreach ($cuotas as $cuota) {
                $cuotasById[(int) ($cuota['id'] ?? 0)] = $cuota;
            }

            $periodos = [];
            foreach ($cuotasIds as $cuotaId) {
                if (!isset($cuotasById[$cuotaId])) {
                    continue;
                }
                $periodos[] = $this->formatearPeriodoAPagar((array) $cuotasById[$cuotaId]);
            }
            $periodoAPagar = implode(', ', array_values(array_unique(array_filter($periodos))));

            $numeroComprobante = 'FLOW-' . date('Ymd-His') . '-' . random_int(100, 999);
            $stmtPago = $db->prepare("INSERT INTO pagos (socio_id, fecha_pago, monto_total, medio_pago_id, numero_comprobante, referencia_externa, observacion, periodo_a_pagar, estado_pago, usuario_id)
                VALUES (:socio_id, :fecha_pago, :monto_total, :medio_pago_id, :numero_comprobante, :referencia_externa, :observacion, :periodo_a_pagar, 'aplicado', :usuario_id)");
            $stmtPago->bindValue(':socio_id', $socioId, \PDO::PARAM_INT);
            $stmtPago->bindValue(':fecha_pago', date('Y-m-d'));
            $stmtPago->bindValue(':monto_total', $amount);
            $stmtPago->bindValue(':medio_pago_id', $medioPagoId, \PDO::PARAM_INT);
            $stmtPago->bindValue(':numero_comprobante', $numeroComprobante);
            $stmtPago->bindValue(':referencia_externa', $token);
            $stmtPago->bindValue(':observacion', 'Pago aprobado por Flow. Orden ' . (string) ($flowData['commerceOrder'] ?? ''));
            $stmtPago->bindValue(':periodo_a_pagar', $periodoAPagar !== '' ? $periodoAPagar : null);
            $stmtPago->bindValue(':usuario_id', 1, \PDO::PARAM_INT);
            $stmtPago->execute();

            $pagoId = (int) $db->lastInsertId();
            $stmtDetalle = $db->prepare('INSERT INTO pago_detalle (pago_id, cuota_id, monto_aplicado) VALUES (:pago_id, :cuota_id, :monto_aplicado)');
            $stmtUpdate = $db->prepare('UPDATE cuotas SET monto_pagado = :monto_pagado, saldo_pendiente = :saldo_pendiente, estado_cuota = :estado_cuota WHERE id = :id');

            $remaining = $amount;
            foreach ($cuotasIds as $cuotaId) {
                if (!isset($cuotasById[$cuotaId])) {
                    continue;
                }
                $cuota = $cuotasById[$cuotaId];
                $saldoPendiente = (float) ($cuota['saldo_pendiente'] ?? 0);
                if ($saldoPendiente <= 0 || $remaining <= 0) {
                    continue;
                }

                $abono = min($saldoPendiente, $remaining);
                $remaining -= $abono;

                $stmtDetalle->bindValue(':pago_id', $pagoId, \PDO::PARAM_INT);
                $stmtDetalle->bindValue(':cuota_id', $cuotaId, \PDO::PARAM_INT);
                $stmtDetalle->bindValue(':monto_aplicado', $abono);
                $stmtDetalle->execute();

                $nuevoPagado = (float) ($cuota['monto_pagado'] ?? 0) + $abono;
                $nuevoSaldo = max(0, $saldoPendiente - $abono);
                $estado = $nuevoSaldo <= 0 ? 'pagada' : 'abonada_parcial';

                $stmtUpdate->bindValue(':monto_pagado', $nuevoPagado);
                $stmtUpdate->bindValue(':saldo_pendiente', $nuevoSaldo);
                $stmtUpdate->bindValue(':estado_cuota', $estado);
                $stmtUpdate->bindValue(':id', $cuotaId, \PDO::PARAM_INT);
                $stmtUpdate->execute();
            }

            $db->commit();
        } catch (\Throwable) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
        }
    }

    private function obtenerMedioPagoFlowId(\PDO $db): int
    {
        $stmt = $db->prepare("SELECT id FROM medios_pago WHERE LOWER(nombre) = 'flow' LIMIT 1");
        $stmt->execute();
        $id = (int) ($stmt->fetchColumn() ?: 0);
        if ($id > 0) {
            return $id;
        }

        $insert = $db->prepare('INSERT INTO medios_pago (nombre, activo) VALUES (:nombre, 1)');
        $insert->bindValue(':nombre', 'Flow');
        $insert->execute();

        return (int) $db->lastInsertId();
    }

    private function formatearPeriodoAPagar(array $cuota): string
    {
        $tipoPeriodo = trim((string) ($cuota['tipo_periodo'] ?? 'mensual'));
        $mesBase = (int) ($cuota['mes'] ?? 0);
        if ($mesBase < 1 || $mesBase > 12) {
            $mesBase = (int) date('n');
        }

        $anioBase = (int) ($cuota['anio'] ?? 0);
        if ($anioBase <= 0) {
            $anioBase = (int) date('Y');
        }

        $mapMes = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        return match ($tipoPeriodo) {
            'trimestral' => 'Trimestre ' . (int) ceil($mesBase / 3) . ' ' . $anioBase,
            'semestral' => 'Semestre ' . ($mesBase <= 6 ? 'I' : 'II') . ' ' . $anioBase,
            'anual' => 'Año ' . $anioBase,
            default => (($mapMes[$mesBase] ?? 'Mes') . ' ' . $anioBase),
        };
    }

    private function flowRequest(string $path, array $params, array $flowConfig): array
    {
        $signatureData = $params;
        unset($signatureData['s']);
        ksort($signatureData);
        $toSign = http_build_query($signatureData);
        $params['s'] = hash_hmac('sha256', $toSign, (string) $flowConfig['secret_key']);

        $ch = curl_init((string) ($flowConfig['base_url'] . $path));
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT => 20,
        ]);

        $raw = curl_exec($ch);
        $curlError = curl_error($ch);
        $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false || $curlError !== '') {
            return ['ok' => false, 'message' => 'No fue posible conectar con Flow: ' . $curlError];
        }

        $decoded = json_decode((string) $raw, true);
        if (!is_array($decoded)) {
            return ['ok' => false, 'message' => 'Respuesta inválida de Flow.'];
        }

        if ($statusCode >= 400 || isset($decoded['code'])) {
            $message = (string) ($decoded['message'] ?? 'Error al procesar solicitud con Flow.');
            return ['ok' => false, 'message' => $message, 'data' => $decoded];
        }

        return ['ok' => true, 'message' => 'ok', 'data' => $decoded];
    }
}
