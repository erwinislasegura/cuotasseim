<?php

declare(strict_types=1);

return [
    ['GET', '/', 'LandingController@index'],
    ['GET', '/login', 'AuthController@showLogin'],
    ['POST', '/login', 'AuthController@login'],
    ['POST', '/logout', 'AuthController@logout'],

    ['GET', '/panel', 'PanelController@index'],
    ['GET', '/socios', 'SociosController@index'],
    ['GET', '/cuotas', 'CuotasController@index'],
    ['GET', '/cuotas/generar', 'CuotasController@generar'],
    ['GET', '/pagos', 'PagosController@index'],
    ['GET', '/pagos/crear', 'PagosController@crear'],
    ['GET', '/egresos', 'EgresosController@index'],
    ['GET', '/tesoreria', 'TesoreriaController@index'],
    ['GET', '/reportes', 'ReportesController@index'],
    ['GET', '/configuracion', 'ConfiguracionController@index'],
    ['GET', '/auditoria', 'AuditoriaController@index'],
];
