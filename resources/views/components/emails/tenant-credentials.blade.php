@extends('emails.layout.theme')

@section('title')
Credenciales del Tenant
@endsection

@section('content')
<p>Hola {{ $tenantName }},</p>

<p>Se ha generado un acceso </p>
<p>en el sistema {{ config('app.name') }} :</p>

<ul>
    <li><strong>Credenciales del Tenant :</strong> {{ $tenantName }}</li>
    <li><strong>Contraseña :</strong> {{ $superAdminPass }}</li>
</ul>

@endsection
