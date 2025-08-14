@extends('layout.default')

@section('title', 'Transactions by User')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.gift-cards') }}">Gift Cards</a></li>
                <li class="breadcrumb-item active">TRANSACTIONS BY {{ $user->name }}</li>
            </ol>
            <h1 class="page-header mb-0">Transactions by {{ $user->name }}</h1>
        </div>
        <div class="ms-auto">
            <span class="text-body">Welcome, {{ Auth::user()->name }}</span>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center bg-none fw-semibold">
            Transactions ({{ $transactions->count() }})
            <a href="{{ route('admin.gift-cards.create-transaction') }}" class="ms-auto btn btn-theme btn-sm">Create New Transaction</a>
        </div>
        <div class="card-body">
            @if ($transactions->isEmpty())
                <p class="text-muted">No transactions found for {{ $user->name }}.</p>
            @else
                <table class="table table-borderless table-sm m-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gift Card</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->id }}</td>
                                <td>{{ $transaction->giftCard->name }}</td>
                                <td>{{ ucfirst($transaction->type) }}</td>
                                <td>${{ $transaction->amount }}</td>
                                <td>
                                    <span class="badge {{ $transaction->status == 'completed' ? 'bg-success' : ($transaction->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.gift-cards.transaction', $transaction->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection