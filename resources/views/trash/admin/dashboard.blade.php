<!-- resources/views/admin/dashboard.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    @if (session('status'))
        <div>
            {{ session('status') }}
        </div>
    @endif

    <h2>Call Conversations</h2>
    <table>
        <thead>
            <tr>
                <th>User Phone</th>
                <th>Language</th>
                <th>Conversation</th>
                <th>Close Conversation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($callConversations as $conversation)
                <tr>
                    <td>{{ $conversation->user->phone }}</td>
                    <td>{{ $conversation->user->language }}</td>
                    <td>
                        @foreach($conversation->messages as $message)
                            <p><strong>{{ ucfirst($message->sender) }}:</strong> {{ $message->content }} ({{ $message->created_at }})</p>
                            @if($message->audio_url)
                                <p><a href="{{ $message->audio_url }}" target="_blank">Listen to audio</a></p>
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @if(!$conversation->is_closed)
                            <form action="{{ route('admin.close-conversation', $conversation->id) }}" method="POST">
                                @csrf
                                @method('POST')
                                <button type="submit">Close</button>
                            </form>
                        @else
                            <p>Closed</p>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>SMS Conversations</h2>
    <table>
        <thead>
            <tr>
                <th>User Phone</th>
                <th>Language</th>
                <th>Conversation</th>
                <th>Close Conversation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($smsConversations as $conversation)
                <tr>
                    <td>{{ $conversation->user->phone }}</td>
                    <td>{{ $conversation->user->language }}</td>
                    <td>
                        @foreach($conversation->messages as $message)
                            <p><strong>{{ ucfirst($message->sender) }}:</strong> {{ $message->content }} ({{ $message->created_at }})</p>
                        @endforeach
                    </td>
                    <td>
                        @if(!$conversation->is_closed)
                            <form action="{{ route('admin.close-conversation', $conversation->id) }}" method="POST">
                                @csrf
                                @method('POST')
                                <button type="submit">Close</button>
                            </form>
                        @else
                            <p>Closed</p>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
