<!-- resources/views/contacts/create.blade.php or edit.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($contact) ? 'Edit' : 'Add' }} Contact</title>
</head>
<body>
    <h1>{{ isset($contact) ? 'Edit' : 'Add' }} Contact</h1>
    <form action="{{ isset($contact) ? route('contacts.update', $contact->id) : route('contacts.store') }}" method="POST">
        @csrf
        @if (isset($contact))
            @method('PUT')
        @endif
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="{{ $contact->name ?? '' }}" required>
        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" value="{{ $contact->phone_number ?? '' }}" required>
        <button type="submit">{{ isset($contact) ? 'Update' : 'Add' }} Contact</button>
    </form>
</body>
</html>
