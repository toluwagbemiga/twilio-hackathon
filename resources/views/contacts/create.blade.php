@extends('layouts.app')

@section('title', 'Add Contact')

@section('content')
<div class="container">
    <h1 class="my-4">Add Contact</h1>
    <form action="{{ route('contacts.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" id="name" required>
        </div>
        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input type="text" name="phone_number" class="form-control" id="phone_number" required>
        </div>
        <button type="submit" class="btn btn-success">Add Contact</button>
    </form>
</div>
@endsection
