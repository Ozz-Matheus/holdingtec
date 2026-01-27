<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Notificación de ' . config('app.name'))</title>
    @include('emails.layout.styles')
</head>
<body>
    <table class="body" role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table class="container" role="presentation" cellpadding="0" cellspacing="0">
                    <!-- Header -->
                    <tr>
                        <td class="header">
                            <img src="{{ asset('images/logo.jpg') }}"
                                 alt="Logo de {{ config('app.name') }}">
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td class="content">
                            <h1>@yield('title', 'Notificación de ' . config('app.name'))</h1>
                            
                            @yield('content')

                            <!-- Firma -->
                            <div class="signature">
                                <strong>Gracias,</strong>
                                <p>Equipo de {{ config('app.name') }}</p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="footer">
                            <p>
                                <strong>{{ config('app.name') }}</strong>
                            </p>
                            <p>
                                Este mensaje fue generado automáticamente por nuestro sistema.<br>
                                Por favor, no responda directamente a este correo.
                            </p>
                            <div class="footer-links">
                                <a href="{{ route('filament.dashboard.pages.dashboard') }}" target="_blank">
                                    Ir al Dashboard
                                </a>
                                <a href="mailto:soporte@holdingtec.app" target="_blank">
                                    Centro de Ayuda
                                </a>
                            </div>
                            
                            <p style="margin-top: 16px; font-size: 12px; color: #9ca3af;">
                                © {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>