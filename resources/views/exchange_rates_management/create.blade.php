@extends('layout.default')

@section('title', 'Add Exchange Rate')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <h1 class="page-header mb-0">Add Exchange Rate</h1>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-none fw-semibold">New Exchange Rate (Base: USD)</div>
        <div class="card-body">
            <form action="{{ route('admin.exchange-rates.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Currency Code</label>
                    <input type="text" class="form-control @error('currency_code') is-invalid @enderror" name="currency_code" value="{{ old('currency_code') }}" placeholder="e.g., NGN" maxlength="3" required>
                    @error('currency_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Exchange Rate (1 Currency = X USD)</label>
                    <input type="number" step="0.0001" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{ old('rate') }}" required>
                    @error('rate')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.exchange-rates.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-theme">Add Exchange Rate</button>
                </div>
            </form>
        </div>
    </div>
@endsection