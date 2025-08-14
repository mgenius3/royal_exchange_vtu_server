@extends('layouts.default')

@section('title', 'Gift Card Transaction')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.giftcards') }}">Gift Cards</a></li>
                <li class="breadcrumb-item active">CREATE TRANSACTION</li>
            </ol>
            <h1 class="page-header mb-0">Create Gift Card Transaction</h1>
        </div>
        <div class="ms-auto">
            <span class="text-body">Welcome, {{ auth()->user()->name }}</span>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex align-items-center bg-none fw-semibold">
            New Transaction
        </div>
        <div class="card-body">
            <form action="{{ route('admin.giftcards.store-transaction') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">User</label>
                    <select class="form-select @error('user_id') is-invalid @enderror" name="user_id" required>
                        <option value="">Select a user</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Gift Card</label>
                    <select class="form-select @error('gift_card_id') is-invalid @enderror" name="gift_card_id" id="giftCard" required>
                        <option value="">Select a gift card</option>
                        @foreach ($giftCards as $giftCard)
                            <option value="{{ $giftCard->id }}" data-name="{{ $giftCard->name }}"
                                data-countries='{{ json_encode($giftCard->countries) }}'>{{ $giftCard->name }}</option>
                        @endforeach
                    </select>
                    @error('gift_card_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <!-- Hidden field to store gift card name -->
                <input type="hidden" name="gift_card_name" id="giftCardName">
                <div class="mb-3">
                    <label class="form-label">Country</label>
                    <select class="form-select @error('country') is-invalid @enderror" name="country" id="countrySelect" required>
                        <option value="">Select a country</option>
                    </select>
                    @error('country')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Transaction Type</label>
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
                    <label class="form-label">Gift Card Type</label>
                    <select class="form-select @error('gift_card_type') is-invalid @enderror" name="gift_card_type" id="giftCardType" required>
                        <option value="">Select type</option>
                        <option value="physical">Physical</option>
                        <option value="ecode">E-code</option>
                    </select>
                    @error('gift_card_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Gift Card Balance</label>
                    <input type="number" step="0.01" min="0.01" class="form-control @error('balance') is-invalid @enderror" name="balance" placeholder="e.g., 100.00" required>
                    @error('balance')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3" id="payment_method_container">
                    <label class="form-label">Payment Method</label>
                    <select class="form-select @error('payment_method') is-invalid @enderror" name="payment_method" id="paymentMethod" required>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="wallet_balance">Wallet Balance</option>
                    </select>
                    @error('payment_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3" id="proofFileField" style="display: none;">
                    <label class="form-label">Proof of Money Transfer (e.g., bank receipt)</label>
                    <input type="file" class="form-control @error('proof_file') is-invalid @enderror" name="proof_file" id="proof_file_buy" accept="image/*,application/pdf">
                    @error('proof_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3" id="proofFileFieldSell" style="display: none;">
                    <label class="form-label">Proof of Gift Card (e.g., gift card code or image)</label>
                    <input type="file" class="form-control @error('proof_file') is-invalid @enderror" name="proof_file_sell" id="proof_file_sell" accept="image/*,application/pdf">
                    @error('proof_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Transaction Hash <small>(Optional)</small></label>
                    <input type="text" class="form-control @error('tx_hash') is-invalid @enderror" name="tx_hash" placeholder="e.g., 0x123abc...">
                    @error('tx_hash')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Admin Notes <small>(Optional)</small></label>
                    <textarea class="form-control @error('admin_notes') is-invalid @enderror" name="admin_notes" rows="3" placeholder="e.g., Manual entry by admin"></textarea>
                    @error('admin_notes')
                    @error('admin_notes')
                        <div class="invalid-feedback">{{ $message }}
</div>
                    @enderror
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.giftcards') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-theme">Create Transaction</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@endsection

@push('scripts')
    <script>
        // Get DOM elements
        const proofFileField = document.getElementById('proofFileField');
        const proofFileFieldSell = document.getElementById('proofFileFieldSell');
        const paymentMethodContainer = document.getElementById('payment_method_container');
        const giftCardSelect = document.getElementById('giftCard');
        const countrySelect = document.getElementById('countrySelect');

        // Update gift card name and countries when a gift card is selected
        giftCardSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const giftCardName = selectedOption.getAttribute('data-name');
            const countries = selectedOption.dataset.countries ? JSON.parse(selectedOption.dataset.countries) : {};

            document.getElementById('giftCardName').value = giftCardName || '';

            // Populate country dropdown
            countrySelect.innerHTML = '<option value="">Select a country</option>';
            for (const country of Object.keys(countries)) {
                const option = document.createElement('option');
                option.value = country;
                option.textContent = country;
                countrySelect.appendChild(option);
            }
        });

        // Show/hide fields based on transaction type
        document.getElementById('transactionType').addEventListener('change', function() {
            const type = this.value;

            if (type === 'buy') {
                proofFileField.style.display = 'block';
                proofFileFieldSell.style.display = 'none';
                paymentMethodContainer.style.display = 'block';
                document.querySelector('input[id="proof_file_buy"]')').required = false;
                document.querySelector('input[id="proof_file_sell"]').required = false;
            } else if (type === 'sell') {
                proofFileField.style.display = 'none';
                proofFileFieldSell.style.display = 'block';
                paymentMethodContainer.style.display = 'none';
                document.querySelector('input[id="proof_file_buy"]')').required = false;
                document.querySelector('input[id="proof_file_sell"]').required = true;
            } else {
                proofFileField.style.display = 'none';
                proofFileFieldSell.style.display = 'none';
                paymentMethodContainer.style.display = 'none';
                document.querySelector('input[id="proof_file"]')').required = false;
                document.querySelector('input[id="proof_file_sell"]')').required = false;
            }
        });

        // Show/hide proof file field based on payment method
        document.getElementById('paymentMethod').addEventListener('change', function() {
            const method = this.value;
            if (method === 'wallet_balance') {
                proofFileField.style.display = 'none';
                document.querySelector('input[id="proof_file_buy"]')').required = false;
            } else if (method === 'bank_transfer') {
                proofFileField.style.display = 'block';
                document.querySelector('input[id="proof_file_buy"]')').required = true;
            }
        });

        // Trigger change event on page load to initialize fields
        giftCardSelect.dispatchEvent(new Event('change'));
    </script>
@endpush('other_scripts')