<!-- resources/views/tickets/index.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets</title>
</head>
<body>
    <h1>Support Tickets</h1>
    <a href="{{ route('tickets.create') }}">Create New Ticket</a>
    <ul>
        @foreach ($tickets as $ticket)
            <li>
                <a href="{{ route('tickets.show', $ticket->id) }}">{{ $ticket->subject }}</a>
            </li>
        @endforeach
    </ul>
</body>
</html>
