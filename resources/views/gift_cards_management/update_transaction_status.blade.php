@extends('layout.default')

@section('title', 'Update Transaction Status - #{{ $transaction->id }}')

@section('content')
    <div class="d-flex align-items-center mb-4">
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.gift-cards') }}">Gift Cards</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.gift-cards.all-transactions') }}">All Transactions</a></li>
                <li class="breadcrumb-item active">UPDATE STATUS #{{ $transaction->id }}</li>
            </ol>
            <h1 class="page-header mb-0">Update Transaction Status #{{ $transaction->id }}</h1>
        </div>
        <div class="ms-auto">
            <a href="{{ route('admin.gift-cards.transaction', $transaction->id) }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-2"></i> Back to Details
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <h5 class="mb-0">Change Transaction Status</h5>
            <span class="ms-auto badge {{ $transaction->status == 'completed' ? 'bg-success' : ($transaction->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                {{ ucfirst($transaction->status) }}
            </span>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.gift-cards.update-transaction-status', $transaction->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Current Details</label>
                            <ul class="list-unstyled">
                                <li><strong>User:</strong> {{ $transaction->user->name }}</li>
                                <li><strong>Gift Card:</strong> {{ $transaction->giftCard->name }}</li>
                                <li><strong>Type:</strong> {{ ucfirst($transaction->type) }}</li>
                                <li><strong>Quantity:</strong> {{ number_format($transaction->amount, 2) }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">New Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="pending" {{ $transaction->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ $transaction->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="rejected" {{ $transaction->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="flagged" {{ $transaction->status == 'flagged' ? 'selected' : '' }}>Flagged</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.gift-cards.transaction', $transaction->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-theme">Update Status</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .card-header {
        border-bottom: none;
    }
    .form-label {
        font-weight: 500;
        color: #6c757d;
    }
    .list-unstyled li {
        margin-bottom: 0.5rem;
    }
</style>
@endpush