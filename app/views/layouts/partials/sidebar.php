<?php
use App\Core\Database;

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

if ($scriptDir !== '' && $scriptDir !== '.' && str_starts_with($requestPath, $scriptDir)) {
    $requestPath = substr($requestPath, strlen($scriptDir)) ?: '/';
}

$frontController = '/' . ltrim(basename((string) ($_SERVER['SCRIPT_NAME'] ?? 'index.php')), '/');

if ($requestPath === $frontController) {
    $requestPath = '/';
} elseif (str_starts_with($requestPath, $frontController . '/')) {
    $requestPath = substr($requestPath, strlen($frontController)) ?: '/';
}

$currentPath = trim((string) $requestPath, '/');
$currentPath = $currentPath === '' ? 'panel' : $currentPath;
$currentRoot = explode('/', $currentPath)[0] ?? 'panel';

$menu = [
    'Operación' => [
        ['route' => 'panel', 'label' => 'Panel', 'icon' => 'bi-speedometer2'],
        ['route' => 'socios', 'label' => 'Socios', 'icon' => 'bi-people'],
        ['route' => 'periodos', 'label' => 'Planes', 'icon' => 'bi-calendar3'],
        ['route' => 'cuotas', 'label' => 'Registro de cuotas', 'icon' => 'bi-receipt'],
        ['route' => 'pagos', 'label' => 'Historial de pagos', 'icon' => 'bi-credit-card-2-front'],
        ['route' => 'aportes', 'label' => 'Aportes', 'icon' => 'bi-piggy-bank'],
        ['route' => 'egresos', 'label' => 'Egresos', 'icon' => 'bi-cash-stack'],
    ],
    'Gestión' => [
        ['route' => 'reportes', 'label' => 'Reportes', 'icon' => 'bi-bar-chart'],
        ['route' => 'usuarios', 'label' => 'Usuarios', 'icon' => 'bi-person-gear'],
        ['route' => 'roles', 'label' => 'Roles', 'icon' => 'bi-person-badge'],
        ['route' => 'configuracion', 'label' => 'Configuración', 'icon' => 'bi-sliders'],
    ],
    'Catálogos' => [
        ['route' => 'tipos-socio', 'label' => 'Tipos de socio', 'icon' => 'bi-diagram-3'],
        ['route' => 'medios-pago', 'label' => 'Medios de pago', 'icon' => 'bi-wallet2'],
        ['route' => 'tipos-aporte', 'label' => 'Tipos de aporte', 'icon' => 'bi-coin'],
        ['route' => 'tipos-egreso', 'label' => 'Tipos de egreso', 'icon' => 'bi-tags'],
    ],
];

$logoPath = '';
try {
    if (Database::connection() !== null) {
        $stmtLogo = Database::connection()->query('SELECT logo FROM configuracion ORDER BY id DESC LIMIT 1');
        $logoPath = trim((string) ($stmtLogo->fetchColumn() ?: ''));
    }
} catch (\Throwable $exception) {
    $logoPath = '';
}
?>

<div class="sidebar-brand">
  <div class="sidebar-logo">
    <?php if ($logoPath !== ''): ?>
      <img src="<?= htmlspecialchars(url($logoPath)) ?>" alt="Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
    <?php else: ?>
      <i class="bi bi-grid-1x2-fill"></i>
    <?php endif; ?>
  </div>
  <div>
    <h6 class="mb-0 text-white">Gestión de Cuotas</h6>
    <small class="text-white-50">Administración integral</small>
  </div>
</div>

<?php foreach ($menu as $group => $items): ?>
  <section class="sidebar-group">
    <p class="sidebar-label"><?= htmlspecialchars($group) ?></p>
    <ul class="nav flex-column sidebar-nav mb-2">
      <?php foreach ($items as $item): ?>
        <?php
        $itemRoute = trim((string) $item['route'], '/');
        $isActive = $currentRoot === $itemRoute;
        $classes = 'nav-link' . ($isActive ? ' is-active' : '');
        ?>
        <li class="nav-item">
          <a class="<?= htmlspecialchars($classes) ?>" href="<?= htmlspecialchars(url((string) $item['route'])) ?>" <?= $isActive ? 'aria-current="page"' : '' ?>>
            <i class="bi <?= htmlspecialchars((string) $item['icon']) ?>"></i>
            <span><?= htmlspecialchars((string) $item['label']) ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
<?php endforeach; ?>
