<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista urządzeń KIS.ME</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <h2>Lista urządzeń KIS.ME</h2>

    @if(empty($devices))
        <p>Brak urządzeń do wyświetlenia.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nazwa</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($devices as $device)
                    <tr>
                        <td>{{ $device['id'] }}</td>
                        <td>{{ $device['name'] ?? 'Brak nazwy' }}</td>
                        <td>{{ $device['status'] ?? 'Nieznany' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
