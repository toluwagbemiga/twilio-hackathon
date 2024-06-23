@extends('layouts.app')

@section('content')
    <h1>Conversations</h1>
    @foreach ($conversations as $conversation)
        <div>
            <h2>Conversation with {{ $conversation->user->phone }} ({{ $conversation->type }})</h2>
            <a href="{{ route('conversations.show', $conversation->id) }}">View Conversation</a>
        </div>
    @endforeach
@endsection
