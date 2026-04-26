<?php
$currentPath = trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
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
        ['route' => 'rendiciones', 'label' => 'Rendiciones', 'icon' => 'bi-journal-check'],
        ['route' => 'tesoreria', 'label' => 'Tesorería', 'icon' => 'bi-bank'],
    ],
    'Gestión' => [
        ['route' => 'reportes', 'label' => 'Reportes', 'icon' => 'bi-bar-chart'],
        ['route' => 'auditoria', 'label' => 'Auditoría', 'icon' => 'bi-shield-lock'],
        ['route' => 'usuarios', 'label' => 'Usuarios', 'icon' => 'bi-person-gear'],
        ['route' => 'roles', 'label' => 'Roles', 'icon' => 'bi-person-badge'],
        ['route' => 'configuracion', 'label' => 'Configuración', 'icon' => 'bi-sliders'],
    ],
    'Catálogos' => [
        ['route' => 'tipos-socio', 'label' => 'Tipos de socio', 'icon' => 'bi-diagram-3'],
        ['route' => 'medios-pago', 'label' => 'Medios de pago', 'icon' => 'bi-wallet2'],
        ['route' => 'tipos-aporte', 'label' => 'Tipos de aporte', 'icon' => 'bi-coin'],
        ['route' => 'tipos-egreso', 'label' => 'Tipos de egreso', 'icon' => 'bi-tags'],
        ['route' => 'conceptos-cobro', 'label' => 'Conceptos de cobro', 'icon' => 'bi-card-list'],
    ],
];
?>

<div class="sidebar-brand">
  <div class="sidebar-logo"><i class="bi bi-grid-1x2-fill"></i></div>
  <div>
    <h6 class="mb-0 text-white">Gestión de Cuotas</h6>
    <small class="text-white-50">Administración integral</small>
  </div>
</div>

<?php foreach ($menu as $group => $items): ?>
  <?php
  $groupId = 'sidebar-group-' . strtolower((string) preg_replace('/[^a-z0-9]+/i', '-', $group));
  $isGroupActive = false;

  foreach ($items as $menuItem) {
      $routeRoot = trim((string) $menuItem['route'], '/');
      if ($currentRoot === $routeRoot) {
          $isGroupActive = true;
          break;
      }
  }
  ?>
  <button
    class="sidebar-group-toggle <?= $isGroupActive ? 'is-open' : '' ?>"
    type="button"
    data-bs-toggle="collapse"
    data-bs-target="#<?= htmlspecialchars($groupId) ?>"
    aria-expanded="<?= $isGroupActive ? 'true' : 'false' ?>"
    aria-controls="<?= htmlspecialchars($groupId) ?>"
  >
    <span class="sidebar-label mb-0"><?= htmlspecialchars($group) ?></span>
    <i class="bi bi-chevron-down"></i>
  </button>
  <div class="collapse <?= $isGroupActive ? 'show' : '' ?>" id="<?= htmlspecialchars($groupId) ?>">
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
  </div>
<?php endforeach; ?>
