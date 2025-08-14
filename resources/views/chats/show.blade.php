@extends('layout.default')

@section('title', 'Chats')
@section('content')
    <style>
        .chat-container { max-height: 500px; overflow-y: auto; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .message { margin: 10px 0; }
        .user-message { text-align: right; }
        .admin-message { text-align: left; }
        .message-content { display: inline-block; padding: 10px; border-radius: 10px; max-width: 70%; }
        .user-message .message-content { background-color: #d1e7dd; }
        .admin-message .message-content { background-color: #f8d7da; }
        .timestamp { font-size: 0.8em; color: #666; }
    </style>

    <div class="container mt-5">
        <h1>Chat with {{ $chat->user->name }}</h1>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="chat-container">
            @foreach ($chat->messages as $message)
                <div class="message {{ $message->sender_type == 'user' ? 'user-message' : 'admin-message' }}">
                    <div class="message-content">
                        <p>{{ $message->content }}</p>
                        <div class="timestamp">{{ $message->created_at->format('H:i, d M Y') }}</div>
                        @if ($message->sender_type == 'user' && !$message->is_read)
                            <span class="badge bg-warning">Unread</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <form action="{{ route('admin.chats.reply', $chat->id) }}" method="POST" class="mt-3">
            @csrf
            <div class="mb-3">
                <textarea name="content" class="form-control" rows="4" placeholder="Type your reply..." required></textarea>
            </div>
            <button type="submit" class="btn btn-success">Send Reply</button>
            @if ($chat->has_unread)
                <a href="{{ route('admin.chats.markAsRead', $chat->id) }}" class="btn btn-secondary">Mark as Read</a>
            @endif
        </form>
    </div>
    @endsection
