@extends('layout.default')

@section('title', 'Edit Ad')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <h1 class="page-header mb-0">Edit Ad #{{ $ad->id }}</h1>
        <div class="ms-auto">
            <a href="{{ route('admin.ads.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-none fw-semibold">Edit Advertisement</div>
        <div class="card-body">
            <form action="{{ route('admin.ads.update', $ad->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $ad->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" name="description">{{ old('description', $ad->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Current Image</label>
                    @if ($ad->image_url)
                        <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}" style="max-width: 200px;">
                    @else
                        <p>No image uploaded</p>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload New Image</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" accept="image/*">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Target URL</label>
                    <input type="url" class="form-control @error('target_url') is-invalid @enderror" name="target_url" value="{{ old('target_url', $ad->target_url) }}">
                    @error('target_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Type</label>
                    <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                        <option value="banner" {{ old('type', $ad->type) == 'banner' ? 'selected' : '' }}>Banner</option>
                        <option value="popup" {{ old('type', $ad->type) == 'popup' ? 'selected' : '' }}>Popup</option>
                        <option value="interstitial" {{ old('type', $ad->type) == 'interstitial' ? 'selected' : '' }}>Interstitial</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Active</label>
                    <select class="form-select @error('is_active') is-invalid @enderror" name="is_active" required>
                        <option value="1" {{ old('is_active', $ad->is_active) ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('is_active', $ad->is_active) ? '' : 'selected' }} >No</option>
                    </select>
                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" name="start_date" value="{{ old('start_date', $ad->start_date ? $ad->start_date->format('Y-m-d\TH:i') : '') }}">
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">End Date</label>
                    <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" name="end_date" value="{{ old('end_date', $ad->end_date ? $ad->end_date->format('Y-m-d\TH:i') : '') }}">
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Priority</label>
                    <input type="number" class="form-control @error('priority') is-invalid @enderror" name="priority" value="{{ old('priority', $ad->priority) }}" min="0" required>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.ads.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-theme">Update Ad</button>
                </div>
            </form>
        </div>
    </div>
@endsection