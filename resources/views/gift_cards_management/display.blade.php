@extends('layout.default')

@section('title', 'Gift Cards')

@section('content')
    <div class="container">
        <div class="d-flex align-items-center">
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;">PAGES</a></li>
                    <li class="breadcrumb-item active">GIFT CARD MANAGEMENT</li>
                </ol>
                <h1 class="page-header mb-0">Gift Card Management</h1>
            </div>
            <div class="ms-auto">
                <span class="text-body">Welcome, {{ Auth::user()->name }}</span>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-md-3 mb-2 d-flex flex-wrap">
            <div class="me-4 mb-md-0 mb-2">
                <a href="#" class="text-decoration-none text-body d-flex align-items-center" data-bs-toggle="modal"
                    data-bs-target="#addGiftCardModal">
                    <i class="fa fa-plus me-2 text-body text-opacity-50 my-n1"></i> Add New Gift Card
                </a>
            </div>
            <div class="me-4 mb-md-0 mb-2 dropdown-toggle">
                <a href="#" data-bs-toggle="dropdown"
                    class="text-decoration-none text-body d-inline-flex align-items-center">
                    <i class="fa fa-gear me-2 text-body text-opacity-50 my-n1"></i> More Actions
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.gift-cards.create-transaction') }}">Create New
                        Transaction</a>
                </div>
            </div>
        </div>

        <div class="row gx-4">
            <div class="col-xl-8">
                <!-- Gift Card Catalog -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center bg-none fw-semibold">
                        Gift Card Catalog ({{ $giftCards->count() }})
                        <a href="#"
                            class="ms-auto text-decoration-none text-body text-opacity-50 fs-13px d-flex align-items-center"
                            data-bs-toggle="modal" data-bs-target="#addGiftCardModal">
                            <i class="fa fa-plus me-2 my-n1"></i> Add Gift Card
                        </a>
                    </div>
                    <div class="card-body">
                        @foreach ($giftCards as $giftCard)
                            <div class="row align-items-center">
                                <div class="col-lg-5 d-flex align-items-center">
                                    <div
                                        class="h-65px w-65px d-flex align-items-center justify-content-center position-relative bg-body rounded p-2">
                                        <img src="{{ $giftCard->image ? $giftCard->image : asset('assets/img/giftcard/default.png') }}"
                                            alt="{{ $giftCard->name }}" class="mw-100 mh-100">
                                    </div>
                                    <div class="ps-3 flex-1">
                                        <div><a href="#"
                                                class="text-decoration-none text-body">{{ $giftCard->name }}</a>
                                        </div>
                                        <div class="text-body text-opacity-50 small">
                                            Category: {{ $giftCard->category }} | Stock: {{ $giftCard->stock ?? 'N/A' }}
                                        </div>
                                        <div class="text-body text-opacity-50 small">
                                            Countries:
                                            @php
                                                $countries = is_string($giftCard->countries)
                                                    ? json_decode($giftCard->countries, true)
                                                    : $giftCard->countries;
                                            @endphp
                                            {{ $countries && is_array($countries) ? implode(', ', array_keys($countries)) : 'Not set' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 m-0 ps-lg-3">
                                    @if ($countries)
                                        Rates: Varies by country
                                        <ul>
                                            @foreach ($countries as $country => $rates)
                                                <li>{{ $rates['name'] }}: Buy {{ $rates['buy_rate'] }} | Sell
                                                    {{ $rates['sell_rate'] }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        No rates set
                                    @endif
                                </div>
                                <div class="col-lg-4 m-0 text-end">
                                    <button
                                        class="btn btn-sm {{ $giftCard->is_enabled ? 'btn-success' : 'btn-secondary' }} toggle-status"
                                        data-id="{{ $giftCard->id }}"
                                        data-enabled="{{ $giftCard->is_enabled ? '1' : '0' }}" data-bs-toggle="tooltip"
                                        title="Toggle {{ $giftCard->name }} status">
                                        {{ $giftCard->is_enabled ? 'Enabled' : 'Disabled' }}
                                    </button>
                                    <a href="#" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal"
                                        data-bs-target="#editGiftCardModal{{ $giftCard->id }}">Edit</a>
                                    <form action="{{ route('admin.gift-cards.delete', $giftCard->id) }}" method="POST"
                                        style="display: inline;"
                                        onsubmit="return confirm('Are you sure you want to delete this gift card?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                            @if (!$loop->last)
                                <hr class="opacity-1 my-4">
                            @endif

                            <!-- Edit Modal for Each Gift Card -->
                            <div class="modal fade" id="editGiftCardModal{{ $giftCard->id }}" tabindex="-1"
                                aria-labelledby="editGiftCardModalLabel{{ $giftCard->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editGiftCardModalLabel{{ $giftCard->id }}">Edit
                                                {{ $giftCard->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.gift-cards.update', $giftCard->id) }}" method="POST"
                                            enctype="multipart/form-data"
                                            onsubmit="return confirm('Are you sure you want to save changes?');">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Name</label>
                                                    <input type="text"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        name="name" value="{{ old('name', $giftCard->name) }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Category</label>
                                                    <input type="text"
                                                        class="form-control @error('category') is-invalid @enderror"
                                                        name="category" value="{{ old('category', $giftCard->category) }}"
                                                        required>
                                                    @error('category')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Stock</label>
                                                    <input type="number"
                                                        class="form-control @error('stock') is-invalid @enderror"
                                                        name="stock" value="{{ old('stock', $giftCard->stock) }}"
                                                        placeholder="e.g., 100" required>
                                                    @error('stock')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Countries</label>
                                                    <div id="edit-countries-container-{{ $giftCard->id }}">
                                                        @php
                                                            $countries = is_string($giftCard->countries)
                                                                ? json_decode($giftCard->countries, true)
                                                                : $giftCard->countries;
                                                            $countries = is_array($countries) ? $countries : [];
                                                        @endphp
                                                        @foreach ($countries as $country => $rates)
                                                            <div class="country-input mb-2"
                                                                data-index="{{ $loop->index }}">
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <input type="text"
                                                                        class="form-control me-2 @error('countries.' . $loop->index . '.name') is-invalid @enderror"
                                                                        name="countries[{{ $loop->index }}][name]"
                                                                        value="{{ old('countries.' . $loop->index . '.name', $rates['name']) }}"
                                                                        placeholder="e.g., US" required>
                                                                    <button type="button" class="btn btn-danger btn-sm"
                                                                        onclick="this.closest('.country-input').remove()">Remove</button>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <input type="number" step="0.01"
                                                                        class="form-control me-2 @error('countries.' . $loop->index . '.buy_rate') is-invalid @enderror"
                                                                        name="countries[{{ $loop->index }}][buy_rate]"
                                                                        value="{{ old('countries.' . $loop->index . '.buy_rate', $rates['buy_rate']) }}"
                                                                        placeholder="Buy Rate" required>
                                                                    <input type="number" step="0.01"
                                                                        class="form-control @error('countries.' . $loop->index . '.sell_rate') is-invalid @enderror"
                                                                        name="countries[{{ $loop->index }}][sell_rate]"
                                                                        value="{{ old('countries.' . $loop->index . '.sell_rate', $rates['sell_rate']) }}"
                                                                        placeholder="Sell Rate" required>
                                                                </div>
                                                                @error('countries.' . $loop->index . '.name')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                                @error('countries.' . $loop->index . '.buy_rate')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                                @error('countries.' . $loop->index . '.sell_rate')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button type="button" class="btn btn-outline-primary mt-2"
                                                        onclick="addCountry('edit-countries-container-{{ $giftCard->id }}')">Add
                                                        Country</button>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Image</label>
                                                    <input type="file"
                                                        class="form-control @error('image') is-invalid @enderror"
                                                        name="image" accept="image/*">
                                                    @if ($giftCard->image)
                                                        <img src="{{ $giftCard->image }}" alt="{{ $giftCard->name }}"
                                                            class="mt-2" style="max-width: 100px;">
                                                    @endif
                                                    @error('image')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-theme">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer bg-none d-flex p-3">
                        <a href="#" class="btn btn-default fw-semibold fs-13px ms-auto">View All</a>
                    </div>
                </div>

                <!-- Transaction Oversight -->
                <div class="card">
                    <div class="card-header d-flex align-items-center bg-none fw-semibold">
                        Recent Transactions ({{ $transactions->count() }})
                        <a href="#" class="ms-auto text-decoration-none fs-13px text-body text-opacity-50">
                            <i class="fa fa-filter me-1"></i> Filter
                        </a>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm m-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Gift Card</th>
                                    <th>Type</th>
                                    <th>Gift Card Type</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->user->name }}</td>
                                        <td><a href="{{ route('admin.gift-cards.transaction', $transaction->id) }}"
                                                class="text-decoration-none">{{ $transaction->giftCard->name }}</a></td>
                                        <td>{{ ucfirst($transaction->type) }}</td>
                                        <td>{{ ucfirst($transaction->gift_card_type) }}</td>
                                        <td>{{ $transaction->balance }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $transaction->status == 'completed' ? 'bg-success' : ($transaction->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-none d-flex p-3">
                        <a href="/gift-cards/all-transactions" class="btn btn-theme ms-auto">View All Transactions</a>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <!-- Rate Management -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center bg-none fw-semibold">
                        Rate Management
                        <a href="#" class="ms-auto text-decoration-none fs-13px text-body text-opacity-50"
                            id="refreshRates">Refresh</a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.gift-cards.update-rates') }}" method="POST" id="rateForm"
                            onsubmit="return confirm('Are you sure you want to update rates?');">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Select Gift Card</label>
                                <select class="form-select @error('gift_card_id') is-invalid @enderror"
                                    name="gift_card_id" id="giftCardSelect" required>
                                    <option>Select Giftcard</option>
                                    @forelse ($giftCards as $giftCard)
                                        <option value="{{ $giftCard->id }}"
                                            data-countries='{{ json_encode($giftCard->countries) }}'>
                                            {{ $giftCard->name }}
                                        </option>
                                    @empty
                                        <option value="">No gift cards available</option>
                                    @endforelse
                                </select>
                                @error('gift_card_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Countries</label>
                                <div id="rate-countries-container">
                                    <!-- Countries will be populated by JavaScript -->
                                </div>
                                <button type="button" class="btn btn-outline-primary mt-2"
                                    onclick="addRateCountry()">Add Country</button>
                            </div>
                            <button type="submit" class="btn btn-theme" data-bs-toggle="tooltip"
                                title="Update rates for selected gift card"
                                {{ $giftCards->isEmpty() ? 'disabled' : '' }}>Update Rates</button>
                        </form>
                    </div>
                </div>

                <!-- Analytics -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center bg-none fw-semibold">
                        Quick Analytics
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Total Gift Cards:</strong> {{ $giftCards->count() }}
                        </div>
                        <div class="mb-2">
                            <strong>Active Cards:</strong> {{ $giftCards->where('is_enabled', true)->count() }}
                        </div>
                        <div class="mb-2">
                            <strong>Total Transactions:</strong> {{ $transactions->count() }}
                        </div>
                        <div>
                            <strong>Completed Transactions:</strong>
                            {{ $transactions->where('status', 'completed')->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Gift Card Modal -->
        <div class="modal fade" id="addGiftCardModal" tabindex="-1" aria-labelledby="addGiftCardModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addGiftCardModalLabel">Add New Gift Card</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.gift-cards.store') }}" method="POST" enctype="multipart/form-data"
                        onsubmit="return confirm('Are you sure you want to add this gift card?');">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <input type="text" class="form-control @error('category') is-invalid @enderror"
                                    name="category" value="{{ old('category') }}" required>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Stock</label>
                                <input type="number" class="form-control @error('stock') is-invalid @enderror"
                                    name="stock" value="{{ old('stock') }}" placeholder="e.g., 100" required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Countries</label>
                                <div id="add-countries-container">
                                    <div class="country-input mb-2" data-index="0">
                                        <div class="d-flex align-items-center mb-1">
                                            <input type="text"
                                                class="form-control me-2 @error('countries.0.name') is-invalid @enderror"
                                                name="countries[0][name]" value="{{ old('countries.0.name') }}"
                                                placeholder="e.g., US" required>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="this.closest('.country-input').remove()">Remove</button>
                                        </div>
                                        <div class="d-flex">
                                            <input type="number" step="0.01"
                                                class="form-control me-2 @error('countries.0.buy_rate') is-invalid @enderror"
                                                name="countries[0][buy_rate]" value="{{ old('countries.0.buy_rate') }}"
                                                placeholder="Buy Rate" required>
                                            <input type="number" step="0.01"
                                                class="form-control @error('countries.0.sell_rate') is-invalid @enderror"
                                                name="countries[0][sell_rate]" value="{{ old('countries.0.sell_rate') }}"
                                                placeholder="Sell Rate" required>
                                        </div>
                                        @error('countries.0.name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @error('countries.0.buy_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @error('countries.0.sell_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary mt-2"
                                    onclick="addCountry('add-countries-container')">Add Country</button>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Image</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    name="image" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-theme">Add Gift Card</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- JavaScript for Interactivity -->
        <script>
            // Real-time Rate Preview for Rate Management
            const giftCardSelect = document.getElementById('giftCardSelect');
            const rateCountriesContainer = document.getElementById('rate-countries-container');

            giftCardSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const countries = selectedOption.dataset.countries ? JSON.parse(selectedOption.dataset.countries) : [];
                const mappedCountries = JSON.parse(countries).map(item => ({
                    name: item.name,
                    buy_rate: parseFloat(item.buy_rate),
                    sell_rate: parseFloat(item.sell_rate)
                }));


                rateCountriesContainer.innerHTML = '';

                let index = 0;
                for (const country of mappedCountries) {
                    const div = document.createElement('div');
                    div.className = 'country-input mb-2';
                    div.dataset.index = index;
                    console.log(index);
                    div.innerHTML = `
                        <div class="d-flex align-items-center mb-1">
                            <input type="text" class="form-control me-2" name="countries[${index}][name]" value="${country.name}" placeholder="e.g., US" required>
                            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.country-input').remove()">Remove</button>
                        </div>
                        <div class="d-flex">
                            <input type="number" step="0.01" class="form-control me-2" name="countries[${index}][buy_rate]" value="${country.buy_rate}" placeholder="Buy Rate" required>
                            <input type="number" step="0.01" class="form-control" name="countries[${index}][sell_rate]" value="${country.sell_rate}" placeholder="Sell Rate" required>
                        </div>
                    `;
                    rateCountriesContainer.appendChild(div);
                    index++;
                }
            });

            // Function to add a new country input
            function addCountry(containerId) {
                const container = document.getElementById(containerId);
                const existingInputs = container.querySelectorAll('.country-input');
                const newIndex = existingInputs.length;

                const newInput = document.createElement('div');
                newInput.className = 'country-input mb-2';
                newInput.dataset.index = newIndex;
                newInput.innerHTML = `
                    <div class="d-flex align-items-center mb-1">
                        <input type="text" class="form-control me-2" name="countries[${newIndex}][name]" placeholder="e.g., US" required>
                        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.country-input').remove()">Remove</button>
                    </div>
                    <div class="d-flex">
                        <input type="number" step="0.01" class="form-control me-2" name="countries[${newIndex}][buy_rate]" placeholder="Buy Rate" required>
                        <input type="number" step="0.01" class="form-control" name="countries[${newIndex}][sell_rate]" placeholder="Sell Rate" required>
                    </div>
                `;
                container.appendChild(newInput);
            }

            // Function to add a new country input for Rate Management
            function addRateCountry() {
                const container = document.getElementById('rate-countries-container');
                const existingInputs = container.querySelectorAll('.country-input');
                const newIndex = existingInputs.length;

                const newInput = document.createElement('div');
                newInput.className = 'country-input mb-2';
                newInput.dataset.index = newIndex;
                newInput.innerHTML = `
                    <div class="d-flex align-items-center mb-1">
                        <input type="text" class="form-control me-2" name="countries[${newIndex}][name]" placeholder="e.g., US" required>
                        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.country-input').remove()">Remove</button>
                    </div>
                    <div class="d-flex">
                        <input type="number" step="0.01" class="form-control me-2" name="countries[${newIndex}][buy_rate]" placeholder="Buy Rate" required>
                        <input type="number" step="0.01" class="form-control" name="countries[${newIndex}][sell_rate]" placeholder="Sell Rate" required>
                    </div>
                `;
                container.appendChild(newInput);
            }

            // Initialize Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Trigger change event on page load to populate rate management fields
            giftCardSelect.dispatchEvent(new Event('change'));

            // Prevent form submission if no countries are added
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const countryInputs = this.querySelectorAll('.country-input');
                    if (countryInputs.length === 0) {
                        e.preventDefault();
                        alert('Please add at least one country with rates.');
                    }
                });
            });
        </script>
    </div>
@endsection

@push('other_scripts')
    <script src="{{ asset('assets/js/gift_card_management/toggle_status.js') }}"></script>
@endpush