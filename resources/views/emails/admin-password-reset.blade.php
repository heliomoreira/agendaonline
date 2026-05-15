<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de Password</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #696cff; padding: 32px 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 22px; letter-spacing: 0.5px; }
        .body { padding: 40px; color: #333333; }
        .body p { line-height: 1.7; margin: 0 0 16px; }
        .btn-wrapper { text-align: center; margin: 32px 0; }
        .btn { display: inline-block; background: #696cff; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 15px; font-weight: bold; }
        .expiry { font-size: 13px; color: #888888; text-align: center; margin-top: -16px; }
        .url-fallback { font-size: 12px; color: #aaaaaa; word-break: break-all; margin-top: 24px; border-top: 1px solid #eeeeee; padding-top: 16px; }
        .footer { background: #f9f9f9; padding: 20px 40px; text-align: center; font-size: 12px; color: #aaaaaa; border-top: 1px solid #eeeeee; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Agenda Online</h1>
    </div>
    <div class="body">
        <p>Olá,</p>
        <p>Recebemos um pedido para recuperar a password da sua conta de administrador. Clique no botão abaixo para definir uma nova password:</p>

        <div class="btn-wrapper">
            <a href="{{ $resetUrl }}" class="btn">Redefinir Password</a>
        </div>

        <p class="expiry">Este link expira em <strong>60 minutos</strong>.</p>

        <p>Se não foi você a solicitar a recuperação de password, pode ignorar este email em segurança — a sua password não será alterada.</p>

        <div class="url-fallback">
            <p>Se o botão não funcionar, copie e cole este endereço no seu browser:</p>
            <p>{{ $resetUrl }}</p>
        </div>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Agenda Online — Todos os direitos reservados.
    </div>
</div>
</body>
</html>
