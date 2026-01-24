<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class ExistingTenantDatabase implements ValidationRule
{
    /**
     * Valida que la DB exista, no sea reservada y ESTÉ VACÍA.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // El mensaje único
        $errorMessage = "La base de datos '{$value}' no existe o no está autorizada. Verifique con soporte.";

        // 1. LISTA PRINCIPAL : Bases de datos del sistema y la Central
        $centralDb = config('database.connections.mysql.database');
        $systemSchemas = ['information_schema', 'performance_schema', 'mysql', 'sys', $centralDb];

        if (in_array($value, $systemSchemas)) {
            $fail($errorMessage);

            return;
        }

        // 2. EXISTENCIA FÍSICA
        $schemaExists = DB::selectOne(
            'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ? LIMIT 1',
            [$value]
        );

        if (! $schemaExists) {
            $fail($errorMessage);

            return;
        }

        // 3. VALIDACIÓN DE USO
        // Buscamos si existe al menos una tabla en ese esquema
        $hasTables = DB::selectOne(
            'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? LIMIT 1',
            [$value]
        );

        // Si devuelve un resultado, significa que NO está vacía. Bloqueamos.
        if ($hasTables) {
            $fail($errorMessage);
        }
    }
}
