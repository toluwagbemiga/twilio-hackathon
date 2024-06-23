<!-- resources/views/tickets/create.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Ticket</title>
</head>
<body>
    <h1>Create Ticket</h1>
    <form action="{{ route('tickets.store') }}" method="POST">
        @csrf
        <div>
            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required>
        </div>
        <div>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>
        </div>
        <button type="submit">Create</button>
    </form>
</body>
</html>
