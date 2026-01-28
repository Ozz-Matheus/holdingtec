<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración del Super Admin
    |--------------------------------------------------------------------------
    |
    | Credenciales predeterminadas para el usuario maestro en cada tenant.
    |
    */
    'super_admin' => [
        'email' => env('SUPER_ADMIN_EMAIL', 'admin@holdingtec.app'),
        'password' => env('SUPER_ADMIN_PASSWORD', 'password'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración General del QMS
    |--------------------------------------------------------------------------
    |
    | Aquí podemos agregar futuros ajustes globales del sistema.
    |
    */

];
