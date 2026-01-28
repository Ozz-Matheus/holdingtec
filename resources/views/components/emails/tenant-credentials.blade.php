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
    <li>
        <strong>Url de restablecimiento de contraseña :</strong>
        <a href="{{ $resetUrl }}" target="_blank" class="button" >Ir al Sistema</a>
    </li>
</ul>

@endsection
