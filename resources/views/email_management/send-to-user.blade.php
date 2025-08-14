@extends('layout.default')


@section('content')
    <h1>Send Email to a Specific User</h1>

    <form action="{{ route('admin.email.send-to-user') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="user_id" class="form-label">Select User</label>
            <select name="user_id" id="user_id" class="form-select" required>
                <option value="">-- Select a User --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
            @error('user_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject') }}" required>
            @error('subject')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea name="message" id="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
            @error('message')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Send Email</button>
    </form>
@endsection