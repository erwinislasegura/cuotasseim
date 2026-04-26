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

        $amount = (float) ($result['total_pendiente'] ?? 0);
        if ($amount <= 0) {
            $_SESSION['flow_error'] = 'El monto a pagar no es válido.';
            $this->redirect('/pago-flow?rut=' . urlencode($rut));
        }

        $socio = $result['socio'];
        $commerceOrder = 'SOCIO-' . (int) ($socio['id'] ?? 0) . '-' . date('YmdHis');
        $subject = 'Pago cuotas socio ' . trim((string) ($socio['nombre_completo'] ?? ''));
        $apiResponse = $this->requestFlowCreatePayment(
            $flowConfig,
            [
                'commerceOrder' => $commerceOrder,
                'subject' => mb_substr($subject, 0, 140),
                'currency' => 'CLP',
                'amount' => (string) (int) round($amount),
                'email' => (string) ($socio['correo'] ?? ''),
                'urlConfirmation' => $flowConfig['confirmation_url'],
                'urlReturn' => $flowConfig['return_url'],
                'optional' => json_encode([
                    'socio_id' => (int) ($socio['id'] ?? 0),
                    'rut' => (string) ($socio['rut'] ?? ''),
                ], JSON_UNESCAPED_UNICODE),
            ]
        );

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

        $stmtCuotas = Database::connection()->prepare('SELECT c.id, c.estado_cuota, c.saldo_pendiente, c.fecha_vencimiento, COALESCE(cc.nombre, "Cuota") AS concepto, COALESCE(p.nombre_periodo, CONCAT(COALESCE(p.anio, ""), "-", LPAD(COALESCE(p.mes, 0), 2, "0"))) AS periodo FROM cuotas c LEFT JOIN conceptos_cobro cc ON cc.id = c.concepto_cobro_id LEFT JOIN periodos p ON p.id = c.periodo_id WHERE c.socio_id = :socio_id AND c.estado_cuota IN ("pendiente", "vencida", "abonada_parcial") AND COALESCE(c.saldo_pendiente, 0) > 0 ORDER BY c.fecha_vencimiento ASC, c.id ASC');
        $stmtCuotas->bindValue(':socio_id', (int) $socio['id'], \PDO::PARAM_INT);
        $stmtCuotas->execute();
        $cuotas = $stmtCuotas->fetchAll() ?: [];

        $total = 0.0;
        foreach ($cuotas as $row) {
            $total += (float) ($row['saldo_pendiente'] ?? 0);
        }

        return [
            'socio' => $socio,
            'cuotas_pendientes' => $cuotas,
            'total_pendiente' => $total,
        ];
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

        $enabled = (int) ($row['flow_checkout_activo'] ?? 0) === 1;
        $apiKey = trim((string) ($row['flow_api_key'] ?? ''));
        $secretKey = trim((string) ($row['flow_secret_key'] ?? ''));
        $sandbox = (int) ($row['flow_modo_sandbox'] ?? 1) === 1;

        $defaultConfirmation = url('pago-flow');
        $defaultReturn = url('pago-flow');

        return [
            'enabled' => $enabled && $apiKey !== '' && $secretKey !== '',
            'api_key' => $apiKey,
            'secret_key' => $secretKey,
            'sandbox' => $sandbox,
            'base_url' => $sandbox ? 'https://sandbox.flow.cl/api' : 'https://www.flow.cl/api',
            'confirmation_url' => trim((string) ($row['flow_url_confirmacion'] ?? '')) ?: $defaultConfirmation,
            'return_url' => trim((string) ($row['flow_url_retorno'] ?? '')) ?: $defaultReturn,
        ];
    }

    private function requestFlowCreatePayment(array $flowConfig, array $payload): array
    {
        $params = array_merge([
            'apiKey' => $flowConfig['api_key'],
        ], $payload);

        ksort($params);
        $toSign = http_build_query($params);
        $signature = hash_hmac('sha256', $toSign, (string) $flowConfig['secret_key']);
        $params['s'] = $signature;

        $ch = curl_init((string) ($flowConfig['base_url'] . '/payment/create'));
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
            $message = (string) ($decoded['message'] ?? 'Error al crear pago en Flow.');
            return ['ok' => false, 'message' => $message, 'data' => $decoded];
        }

        return ['ok' => true, 'message' => 'ok', 'data' => $decoded];
    }
}
