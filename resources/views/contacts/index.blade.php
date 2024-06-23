@extends('layouts.app')

@section('title', 'Contacts')

@section('content')
<div class="container">
    <h1 class="my-4">Contacts</h1>
    <a href="{{ route('contacts.create') }}" class="btn btn-primary mb-3">Add Contact</a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Phone Number</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($contacts as $contact)
                <tr>
                    <td>{{ $contact->name }}</td>
                    <td>{{ $contact->phone_number }}</td>
                    <td>
                        <a href="{{ route('contacts.edit', $contact->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
