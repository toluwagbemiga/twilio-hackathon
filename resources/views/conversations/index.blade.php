@extends('layouts.app')

@section('title', 'Conversations')

@section('content')
<div class="container">
    <h1 class="my-4">Conversations</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>User</th>
                <th>Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($conversations as $conversation)
                <tr>
                    <td>{{ $conversation->user->name }}</td>
                    <td>{{ $conversation->type }}</td>
                    <td>{{ $conversation->is_closed ? 'Closed' : 'Open' }}</td>
                    <td>
                        <a href="{{ route('conversations.show', $conversation->id) }}" class="btn btn-info">View</a>
                        <form action="{{ route('conversations.close', $conversation->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">Close</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
