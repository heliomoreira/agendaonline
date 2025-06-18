<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Confirmação de Marcação</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }
        h2 {
            color: #2c3e50;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .details-table td {
            padding: 10px;
            background-color: #f9f9f9;
            border-bottom: 1px solid #ddd;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Olá {{ $clientName }},</h2>

    <p>A sua marcação foi registada com sucesso. Aqui estão os detalhes:</p>

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

    <p class="footer">
        Se tiver alguma questão, não hesite em contactar-nos.<br><br>
        Obrigado,<br>
        <strong>{{ config('app.name') }}</strong>
    </p>
</div>
</body>
</html>
