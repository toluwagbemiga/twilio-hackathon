@extends('layouts.app')

@section('title', 'Conversation Details')

@section('content')
<div class="container">
    <h1 class="my-4">Conversation Details</h1>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ $conversation->user->name }}</h5>
            <h6 class="card-subtitle mb-2 text-muted">{{ $conversation->type }}</h6>
            @foreach ($conversation->messages as $message)
                <p>{{ $message->sender }}: {{ $message->content }}</p>
            @endforeach
            @if (!$conversation->is_closed)
                <form action="{{ route('conversations.respond', $conversation->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="response" class="form-label">Response</label>
                        <textarea name="response" class="form-control" id="response" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Send Response</button>
                </form>
            @endif
        </div>
    </div>
    <form action="{{ route('conversations.close', $conversation->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger">Close Conversation</button>
    </form>
</div>
@endsection
