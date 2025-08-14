@extends('layout.default')

@section('title', 'All VTU Transactions')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <h1 class="page-header mb-0">All VTU Transactions</h1>
    </div>

    <div class="card">
        <div class="card-header bg-none fw-semibold">Transactions ({{ $transactions->count() }})</div>
        <div class="card-body">
            <table class="table table-borderless table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Plan</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>{{ $transaction->user->name }}</td>
                            <td>{{ $transaction->plan->plan_code }}</td>
                            <td>${{ $transaction->amount }}</td>
                            <td><span class="badge {{ $transaction->status == 'success' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst($transaction->status) }}</span></td>
                            <td>
                                @if ($transaction->status == 'failed' && !$transaction->is_refunded)
                                    <form action="{{ route('admin.vtu.refund-transaction', $transaction->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Refund</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection