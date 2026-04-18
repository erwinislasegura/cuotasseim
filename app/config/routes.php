<?php

declare(strict_types=1);

return [
    ['GET', '/', 'LandingController@index'],
    ['GET', '/login', 'AuthController@showLogin'],
    ['POST', '/login', 'AuthController@login'],
    ['POST', '/logout', 'AuthController@logout'],

    ['GET', '/panel', 'PanelController@index'],

    ['GET', '/socios', 'SociosController@index'],
    ['GET', '/tipos-socio', 'TiposSocioController@index'],
    ['GET', '/periodos', 'PeriodosController@index'],
    ['GET', '/conceptos-cobro', 'ConceptosCobroController@index'],

    ['GET', '/cuotas', 'CuotasController@index'],
    ['GET', '/pagos', 'PagosController@index'],
    ['GET', '/medios-pago', 'MediosPagoController@index'],

    ['GET', '/tipos-aporte', 'TiposAporteController@index'],
    ['GET', '/aportes', 'AportesController@index'],

    ['GET', '/tipos-egreso', 'TiposEgresoController@index'],
    ['GET', '/egresos', 'EgresosController@index'],
    ['GET', '/rendiciones', 'RendicionesController@index'],
    ['GET', '/tesoreria', 'TesoreriaController@index'],

    ['GET', '/roles', 'RolesController@index'],
    ['GET', '/usuarios', 'UsuariosController@index'],

    ['GET', '/reportes', 'ReportesController@index'],
    ['GET', '/configuracion', 'ConfiguracionController@index'],
    ['GET', '/auditoria', 'AuditoriaController@index'],
];
