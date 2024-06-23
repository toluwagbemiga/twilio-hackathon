<!-- resources/views/tickets/edit.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ticket</title>
</head>
<body>
    <h1>Edit Ticket</h1>
    <form action="{{ route('tickets.update', $ticket->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" value="{{ $ticket->subject }}" required>
        </div>
        <div>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required>{{ $ticket->description }}</textarea>
        </div>
        <button type="submit">Update</button>
    </form>
</body>
</html>
