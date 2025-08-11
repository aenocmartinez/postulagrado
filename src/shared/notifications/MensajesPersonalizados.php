<?php

namespace Src\Shared\Notifications;


class MensajesPersonalizados
{

    public static function generarHtmlEnlaceActualizacion(string $enlaceFormulario, string $nombreEstudiante = 'Estudiante'): string
    {
        return <<<HTML
            <!doctype html>
            <html lang="es">
            <head>
                <meta charset="utf-8">
                <title>Actualización de datos</title>
                <meta name="viewport" content="width=device-width,initial-scale=1">
            </head>
            <body style="margin:0;padding:0;background:#f6f7f9;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f6f7f9;padding:24px 0;">
                <tr>
                    <td align="center">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="width:600px;max-width:100%;background:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;">
                        <tr>
                        <td style="padding:24px 24px 8px 24px;background:#0ea5e9;color:#ffffff;font-family:Arial,Helvetica,sans-serif;">
                            <h1 style="margin:0;font-size:20px;line-height:1.3;">Proceso de grado</h1>
                        </td>
                        </tr>
                        <tr>
                        <td style="padding:24px;font-family:Arial,Helvetica,sans-serif;color:#111827;">
                            <p style="margin:0 0 12px 0;font-size:16px;line-height:1.5;">Estimado(a) {$nombreEstudiante},</p>
                            <p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;color:#374151;">
                            Para garantizar que su información personal esté actualizada en el marco del proceso de grado,
                            le invitamos a diligenciar el siguiente formulario.
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:16px 0 20px 0;">
                            <tr>
                                <td align="center" bgcolor="#16a34a" style="border-radius:6px;">
                                <a href="{$enlaceFormulario}" target="_blank"
                                    style="display:inline-block;padding:12px 18px;font-family:Arial,Helvetica,sans-serif;
                                            font-size:14px;color:#ffffff;text-decoration:none;border-radius:6px;">
                                    Acceder al formulario de actualización
                                </a>
                                </td>
                            </tr>
                            </table>

                            <p style="margin:0 0 16px 0;font-size:13px;line-height:1.6;color:#6b7280;">
                            Si el botón no funciona, copie y pegue este enlace en su navegador:<br>
                            <a href="{$enlaceFormulario}" target="_blank" style="color:#2563eb;word-break:break-all;">{$enlaceFormulario}</a>
                            </p>

                            <p style="margin:16px 0 0 0;font-size:14px;line-height:1.6;color:#374151;">
                            Agradecemos completar la información solicitada a la mayor brevedad.
                            </p>

                            <p style="margin:16px 0 0 0;font-size:14px;line-height:1.6;color:#374151;">
                            Atentamente,<br>
                            <strong>Universidad Colegio Mayor de Cundinamarca</strong>
                            </p>
                        </td>
                        </tr>
                        <tr>
                        <td style="padding:12px 24px 20px 24px;background:#f9fafb;color:#6b7280;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.5;">
                            Este mensaje fue enviado en el marco del proceso de grado. Si recibió este correo por error, puede ignorarlo.
                        </td>
                        </tr>
                    </table>
                    </td>
                </tr>
                </table>
            </body>
            </html>
            HTML;
    }
}