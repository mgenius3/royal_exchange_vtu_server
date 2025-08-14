@extends('layout.default')

@section('title', 'Manage Exchange Rates')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <h1 class="page-header mb-0">Manage Exchange Rates</h1>
        <a href="{{ route('admin.exchange-rates.create') }}" class="btn btn-theme ms-auto">Add Exchange Rate</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-none fw-semibold">Exchange Rates (Base: USD)</div>
        <div class="card-body">
            @if ($exchangeRates->isEmpty())
                <p>No exchange rates found.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Currency</th>
                            <th>Rate (1 Currency = X USD)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($exchangeRates as $rate)
                            <tr>
                                <td>{{ $rate->currency_code }}</td>
                                <td>{{ number_format($rate->rate, 4) }}</td>
                                <td>
                                    <a href="{{ route('admin.exchange-rates.edit', $rate) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('admin.exchange-rates.destroy', $rate) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this exchange rate?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection