@extends('layout.default')

@section('title', 'Bank Details')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <h1 class="page-header mb-0">Bank Details</h1>
        <div class="ms-auto">
            <span class="text-body">Welcome, {{ Auth::user()->name }}</span>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <!-- Add New Bank Account -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header bg-none fw-semibold">Add New Bank Account</div>
                <div class="card-body">
                    <form action="{{ route('admin.bank-details.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Bank Name</label>
                            <input type="text" class="form-control @error('bank_name') is-invalid @enderror" name="bank_name" value="{{ old('bank_name') }}" required>
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Account Name</label>
                            <input type="text" class="form-control @error('account_name') is-invalid @enderror" name="account_name" value="{{ old('account_name') }}" required>
                            @error('account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Account Number</label>
                            <input type="text" class="form-control @error('account_number') is-invalid @enderror" name="account_number" value="{{ old('account_number') }}" required>
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">IFSC Code (Optional)</label>
                            <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror" name="ifsc_code" value="{{ old('ifsc_code') }}">
                            @error('ifsc_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">SWIFT Code (Optional)</label>
                            <input type="text" class="form-control @error('swift_code') is-invalid @enderror" name="swift_code" value="{{ old('swift_code') }}">
                            @error('swift_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-theme">Add Bank Account</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- List of Bank Accounts -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header bg-none fw-semibold">Your Bank Accounts ({{ $bankDetailsList->count() }})</div>
                <div class="card-body">
                    @if ($bankDetailsList->isEmpty())
                        <p>No bank accounts added yet.</p>
                    @else
                        @foreach ($bankDetailsList as $bankDetails)
                            <div class="mb-3">
                                <p><strong>Bank Name:</strong> {{ $bankDetails->bank_name }}</p>
                                <p><strong>Account Name:</strong> {{ $bankDetails->account_name }}</p>
                                <p><strong>Account Number:</strong> {{ $bankDetails->account_number }}</p>
                                @if ($bankDetails->ifsc_code)
                                    <p><strong>IFSC Code:</strong> {{ $bankDetails->ifsc_code }}</p>
                                @endif
                                @if ($bankDetails->swift_code)
                                    <p><strong>SWIFT Code:</strong> {{ $bankDetails->swift_code }}</p>
                                @endif
                                <form action="{{ route('admin.bank-details.delete', $bankDetails->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this bank account?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                            @if (!$loop->last)
                                <hr>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection