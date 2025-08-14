@extends('layout.default')

@section('title', 'Edit User')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">PAGES</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">USERS</a></li>
                <li class="breadcrumb-item active">EDIT USER</li>
            </ul>
            <h1 class="page-header mb-0">Edit User - <b data-id="{{$user['id']}}">{{$user['id']}}</b>

            </h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form>
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $user['name'] }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $user['email'] }}" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ $user['phone'] }}">
                </div>

                {{-- <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="active" {{ $user['status'] === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="banned" {{ $user['status'] === 'banned' ? 'selected' : '' }}>Banned</option>
                        <option value="suspended" {{ $user['status'] === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div> --}}

                <button type="submit" class="btn btn-primary" id="updateUserButton">Update User</button>
            </form>
        </div>
    </div>
@endsection

@push('other_scripts')
<script src="{{ asset('assets/js/user_management/edit_user.js') }}"></script>
@endpush