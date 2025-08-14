@extends('layout.default')

@section('title', 'Create Crypto Transaction')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <h1 class="page-header mb-0">Create Crypto Transaction</h1>
    </div>

    <!-- Display Error Message -->
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Display Success Message -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-none fw-semibold">New Transaction</div>
        <div class="card-body">
            <form action="{{ route('admin.crypto.store-transaction') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">User</label>
                    <select class="form-select @error('user_id') is-invalid @enderror" name="user_id" required>
                        <option value="">Select a user</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{$user->email}})</option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Cryptocurrency</label>
                    <select class="form-select @error('crypto_currency_id') is-invalid @enderror" name="crypto_currency_id" id="cryptoCurrency" required>
                        <option value="">Select a cryptocurrency</option>
                        @foreach ($cryptos as $crypto)
                            <option value="{{ $crypto->id }}" data-name="{{ $crypto->name }}" data-wallet-address="{{ $crypto->wallet_address }}">{{ $crypto->name }} ({{ $crypto->symbol }})</option>
                        @endforeach
                    </select>
                    @error('crypto_currency_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <!-- Hidden field to store crypto name -->
                <input type="hidden" name="crypto_name" id="cryptoName">
                <div class="mb-3">
                    <label class="form-label">Type</label>
                    <select class="form-select @error('type') is-invalid @enderror" name="type" id="transactionType" required>
                        <option value="">Select type</option>
                        <option value="buy">Buy</option>
                        <option value="sell">Sell</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount (in crypto)</label>
                    <input type="number" step="0.00000001" class="form-control @error('amount') is-invalid @enderror" name="amount" required>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3" id="payment_method_container">
                    <label class="form-label">Payment Method</label>
                    <select class="form-select @error('payment_method') is-invalid @enderror" name="payment_method" id="payment_method" required>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="wallet_balance">Wallet Balance</option>
                    </select>
                    @error('payment_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3" id="walletAddressField" style="display: none;">
                    <label class="form-label">Wallet Address (to receive crypto)</label>
                    <input type="text" class="form-control @error('wallet_address') is-invalid @enderror" name="wallet_address">
                    @error('wallet_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3" id="adminWalletAddressField" style="display: none;">
                    <label class="form-label">Send Crypto to Admin Wallet Address</label>
                    <div class="alert alert-info" id="adminWalletAddress">Select a cryptocurrency to see the admin wallet address.</div>
                </div>
                <div class="mb-3" id="proofFileField" style="display: none;">
                    <label class="form-label">Proof of Money Transfer (e.g., bank receipt)</label>
                    <input type="file" class="form-control @error('proof_file') is-invalid @enderror" name="proof_file" id="proof_file_buy" accept="image/*,application/pdf">
                    @error('proof_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3" id="proofFileFieldSell" style="display: none;">
                    <label class="form-label">Proof of Coin Transfer (e.g., binance receipt)</label>
                    <input type="file" class="form-control @error('proof_file') is-invalid @enderror" name="proof_file_sell" id="proof_file_sell" accept="image/*,application/pdf">
                    @error('proof_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Transaction Hash (optional)</label>
                    <input type="text" class="form-control @error('tx_hash') is-invalid @enderror" name="tx_hash">
                    @error('tx_hash')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.crypto') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-theme">Create Transaction</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('other_scripts')
<script>
    const proofFileField = document.getElementById('proofFileField');
    const adminWalletAddressField = document.getElementById('adminWalletAddressField');
    const adminWalletAddress = document.getElementById('adminWalletAddress');

    // Update crypto name and wallet address when a cryptocurrency is selected
    document.getElementById('cryptoCurrency').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const cryptoName = selectedOption.getAttribute('data-name');
        const walletAddress = selectedOption.getAttribute('data-wallet-address');
        document.getElementById('cryptoName').value = cryptoName || '';
        
        // Update the admin wallet address display
        if (walletAddress) {
            adminWalletAddress.textContent = walletAddress;
        } else {
            adminWalletAddress.textContent = 'No wallet address set for this cryptocurrency.';
        }
    });

    document.getElementById('transactionType').addEventListener('change', function() {
        const type = this.value;
        const walletAddressField = document.getElementById('walletAddressField');
        const proofFileFieldSell = document.getElementById('proofFileFieldSell');
        const payment_method_container = document.getElementById('payment_method_container');

        if (type === 'buy') {
            walletAddressField.style.display = 'block';
            proofFileField.style.display = 'block';
            proofFileFieldSell.style.display = 'none';
            adminWalletAddressField.style.display = 'none';
            payment_method_container.style.display = "block";
            document.querySelector('input[name="wallet_address"]').required = true;
            document.querySelector('input[id="proof_file_buy"]').required = true;
            document.querySelector('input[id="proof_file_sell"]').required = false;
        } else if (type === 'sell') {
            walletAddressField.style.display = 'none';
            proofFileFieldSell.style.display = 'block';
            proofFileField.style.display = 'none';
            adminWalletAddressField.style.display = 'block';
            payment_method_container.style.display = "none";
            document.querySelector('input[name="wallet_address"]').required = false;
            document.querySelector('input[id="proof_file_sell"]').required = true;
            document.querySelector('input[id="proof_file_buy"]').required = false;
        } else {
            walletAddressField.style.display = 'none';
            proofFileField.style.display = 'none';
            proofFileFieldSell.style.display = 'none';
            adminWalletAddressField.style.display = 'none';
            payment_method_container.style.display = "none";
            document.querySelector('input[name="wallet_address"]').required = false;
            document.querySelector('input[id="proof_file_sell"]').required = false;
            document.querySelector('input[id="proof_file_buy"]').required = false;
        }
    });

    document.getElementById('payment_method').addEventListener("change", function() {
        const type = this.value;
        if (type === 'wallet_balance') {
            proofFileField.style.display = 'none';
            document.querySelector('input[id="proof_file_buy"]').required = false;
        } else {
            proofFileField.style.display = 'block';
            document.querySelector('input[id="proof_file_buy"]').required = true;
        }
    });
</script>
@endpush