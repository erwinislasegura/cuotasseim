<?php

declare(strict_types=1);

return [
    ['GET', '/', 'LandingController@index'],
    ['GET', '/login', 'AuthController@showLogin'],
    ['POST', '/login', 'AuthController@login'],
    ['POST', '/logout', 'AuthController@logout'],

    ['GET', '/panel', 'PanelController@index'],

    ['GET', '/socios', 'SociosController@index'],
    ['POST', '/socios', 'SociosController@index'],
    ['GET', '/tipos-socio', 'TiposSocioController@index'],
    ['POST', '/tipos-socio', 'TiposSocioController@index'],
    ['GET', '/periodos', 'PeriodosController@index'],
    ['POST', '/periodos', 'PeriodosController@index'],
    ['GET', '/conceptos-cobro', 'ConceptosCobroController@index'],
    ['POST', '/conceptos-cobro', 'ConceptosCobroController@index'],

    ['GET', '/cuotas', 'CuotasController@index'],
    ['POST', '/cuotas', 'CuotasController@index'],
    ['GET', '/pagos', 'PagosController@index'],
    ['POST', '/pagos', 'PagosController@index'],
    ['GET', '/medios-pago', 'MediosPagoController@index'],
    ['POST', '/medios-pago', 'MediosPagoController@index'],

    ['GET', '/tipos-aporte', 'TiposAporteController@index'],
    ['POST', '/tipos-aporte', 'TiposAporteController@index'],
    ['GET', '/aportes', 'AportesController@index'],
    ['POST', '/aportes', 'AportesController@index'],

    ['GET', '/tipos-egreso', 'TiposEgresoController@index'],
    ['POST', '/tipos-egreso', 'TiposEgresoController@index'],
    ['GET', '/egresos', 'EgresosController@index'],
    ['POST', '/egresos', 'EgresosController@index'],

    ['GET', '/roles', 'RolesController@index'],
    ['POST', '/roles', 'RolesController@index'],
    ['GET', '/usuarios', 'UsuariosController@index'],
    ['POST', '/usuarios', 'UsuariosController@index'],

    ['GET', '/reportes', 'ReportesController@index'],
    ['GET', '/configuracion', 'ConfiguracionController@index'],
    ['POST', '/configuracion', 'ConfiguracionController@index'],
    ['GET', '/auditoria', 'AuditoriaController@index'],
];
