@extends('layout.default')

@section('title', 'VTU Management')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <h1 class="page-header mb-0">VTU Management</h1>
        <div class="ms-auto">
            <span class="text-body">Welcome, {{ Auth::user()->name }}</span>
        </div>
    </div>

    <div class="row gx-4">
        <div class="col-xl-8">
            <!-- Dashboard Stats -->
            <div class="card mb-4">
                <div class="card-header bg-none fw-semibold">Dashboard</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">Total Transactions: {{ $stats['total_transactions'] }}</div>
                        <div class="col-md-4">Success Rate: {{ round($stats['success_rate']) }}%</div>
                        <div class="col-md-4">Revenue: ${{ number_format($stats['revenue'], 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Providers -->
            <div class="card mb-4">
                <div class="card-header bg-none fw-semibold">
                    VTU Providers ({{ $providers->count() }})
                    <a href="#" class="ms-auto text-body text-opacity-50" data-bs-toggle="modal" data-bs-target="#addProviderModal">Add Provider</a>
                </div>
                <div class="card-body">
                    @foreach ($providers as $provider)
                        <div class="row align-items-center">
                            <div class="col-md-4">{{ $provider->name }}</div>
                            <div class="col-md-4">Success Rate: {{ $provider->success_rate }}%</div>
                            <div class="col-md-4 text-end">
                                <span class="badge {{ $provider->is_active ? 'bg-success' : 'bg-danger' }}">{{ $provider->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                        </div>
                        @if (!$loop->last)
                            <hr class="my-3">
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Plans -->
            <div class="card">
                <div class="card-header bg-none fw-semibold">
                    Available Plans ({{ $plans->count() }})
                    <a href="#" class="ms-auto text-body text-opacity-50" data-bs-toggle="modal" data-bs-target="#addPlanModal">Add Plan</a>
                </div>
                <div class="card-body">
                    @foreach ($plans as $plan)
                        <div class="row align-items-center">
                            <div class="col-md-4">{{ $plan->network }} - {{ $plan->description }} ({{ $plan->plan_code }})</div>
                            <div class="col-md-4">Price: ${{ $plan->price }} | Commission: ${{ $plan->commission }}</div>
                            <div class="col-md-4 text-end">{{ $plan->provider->name }}</div>
                        </div>
                        @if (!$loop->last)
                            <hr class="my-3">
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <!-- Recent Transactions -->
            <div class="card mb-4">
                <div class="card-header bg-none fw-semibold">
                    Recent Transactions ({{ $transactions->count() }})
                    <a href="{{ route('admin.vtu.all-transactions') }}" class="ms-auto text-body text-opacity-50">View All</a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Plan</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->user->name }}</td>
                                    <td>{{ $transaction->plan->plan_code }}</td>
                                    <td>${{ $transaction->amount }}</td>
                                    <td><span class="badge {{ $transaction->status == 'success' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst($transaction->status) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Provider Modal -->
    <div class="modal fade" id="addProviderModal" tabindex="-1" aria-labelledby="addProviderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProviderModalLabel">Add New VTU Provider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.vtu.store-provider') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Key</label>
                            <input type="text" class="form-control" name="api_key">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Token</label>
                            <input type="text" class="form-control" name="api_token">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Base URL</label>
                            <input type="url" class="form-control" name="base_url">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-theme">Add Provider</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Plan Modal -->
    <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPlanModalLabel">Add New VTU Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.vtu.store-plan') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Provider</label>
                            <select class="form-select" name="vtu_provider_id" required>
                                @foreach ($providers as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Network</label>
                            <input type="text" class="form-control" name="network" placeholder="e.g., MTN, Airtel">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="type" required>
                                <option value="airtime">Airtime</option>
                                <option value="data">Data</option>
                                <option value="tv">TV Subscription</option>
                                <option value="electricity">Electricity</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Plan Code</label>
                            <input type="text" class="form-control" name="plan_code" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="description">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Commission</label>
                            <input type="number" step="0.01" class="form-control" name="commission" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-theme">Add Plan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection