@extends('layout.default')

@section('title', 'Update Crypto Transaction Status')

@section('content')
    <div class="d-flex align-items-center mb-4">
        <h1 class="page-header mb-0">Update Transaction #{{ $transaction->id }} Status</h1>
        <div class="ms-auto">
            <a href="{{ route('admin.crypto.transaction', $transaction->id) }}" class="btn btn-secondary">Back to Details</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <h5 class="mb-0">Change Transaction Status</h5>
            <span class="ms-auto badge {{ $transaction->status == 'completed' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst($transaction->status) }}</span>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.crypto.update-transaction-status', $transaction->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Current Details</label>
                            <ul class="list-unstyled">
                                <li><strong>User:</strong> {{ $transaction->user->name }}</li>
                                <li><strong>Crypto:</strong> {{ $transaction->cryptoCurrency->symbol }}</li>
                                <li><strong>Type:</strong> {{ ucfirst($transaction->type) }}</li>
                                <li><strong>Fiat Amount:</strong> ${{ $transaction->fiat_amount }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">New Status</label>
                            <select class="form-select" name="status" required>
                                <option value="pending" {{ $transaction->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ $transaction->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ $transaction->status == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.crypto.transaction', $transaction->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-theme">Update Status</button>
                </div>
            </form>
        </div>
    </div>
@endsection