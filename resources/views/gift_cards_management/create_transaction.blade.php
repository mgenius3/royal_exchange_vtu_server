@extends('layout.default')

@section('title', 'Create Gift Card Transaction')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.gift-cards') }}">Gift Cards</a></li>
                <li class="breadcrumb-item active">CREATE TRANSACTION</li>
            </ol>
            <h1 class="page-header mb-0">Create Gift Card Transaction</h1>
        </div>
        <div class="ms-auto">
            <span class="text-body">Welcome, {{ Auth::user()->name }}</span>
        </div>
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
        <div class="card-header d-flex align-items-center bg-none fw-semibold">
            New Transaction
        </div>
        <div class="card-body">
            <form action="{{ route('admin.gift-cards.store-transaction') }}" method="POST" enctype="multipart/form-data"
                id="transactionForm">
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
                    <select class="form-select @error('gift_card_id') is-invalid @enderror" name="gift_card_id"
                        id="giftCard" required>
                        <option value="">Select a gift card</option>
                        @foreach ($giftCards as $giftCard)
                            <option value="{{ $giftCard->id }}" data-name="{{ $giftCard->name }}"
                                data-countries='{{ json_encode($giftCard->countries) }}'
                                data-denomination="{{ $giftCard->denomination }}">
                                {{ $giftCard->name }} (${{ $giftCard->denomination }})
                            </option>
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
                    <select class="form-select @error('country') is-invalid @enderror" name="country" id="country"
                        required>
                        <option value="">Select a country</option>
                        <!-- Populated by JavaScript -->
                    </select>
                    @error('country')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Transaction Type</label>
                    <select class="form-select @error('type') is-invalid @enderror" name="type" id="transactionType"
                        required>
                        <option value="">Select type</option>
                        <option value="buy">Buy</option>
                        <option value="sell">Sell</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3" id="giftCardTypeContainer" style="display: none;">
                    <label class="form-label">Gift Card Type</label>
                    <select class="form-select @error('gift_card_type') is-invalid @enderror" name="gift_card_type"
                        id="giftCardType">
                        <option value="">Select gift card type</option>
                        <option value="physical">Physical</option>
                        <option value="ecode">E-code</option>
                    </select>
                    @error('gift_card_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                {{-- <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" step="1" min="1"
                        class="form-control @error('quantity') is-invalid @enderror" name="quantity" id="quantity"
                        placeholder="e.g., 1" value="1" required>
                    @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}
                <div class="mb-3">
                    <label class="form-label">Balance ($)</label>
                    <input type="number" step="0.01" min="0.01"
                        class="form-control @error('balance') is-invalid @enderror" name="balance" id="balance"
                        placeholder="e.g., 100.00" required>
                    <small class="text-muted" id="balanceHint"></small>
                    @error('balance')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3" id="paymentMethodContainer" style="display: none;">
                    <label class="form-label">Payment Method</label>
                    <select class="form-select @error('payment_method') is-invalid @enderror" name="payment_method"
                        id="paymentMethod">
                        <option> </option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="wallet_balance">Wallet Balance</option>
                    </select>
                    @error('payment_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3" id="proofFileField" style="display: none;">
                    <label class="form-label" id="proofFileLabel"></label>
                    <input type="file" class="form-control @error('proof_file') is-invalid @enderror"
                        name="proof_file" id="proofFile" accept="image/*,application/pdf">
                    @error('proof_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3" id="ecodeField" style="display: none;">
                    <label class="form-label">E-code</label>
                    <input type="text" class="form-control @error('ecode') is-invalid @enderror" name="ecode"
                        id="ecode" placeholder="e.g., XXXX-XXXX-XXXX-XXXX">
                    @error('ecode')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Transaction Hash <small>(Optional)</small></label>
                    <input type="text" class="form-control @error('tx_hash') is-invalid @enderror" name="tx_hash"
                        placeholder="e.g., 0x123abc...">
                    @error('tx_hash')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Admin Notes <small>(Optional)</small></label>
                    <textarea class="form-control @error('admin_notes') is-invalid @enderror" name="admin_notes" rows="3"
                        placeholder="e.g., Manual entry by admin"></textarea>
                    @error('admin_notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.gift-cards') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-theme">Create Transaction</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('other_scripts')
    <script>
        // Get DOM elements
        const giftCardSelect = document.getElementById('giftCard');
        const countrySelect = document.getElementById('country');
        const transactionTypeSelect = document.getElementById('transactionType');
        const giftCardTypeContainer = document.getElementById('giftCardTypeContainer');
        const giftCardTypeSelect = document.getElementById('giftCardType');
        // const quantityInput = document.getElementById('quantity');
        const balanceInput = document.getElementById('balance');
        const balanceHint = document.getElementById('balanceHint');
        const paymentMethodContainer = document.getElementById('paymentMethodContainer');
        const paymentMethodSelect = document.getElementById('paymentMethod');
        const proofFileField = document.getElementById('proofFileField');
        const proofFileInput = document.getElementById('proofFile');
        const proofFileLabel = document.getElementById('proofFileLabel');
        const ecodeField = document.getElementById('ecodeField');
        const ecodeInput = document.getElementById('ecode');
        const giftCardNameInput = document.getElementById('giftCardName');

        // Update gift card name and countries when a gift card is selected
        giftCardSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            giftCardNameInput.value = selectedOption.getAttribute('data-name') || '';
            const denomination = parseFloat(selectedOption.getAttribute('data-denomination')) || 0;

            // Reset dependent fields
            countrySelect.innerHTML = '<option value="">Select a country</option>';
            transactionTypeSelect.value = '';
            giftCardTypeSelect.value = '';
            // quantityInput.value = 1;
            balanceInput.value = '';
            balanceHint.textContent = '';
            toggleFields();

            let countries = [];
            let mappedCountries = [];
            countries = selectedOption.dataset.countries ? JSON.parse(selectedOption.dataset.countries) : [];

            mappedCountries = JSON.parse(countries).map(item => ({
                name: item.name,
                buy_rate: parseFloat(item.buy_rate),
                sell_rate: parseFloat(item.sell_rate)
            }));

            
            let index = 0
            for (const country of mappedCountries) {
                console.log(country.name);
                const option = document.createElement('option');
                option.value = index;
                option.textContent = country.name;
                option.dataset.buyRate = country.buy_rate;
                option.dataset.sellRate = country.sell_rate;
                countrySelect.appendChild(option);
            }
            updateBalanceHint();
        });

        // Update fields when transaction type or gift card type changes
        transactionTypeSelect.addEventListener('change', function() {
            giftCardTypeSelect.value = '';
            toggleFields();
            updateBalanceHint();
        });

        giftCardTypeSelect.addEventListener('change', toggleFields);
        paymentMethodSelect.addEventListener('change', toggleFields);
        countrySelect.addEventListener('change', updateBalanceHint);
        quantityInput.addEventListener('input', updateBalanceHint);

        // Toggle visibility and required attributes
        function toggleFields() {
            const transactionType = transactionTypeSelect.value;
            const giftCardType = giftCardTypeSelect.value;
            const paymentMethod = paymentMethodSelect.value;

            // Reset fields
            giftCardTypeContainer.style.display = 'none';
            paymentMethodContainer.style.display = 'none';
            proofFileField.style.display = 'none';
            ecodeField.style.display = 'none';
            giftCardTypeSelect.required = false;
            paymentMethodSelect.required = false;
            proofFileInput.required = false;
            ecodeInput.required = false;
            proofFileLabel.textContent = '';

            if (transactionType === 'buy') {
                paymentMethodContainer.style.display = 'block';
                paymentMethodSelect.required = true;

                if (paymentMethod === 'bank_transfer') {
                    proofFileField.style.display = 'block';
                    proofFileInput.required = true;
                    proofFileLabel.textContent = 'Proof of Money Transfer (e.g., bank receipt)';
                } else {
                    proofFileField.style.display = 'none';
                    proofFileInput.required = false;
                }
            } else if (transactionType === 'sell') {
                giftCardTypeContainer.style.display = 'block';
                giftCardTypeSelect.required = true;

                if (giftCardType === 'physical') {
                    proofFileField.style.display = 'block';
                    proofFileInput.required = true;
                    proofFileLabel.textContent = 'Image of Physical Gift Card';
                    ecodeField.style.display = 'none';
                    ecodeInput.required = false;
                } else if (giftCardType === 'ecode') {
                    ecodeField.style.display = 'block';
                    ecodeInput.required = true;
                    proofFileField.style.display = 'none';
                    proofFileInput.required = false;
                }
            }
        }

        // Update balance hint
        function updateBalanceHint() {
            balanceHint.textContent = '';
            const quantity = parseInt(quantityInput.value) || 1;
            const country = countrySelect.value;
            const type = transactionTypeSelect.value;
            const selectedGiftCard = giftCardSelect.options[giftCardSelect.selectedIndex];
            const denomination = parseFloat(selectedGiftCard?.getAttribute('data-denomination') || 0);

            if (country && type && selectedGiftCard && denomination) {
                const selectedCountryOption = countrySelect.options[countrySelect.selectedIndex];
                const rate = type === 'buy' ?
                    parseFloat(selectedCountryOption.dataset.buyRate) :
                    parseFloat(selectedCountryOption.dataset.sellRate);

                if (rate) {
                    const totalValue = quantity * denomination;
                    const effectiveValue = totalValue * rate;
                    balanceHint.textContent =
                        `Expected balance: $${totalValue.toFixed(2)} (Effective: $${effectiveValue.toFixed(2)} at ${rate} rate)`;
                    balanceInput.value = totalValue.toFixed(2);
                }
            }
        }

        // Client-side form validation
        document.getElementById('transactionForm').addEventListener('submit', function(e) {
            const transactionType = transactionTypeSelect.value;
            const giftCardType = giftCardTypeSelect.value;

            if (!countrySelect.value) {
                e.preventDefault();
                countrySelect.classList.add('is-invalid');
                countrySelect.nextElementSibling.textContent = 'Please select a country.';
            }
            if (transactionType === 'sell' && !giftCardType) {
                e.preventDefault();
                giftCardTypeSelect.classList.add('is-invalid');
                giftCardTypeSelect.nextElementSibling.textContent = 'Please select a gift card type.';
            }
            if (transactionType === 'sell' && giftCardType === 'ecode' && !ecodeInput.value) {
                e.preventDefault();
                ecodeInput.classList.add('is-invalid');
                ecodeInput.nextElementSibling.textContent = 'Please enter the e-code.';
            }
        });
    </script>
@endpush
