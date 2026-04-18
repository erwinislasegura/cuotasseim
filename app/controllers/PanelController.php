<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use Throwable;

class PanelController extends Controller
{
    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $kpis = [
            'socios_activos' => 0,
            'recaudado_mes' => 0,
            'adeudado_total' => 0,
            'saldo_estimado' => 0,
            'cuotas_pendientes' => 0,
            'morosidad_cantidad' => 0,
        ];

        $recentPayments = [];
        $recentExpenses = [];
        $alerts = [];
        $series = ['ingresos' => [], 'egresos' => []];

        try {
            $db = Database::connection();

            $kpis['socios_activos'] = (int) $db->query('SELECT COUNT(*) FROM socios WHERE deleted_at IS NULL AND activo = 1')->fetchColumn();
            $kpis['recaudado_mes'] = (float) $db->query("SELECT COALESCE(SUM(monto_total),0) FROM pagos WHERE estado_pago = 'aplicado' AND DATE_FORMAT(fecha_pago, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->fetchColumn();
            $kpis['adeudado_total'] = (float) $db->query("SELECT COALESCE(SUM(saldo_pendiente),0) FROM cuotas WHERE deleted_at IS NULL AND estado_cuota IN ('pendiente','vencida','abonada_parcial')")->fetchColumn();
            $kpis['saldo_estimado'] = $kpis['recaudado_mes'] - (float) $db->query("SELECT COALESCE(SUM(monto),0) FROM egresos WHERE deleted_at IS NULL AND estado = 'aplicado' AND DATE_FORMAT(fecha, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->fetchColumn();
            $kpis['cuotas_pendientes'] = (int) $db->query("SELECT COUNT(*) FROM cuotas WHERE deleted_at IS NULL AND estado_cuota IN ('pendiente','vencida','abonada_parcial')")->fetchColumn();
            $kpis['morosidad_cantidad'] = (int) $db->query("SELECT COUNT(*) FROM cuotas WHERE deleted_at IS NULL AND estado_cuota = 'vencida'")->fetchColumn();

            $recentPayments = $db->query("SELECT id, fecha_pago, monto_total, estado_pago, numero_comprobante FROM pagos WHERE deleted_at IS NULL ORDER BY fecha_pago DESC, id DESC LIMIT 6")->fetchAll();
            $recentExpenses = $db->query("SELECT id, fecha, descripcion, monto, estado FROM egresos WHERE deleted_at IS NULL ORDER BY fecha DESC, id DESC LIMIT 6")->fetchAll();

            $series['ingresos'] = $db->query("SELECT DATE_FORMAT(fecha_pago,'%Y-%m') AS mes, COALESCE(SUM(monto_total),0) AS total FROM pagos WHERE estado_pago='aplicado' AND fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH) GROUP BY DATE_FORMAT(fecha_pago,'%Y-%m') ORDER BY mes ASC")->fetchAll();
            $series['egresos'] = $db->query("SELECT DATE_FORMAT(fecha,'%Y-%m') AS mes, COALESCE(SUM(monto),0) AS total FROM egresos WHERE estado='aplicado' AND fecha >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH) GROUP BY DATE_FORMAT(fecha,'%Y-%m') ORDER BY mes ASC")->fetchAll();

            if ($kpis['morosidad_cantidad'] > 0) {
                $alerts[] = $kpis['morosidad_cantidad'] . ' cuotas están vencidas y requieren seguimiento.';
            }
            if ($kpis['saldo_estimado'] < 0) {
                $alerts[] = 'El saldo proyectado del mes está en negativo.';
            }
            if ($kpis['cuotas_pendientes'] > 100) {
                $alerts[] = 'Se superó el umbral de cuotas pendientes recomendado.';
            }
        } catch (Throwable) {
            $alerts[] = 'No se pudo cargar toda la información del dashboard. Revisa la conexión a base de datos.';
        }

        $this->view('panel/index', [
            'title' => 'Panel de control',
            'kpis' => $kpis,
            'recentPayments' => $recentPayments,
            'recentExpenses' => $recentExpenses,
            'dashboardAlerts' => $alerts,
            'series' => $series,
        ]);
    }
}
