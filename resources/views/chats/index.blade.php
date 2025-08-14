@extends('layout.default')

@section('title', 'Chats')
@section('content')

<div class="container mt-5">
    <h1>Support Chats</h1>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User</th>
                <th>Unread Messages</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($chats as $chat)
                <tr>
                    <td>{{ $chat->user->name }}</td>
                    <td>
                        @if ($chat->has_unread)
                            <span class="badge bg-danger">Unread</span>
                        @else
                            <span class="badge bg-success">Read</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.chats.show', $chat->id) }}" class="btn btn-primary btn-sm">View</a>
                        <form action="{{ route('admin.chats.destroy', $chat->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this chat?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
    @endsection
