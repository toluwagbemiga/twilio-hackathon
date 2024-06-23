
@extends('layouts.app')

@section('content')
    <h1>Conversation with {{ $conversation->user->phone }} ({{ $conversation->type }})</h1>
    <ul>
        @foreach ($conversation->messages as $message)
            <li><strong>{{ $message->sender }}:</strong> {{ $message->content }}</li>
        @endforeach
    </ul>
    
    <form action="{{ route('conversations.respond', $conversation->id) }}" method="POST">
        @csrf
        <textarea name="message" rows="4" cols="50"></textarea>
        <button type="submit">Send</button>
    </form>

    <form action="{{ route('conversations.close', $conversation->id) }}" method="POST">
        @csrf
        <button type="submit">Close Conversation</button>
    </form>
@endsection
