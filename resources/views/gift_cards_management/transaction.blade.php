@extends('layout.default')

@section('title', 'Transaction Details - #{{ $transaction->id }}')

@section('content')
    <div class="d-flex align-items-center mb-4">
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.gift-cards') }}">Gift Cards</a></li>
                <li class="breadcrumb-item active">TRANSACTION #{{ $transaction->id }}</li>
            </ol>
            <h1 class="page-header mb-0">Transaction #{{ $transaction->id }}</h1>
        </div>
        <div class="ms-auto">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-2"></i> Back
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <h5 class="mb-0">Transaction Details</h5>
            <span class="ms-auto badge {{ $transaction->status == 'completed' ? 'bg-success' : ($transaction->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                {{ ucfirst($transaction->status) }}
            </span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <strong class="text-muted">User:</strong>
                        <span class="ms-2">{{ $transaction->user->name }}</span>
                     
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Gift Card:</strong>
                        <span class="ms-2">{{ $transaction->giftCard->name }}</span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Type:</strong>
                        <span class="ms-2">{{ ucfirst($transaction->type) }}</span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Balance($):</strong>
                        <span class="ms-2 text-success">{{ $transaction->balance }}</span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Payable Amount(NGN):</strong>
                        <span class="ms-2 text-success">{{ $transaction->fiat_amount }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        @if ($transaction->type === 'buy')
                            <strong class="text-muted">Payment Image:</strong>
                        @elseif ($transaction->type === 'sell')
                            <strong class="text-muted">Gift Card Image:</strong>
                        @else
                            <strong class="text-muted">Proof File:</strong>
                        @endif
                    
                        <span class="ms-2">
                            @if ($transaction->proof_file)
                                <a href="{{ $transaction->proof_file }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-eye me-1"></i> View Image
                                </a>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <strong class="text-muted">E-code:</strong>
                        <span class="ms-2">{{ $transaction->ecode ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Transaction Hash:</strong>
                        <span class="ms-2">{{ $transaction->tx_hash ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Admin Notes:</strong>
                        <span class="ms-2">{{ $transaction->admin_notes ?? 'None' }}</span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted">Created At:</strong>
                        <span class="ms-2">{{ $transaction->created_at->format('Y-m-d H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-light text-end">
            <a href="{{ route('admin.gift-cards.all-transactions') }}" class="btn btn-outline-primary me-2">
                View All Transactions
            </a>
            <a href="{{ route('admin.gift-cards.user-transactions', $transaction->user->id) }}" class="btn btn-outline-primary">
                View User Transactions
            </a>
            <a href="{{ route('admin.gift-cards.edit-transaction-status', $transaction->id) }}" class="btn btn-primary">
                <i class="fa fa-sync me-1"></i> Update Status
            </a>

            <form action="{{ route('admin.gift-cards.transaction.delete', $transaction->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this transaction?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete Transaction</button>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .card-header {
        border-bottom: none;
    }
    .card-body .row > div {
        padding: 10px 0;
    }
    .text-muted {
        font-weight: 500;
    }
    .badge {
        font-size: 0.9em;
        padding: 0.5em 1em;
    }
</style>
@endpush