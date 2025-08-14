@extends('layout.default')

@section('title', 'Crypto Management')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold text-dark">Crypto Management</h1>
            <div class="d-flex align-items-center">
                <span class="text-muted me-3">Welcome, {{ Auth::user()->name }}</span>
                <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCryptoModal">
                    <i class="bi bi-plus-circle me-1"></i> Add Crypto
                </a>
            </div>
        </div>

        <!-- Success/Error Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- Left Column: Crypto Catalog & Transactions -->
            <div class="col-xl-8">
                <!-- Crypto Catalog -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title mb-0 fw-semibold">Supported Cryptocurrencies ({{ $cryptos->count() }})</h5>
                        <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addCryptoModal">
                            <i class="bi bi-plus-circle me-1"></i> Add Crypto
                        </a>
                    </div>
                    <div class="card-body">
                        @foreach ($cryptos as $crypto)
                            <div class=" row align-items-center">
                                <div class="col-2 d-flex align-items-center">
                                    @if ($crypto->image)
                                        <img src="{{ $crypto->image }}" alt="{{ $crypto->name }}"
                                            class="rounded-circle me-2" style="width: 32px; height: 32px;">
                                    @endif
                                    <div>
                                        {{ $crypto->symbol }}
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div>Buy Rate: <span
                                            class="text-success">NGN {{ $crypto->buy_rate }}</span>
                                    </div>
                                    <div>Sell Rate: <span
                                            class="text-danger">NGN {{ $crypto->sell_rate }}</span>
                                    </div>
                                    {{-- <div>Price: <span class="text-primary">${{ $crypto->current_price }}</span></div> --}}
                                    <div>Wallet: {{ $crypto->wallet_address ?? 'Not set' }}</div>
                                </div>
                                <div class="col-2 m-0 ">
                                    <form action="{{ route('admin.crypto.toggle', $crypto->id) }}" method="POST"
                                        class="d-inline mb-1">
                                        @csrf
                                        <input type="hidden" name="is_enabled" value="{{ $crypto->is_enabled ? 0 : 1 }}">
                                        <button type="submit"
                                            class="btn btn-sm {{ $crypto->is_enabled ? 'btn-warning' : 'btn-success' }} m-1">
                                            {{ $crypto->is_enabled ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.crypto.edit-wallet-address', $crypto->id) }}"
                                        class="btn btn-sm btn-outline-primary m-1">Edit Wallet</a>
                                    {{-- <div> <a href="#" class="btn btn-sm btn-outline-info m-1" data-bs-toggle="modal"
                                            data-bs-target="#editCurrentPriceModal{{ $crypto->id }}">Edit Price</a></div> --}}

                                    <form action="{{ route('admin.crypto.delete', $crypto->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this cryptocurrency?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger m-1">Delete</button>
                                    </form>
                                </div>


                            </div>

                            @if (!$loop->last)
                                <hr class="opacity-1 my-4">
                            @endif

                            <!-- Edit Current Price Modal -->
                            <div class="modal fade" id="editCurrentPriceModal{{ $crypto->id }}" tabindex="-1"
                                aria-labelledby="editCurrentPriceModalLabel{{ $crypto->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header border-0">
                                            <h5 class="modal-title fw-bold"
                                                id="editCurrentPriceModalLabel{{ $crypto->id }}">Edit Price for
                                                {{ $crypto->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.crypto.update-current-price', $crypto->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Current Price</label>
                                                    <input type="number" step="0.00000001"
                                                        class="form-control @error('current_price') is-invalid @enderror"
                                                        name="current_price" value="{{ $crypto->current_price }}" required>
                                                    @error('current_price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update Price</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>

                <!-- Transaction History -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title mb-0 fw-semibold">Recent Transactions ({{ $transactions->count() }})</h5>
                        <a href="{{ route('admin.crypto.all-transactions') }}"
                            class="text-primary text-decoration-none">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Crypto</th>
                                        <th>Type</th>
                                        <th>Fiat Amount(&#8358;)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->user->name }}</td>
                                            <td>{{ $transaction->cryptoCurrency->symbol }}</td>
                                            <td>{{ ucfirst($transaction->type) }}</td>
                                            <td>&#8358;{{ $transaction->fiat_amount }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $transaction->status == 'completed' ? 'bg-success' : 'bg-warning' }} text-white">
                                                    {{ ucfirst($transaction->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Rate & Liquidity Management -->
            <div class="col-xl-4">
                <!-- Rate Management -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0 fw-semibold">Rate Management</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.crypto.update-rates') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Select Crypto</label>
                                <select class="form-select" name="crypto_id" id="cryptoSelect" required>
                                    {{-- <option value="" disabled selected>Select a cryptocurrency</option> --}}
                                    @forelse ($cryptos as $crypto)
                                        <option value="{{ $crypto->id }}" data-buy-rate="{{ $crypto->buy_rate }}"
                                            data-sell-rate="{{ $crypto->sell_rate }}">
                                            {{ $crypto->name }} ({{ $crypto->symbol }})
                                        </option>
                                    @empty
                                        <option value="">No crypto available</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Currency</label>
                                <input type="text" class="form-control" name="currency" value="USD" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Buy Rate <small>(Current: <span
                                            id="currentBuyRate">{{ $cryptos->isNotEmpty() ? $cryptos->first()->buy_rate : 'N/A' }}</span>)</small></label>
                                <input type="number" step="0.01" class="form-control" name="buy_rate"
                                    id="buyRate" placeholder="e.g., 0.80" {{ $cryptos->isEmpty() ? 'disabled' : '' }}
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sell Rate <small>(Current: <span
                                            id="currentSellRate">{{ $cryptos->isNotEmpty() ? $cryptos->first()->sell_rate : 'N/A' }}</span>)</small></label>
                                <input type="number" step="0.01" class="form-control" name="sell_rate"
                                    id="sellRate" placeholder="e.g., 0.95" {{ $cryptos->isEmpty() ? 'disabled' : '' }}
                                    required>
                            </div>


                            {{-- <div class="mb-3">
                                <label class="form-label fw-semibold">Buy Rate ({{ $crypto->buy_rate }})</label>
                                <input type="number" step="0.01" class="form-control" name="buy_rate"
                                    id="buyRate" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Sell Rate ({{ $crypto->sell_rate }})</label>
                                <input type="number" step="0.01" class="form-control" name="sell_rate"
                                    id="sellRate" required>
                            </div> --}}
                            <button type="submit" class="btn btn-theme" data-bs-toggle="tooltip"
                                title="Update rates for selected gift card"
                                {{ $cryptos->isEmpty() ? 'disabled' : '' }}>Update Rates</button>
                        </form>
                    </div>
                </div>

                <!-- Liquidity Management -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0 fw-semibold">Liquidity Management</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($wallets as $wallet)
                            <div class="mb-3 d-flex justify-content-between">
                                <strong>{{ $wallet->cryptoCurrency->symbol }}</strong>
                                <span>{{ $wallet->balance }}</span>
                            </div>
                        @endforeach
                        <form action="{{ route('admin.crypto.update-liquidity') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Crypto</label>
                                <select class="form-select" name="crypto_id" required>
                                    @foreach ($cryptos as $crypto)
                                        <option value="{{ $crypto->id }}">{{ $crypto->symbol }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Amount</label>
                                <input type="number" step="0.00000001" class="form-control" name="amount" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Action</label>
                                <select class="form-select" name="action" required>
                                    <option value="add">Add</option>
                                    <option value="withdraw">Withdraw</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Liquidity</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Crypto Modal -->
        <div class="modal fade" id="addCryptoModal" tabindex="-1" aria-labelledby="addCryptoModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold" id="addCryptoModalLabel">Add New Cryptocurrency</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.crypto.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Symbol</label>
                                <input type="text" class="form-control @error('symbol') is-invalid @enderror"
                                    name="symbol" required>
                                @error('symbol')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Network</label>
                                <input type="text" class="form-control @error('network') is-invalid @enderror"
                                    name="network" placeholder="e.g., Bitcoin, ERC-20">
                                @error('network')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Buy Rate</label>
                                <input type="number" step="0.01"
                                    class="form-control @error('buy_rate') is-invalid @enderror" name="buy_rate" required>
                                @error('buy_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Sell Rate</label>
                                <input type="number" step="0.01"
                                    class="form-control @error('sell_rate') is-invalid @enderror" name="sell_rate"
                                    required>
                                @error('sell_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Current Price</label>
                                <input type="number" step="0.00000001"
                                    class="form-control @error('current_price') is-invalid @enderror" name="current_price"
                                    required>
                                @error('current_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Admin Wallet Address</label>
                                <input type="text" class="form-control @error('wallet_address') is-invalid @enderror"
                                    name="wallet_address" required>
                                @error('wallet_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Image</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    name="image" accept="image/*" required>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Cryptocurrency</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Rate Management Form -->
    {{-- @section('scripts') --}}
    <script>
        // document.getElementById('cryptoSelect').addEventListener('change', function() {
        //     const selectedOption = this.options[this.selectedIndex];
        //     const buyRate = selectedOption.getAttribute('data-buy-rate');
        //     const sellRate = selectedOption.getAttribute('data-sell-rate');

        //     document.getElementById('buyRate').value = buyRate || '';
        //     document.getElementById('sellRate').value = sellRate || '';
        // });


        const cryptoSelect = document.getElementById('cryptoSelect');
        const currentBuyRate = document.getElementById('currentBuyRate');
        const currentSellRate = document.getElementById('currentSellRate');
        cryptoSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            currentBuyRate.textContent = selectedOption.dataset.buyRate || 'N/A';
            currentSellRate.textContent = selectedOption.dataset.sellRate || 'N/A';
        });
    </script>
    {{-- @endsection --}}
@endsection
