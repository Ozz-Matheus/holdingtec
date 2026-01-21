<?php

return [
    "group" => "Configuraciones",
    "title" => "Tenants",
    "single" => "Tenant",
    "columns" => [
        "id" => "ID",
        "name" => "Nombre",
        "unique_id" => "ID Único",
        "domain" => "Dominio",
        "email" => "Correo Electrónico",
        "phone" => "Teléfono",
        "password" => "Contraseña",
        "passwordConfirmation" => "Confirmar Contraseña",
        "is_active" => "Está Activo",
        "created_at" => "Creado En",
        "updated_at" => "Actualizado En",
    ],
    "actions" => [
        "view" => "Abrir Tenant",
        "login" => "Iniciar Sesión en Tenant",
        "password" => "Cambiar Contraseña",
        "edit" => "Editar",
        "delete" => "Eliminar",
    ],
    "domains" => [
        "title" => "Dominios",
        "single" => "Dominio",
        "columns" => [
            "domain" => "Dominio",
            "full" => "Dominio Completo",
        ],
    ]
];
