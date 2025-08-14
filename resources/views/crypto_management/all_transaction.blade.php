@extends('layout.default')

@section('title', 'All Crypto Transactions')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <h1 class="page-header mb-0">All Crypto Transactions</h1>
    </div>

    <div class="card">
        <div class="card-header bg-none fw-semibold">Transactions ({{ $transactions->count() }})</div>
        <div class="card-body">
            <table class="table table-borderless table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Crypto</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Fiat Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>{{ $transaction->user->name }}</td>
                            <td>{{ $transaction->cryptoCurrency->symbol }}</td>
                            <td>{{ ucfirst($transaction->type) }}</td>
                            <td>{{ $transaction->amount }}</td>
                            <td>&#8358;{{ $transaction->fiat_amount }}</td>
                            <td><span class="badge {{ $transaction->status == 'completed' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst($transaction->status) }}</span></td>
                            <td>
                                <a href="{{ route('admin.crypto.transaction', $transaction->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection