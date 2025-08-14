@extends('layout.default')


@section('content')
    <h1>Broadcast Email to All Users</h1>

    <form action="{{ route('admin.email.broadcast') }}" method="POST">
        @csrf

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

        <button type="submit" class="btn btn-primary">Broadcast Email</button>
    </form>
@endsection