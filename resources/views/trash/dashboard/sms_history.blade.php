<!-- resources/views/dashboard/sms_history.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS History</title>
</head>
<body>
    <h1>SMS History</h1>
    <table>
        <thead>
            <tr>
                <th>Date Sent</th>
                <th>SMS Content</th>
                <th>Recipients</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($smsHistory as $history)
                <tr>
                    <td>{{ $history->created_at }}</td>
                    <td>{{ $history->sms_content }}</td>
                    <td>
                        @foreach (json_decode($history->recipients, true) as $recipient)
                            <p>{{ $recipient['name'] }} ({{ $recipient['phone_number'] }})</p>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
