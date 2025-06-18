<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Confirmação de Marcação</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .email-wrapper {
            width: 100%;
            padding: 30px 0;
            background-color: #f3f4f6;
        }

        .email-container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .email-header img {
            width: 100%;
            height: auto;
            display: block;
        }

        .email-body {
            padding: 30px;
        }

        h2 {
            color: #2c3e50;
            margin-top: 0;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .details-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #6b7280;
        }

        .footer strong {
            color: #111827;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background-color: #3b82f6;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="email-container">
        <!-- Top Image -->
        <div class="email-header">
            <img src="" width="60" alt="Agenda Online - Confirmação de Marcação">
        </div>

        <div class="email-body">
            <h2>Olá {{ $clientName }},</h2>

            <p>A sua marcação foi registada com sucesso! Seguem os detalhes:</p>

            <table class="details-table">
                <tr>
                    <td><strong>Serviço:</strong></td>
                    <td>{{ $service }}</td>
                </tr>
                <tr>
                    <td><strong>Profissional:</strong></td>
                    <td>{{ $professional }}</td>
                </tr>
                <tr>
                    <td><strong>Data:</strong></td>
                    <td>{{ $date }}</td>
                </tr>
                <tr>
                    <td><strong>Hora:</strong></td>
                    <td>{{ $time }}h</td>
                </tr>
            </table>

            <div class="footer">
                <p>Se tiver alguma dúvida, não hesite em contactar-nos.</p>
                <p>Obrigado,<br><strong>{{ config('app.name') }}</strong></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
