@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container">
    <h1 class="my-4">Admin Dashboard</h1>
    <h2 class="my-4">Call Conversations</h2>
    @foreach ($callConversations as $conversation)
        <div class="card my-3">
            <div class="card-body">
                <h5 class="card-title">{{ $conversation->user->name }} - {{ $conversation->type }}</h5>
                <p class="card-text">{{ $conversation->messages->pluck('content')->implode(', ') }}</p>
                <form action="{{ route('admin.conversation.close', $conversation->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Close Conversation</button>
                </form>
            </div>
        </div>
    @endforeach

    <h2 class="my-4">SMS Conversations</h2>
    @foreach ($smsConversations as $conversation)
        <div class="card my-3">
            <div class="card-body">
                <h5 class="card-title">{{ $conversation->user->name }} - {{ $conversation->type }}</h5>
                <p class="card-text">{{ $conversation->messages->pluck('content')->implode(', ') }}</p>
                <form action="{{ route('admin.conversation.close', $conversation->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Close Conversation</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endsection
