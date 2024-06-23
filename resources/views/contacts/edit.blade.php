@extends('layouts.app')

@section('title', 'Edit Contact')

@section('content')
<div class="container">
    <h1 class="my-4">Edit Contact</h1>
    <form action="{{ route('contacts.update', $contact->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" id="name" value="{{ $contact->name }}" required>
        </div>
        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input type="text" name="phone_number" class="form-control" id="phone_number" value="{{ $contact->phone_number }}" required>
        </div>
        <button type="submit" class="btn btn-success">Update Contact</button>
    </form>
</div>
@endsection