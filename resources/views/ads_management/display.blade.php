@extends('layout.default')

@section('title', 'Ads Management')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <h1 class="page-header mb-0">Ads Management</h1>
        <div class="ms-auto">
            <a href="{{ route('admin.ads.create') }}" class="btn btn-theme">Create New Ad</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-none fw-semibold">Advertisements ({{ $ads->count() }})</div>
        <div class="card-body">
            <table class="table table-borderless table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ads as $ad)
                        <tr>
                            <td>{{ $ad->id }}</td>
                            <td>{{ $ad->title }}</td>
                            <td>{{ ucfirst($ad->type) }}</td>
                            <td>{{ $ad->priority }}</td>
                            <td><span class="badge {{ $ad->is_active ? 'bg-success' : 'bg-danger' }}">{{ $ad->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td>
                                <a href="{{ route('admin.ads.edit', $ad->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.ads.destroy', $ad->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection