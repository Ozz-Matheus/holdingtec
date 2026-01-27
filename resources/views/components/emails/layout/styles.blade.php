<style>
    /* Reset y Base */
    body {
        margin: 0;
        padding: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background: #f4f7fa;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    h1 {
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 24px 0;
        color: #1a202c;
        line-height: 1.3;
    }

    p {
        margin: 0 0 16px 0;
        line-height: 1.6;
    }

    /* Layout Principal */
    .body {
        width: 100%;
        padding: 40px 20px;
    }

    .container {
        max-width: 600px;
        width: 100%;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Header con gradiente */
    .header {
        background: #ffffff;
        padding: 32px 40px;
        text-align: center;
        position: relative;
        border-bottom: 3px solid #3575dd;
    }

    .header::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #60a5fa, #3b82f6, #2563eb);
    }

    .header img {
        max-height: 50px;
        width: auto;
    }

    /* Contenido */
    .content {
        padding: 20px;
        font-size: 15px;
        color: #374151;
        background: #ffffff;
    }

    .content h1 {
        color: #1a202c;
        border-bottom: 3px solid #3575dd;
        padding-bottom: 16px;
        margin-bottom: 28px;
        text-align: center;
        text-transform: uppercase;
    }

    /* Card de información */
    .info-card {
        background: #f8fafc;
        border-left: 4px solid #3575dd;
        border-radius: 8px;
        padding: 24px;
        margin: 24px 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    /* Lista de detalles */
    ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    ul li {
        padding: 12px 0;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: flex-start;
    }

    ul li:last-child {
        border-bottom: none;
    }

    ul li strong {
        color: #1f2937;
        font-weight: 600;
        min-width: 180px;
        display: inline-block;
        font-size: 14px;
    }

    ul li span {
        color: #4b5563;
        flex: 1;
    }

    /* Botón mejorado */
    a.button {
        display: inline-block;
        padding: 14px 32px;
        background: #3575dd;
        color: #ffffff !important;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        text-align: center;
        margin: 24px 0 8px 0;
        border: none;
    }

    /* Badge de estado */
    .status-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        margin-left: 8px;
    }

    /* Colores de estado - Filament */
    .status-badge.success {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge.danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-badge.warning {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.info,
    .status-badge.indigo {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-badge.gray {
        background: #f3f4f6;
        color: #374151;
    }

    /* Sección de firma */
    .signature {
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid #e5e7eb;
        color: #6b7280;
        font-size: 14px;
    }

    .signature strong {
        color: #1f2937;
        display: block;
        margin-bottom: 4px;
    }

    /* Footer mejorado */
    .footer {
        background: #f9fafb;
        text-align: center;
        padding: 28px 40px;
        font-size: 13px;
        color: #6b7280;
        border-top: 1px solid #e5e7eb;
    }

    .footer p {
        margin: 0 0 8px 0;
    }

    .footer-links {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }

    .footer-links a {
        color: #3575dd;
        text-decoration: none;
        margin: 0 12px;
        font-weight: 500;
    }

    /* Alert box */
    .alert {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        padding: 16px 20px;
        border-radius: 6px;
        margin: 20px 0;
        font-size: 14px;
        color: #92400e;
    }

    .alert strong {
        display: block;
        margin-bottom: 6px;
        color: #78350f;
    }

    /* Elementos de énfasis */
    .alert-box {
        display: flex;
        font-style: normal;
        background: #eff6ff;
        padding: 12px 16px;
        border-radius: 6px;
        color: #1e40af;
        font-size: 14px;
        margin: 16px 0;
    }

    .alert-box span{
        display: inline-block;
        margin-left: 6px;
        margin-right: 6px;
        line-height: 1.9;
    }

    /* Responsive */
    @media only screen and (max-width: 640px) {
        .body {
            padding: 20px 10px;
        }

        .container {
            border-radius: 8px;
        }

        .header {
            padding: 24px 20px;
        }

        .content {
            padding: 24px 20px;
        }

        h1 {
            font-size: 24px;
        }

        ul li {
            flex-direction: column;
            padding: 16px 0;
        }

        ul li strong {
            min-width: auto;
            margin-bottom: 6px;
        }

        a.button {
            display: block;
            width: 100%;
            box-sizing: border-box;
        }

        .footer {
            padding: 24px 20px;
        }
    }
</style>