<!-- resources/views/tickets/show.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details</title>
</head>
<body>
    <h1>{{ $ticket->subject }}</h1>
    <p>{{ $ticket->description }}</p>
    <a href="{{ route('tickets.edit', $ticket->id) }}">Edit</a>
    <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit">Delete</button>
    </form>
</body>
</html>
