@extends('layout.default')

@section('title', 'Create Ad')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <h1 class="page-header mb-0">Create New Ad</h1>
        <div class="ms-auto">
            <a href="{{ route('admin.ads.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-none fw-semibold">New Advertisement</div>
        <div class="card-body">
            <form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" name="description"></textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" accept="image/*">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Target URL</label>
                    <input type="url" class="form-control @error('target_url') is-invalid @enderror" name="target_url">
                    @error('target_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Type</label>
                    <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                        <option value="banner">Banner</option>
                        <option value="popup">Popup</option>
                        <option value="interstitial">Interstitial</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Active</label>
                    <select class="form-select @error('is_active') is-invalid @enderror" name="is_active" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" name="start_date">
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">End Date</label>
                    <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" name="end_date">
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Priority</label>
                    <input type="number" class="form-control @error('priority') is-invalid @enderror" name="priority" value="0" min="0" required>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.ads.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-theme">Create Ad</button>
                </div>
            </form>
        </div>
    </div>
@endsection