@extends('layout.default')

@section('title', 'Edit Wallet Address')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <h1 class="page-header mb-0">Edit Wallet Address for {{ $crypto->name }}</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header bg-none fw-semibold">Update Wallet Address</div>
        <div class="card-body">
            <form action="{{ route('admin.crypto.update-wallet-address', $crypto->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Wallet Address</label>
                    <input type="text" class="form-control @error('wallet_address') is-invalid @enderror" name="wallet_address" value="{{ old('wallet_address', $crypto->wallet_address) }}" required>
                    @error('wallet_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.crypto') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-theme">Update Wallet Address</button>
                </div>
            </form>
        </div>
    </div>
@endsection