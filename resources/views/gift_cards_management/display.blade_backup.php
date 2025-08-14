@extends('layout.default')

@section('title', 'Gift Card Management')

@section('content')
    <div class="d-flex align-items-center mb-3">
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

    <div class="mb-md-3 mb-2 d-flex flex-wrap">
        {{-- <div class="me-4 mb-md-0 mb-2">
            <a href="#" class="text-decoration-none text-body d-flex align-items-center" data-bs-toggle="tooltip" title="Print this page">
                <i class="fa fa-print me-2 text-body text-opacity-50 my-n1"></i> Print
            </a>
        </div> --}}
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
                                    <div><a href="#" class="text-decoration-none text-body">{{ $giftCard->name }}</a>
                                    </div>
                                    <div class="text-body text-opacity-50 small">
                                        Category: {{ $giftCard->category }} | ${{ $giftCard->denomination }} | Stock:
                                        {{ $giftCard->stock ?? 'N/A' }}
                                    </div>
                                    {{-- <div class="text-body text-opacity-50 small">
                                        Ranges:
                                        @php
                                            $ranges = is_string($giftCard->ranges)
                                                ? json_decode($giftCard->ranges, true)
                                                : $giftCard->ranges;
                                        @endphp
                                        {{ $ranges && is_array($ranges) ? implode(', ', $ranges) : 'Not set' }}
                                    </div> --}}
                                </div>
                            </div>
                            <div class="col-lg-3 m-0 ps-lg-3">
                                Buy: {{ $giftCard->buy_rate }}(${{ $giftCard->denomination * $giftCard->buy_rate }}) |
                                Sell: {{ $giftCard->sell_rate }}(${{ $giftCard->denomination * $giftCard->sell_rate }})
                            </div>
                            <div class="col-lg-4 m-0 text-end">
                                <button
                                    class="btn btn-sm {{ $giftCard->is_enabled ? 'btn-success' : 'btn-secondary' }} toggle-status"
                                    data-id="{{ $giftCard->id }}" data-enabled="{{ $giftCard->is_enabled ? '1' : '0' }}"
                                    data-bs-toggle="tooltip" title="Toggle {{ $giftCard->name }} status">
                                    {{ $giftCard->is_enabled ? 'Enabled' : 'Disabled' }}
                                </button>
                                <a href="#" class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal"
                                    data-bs-target="#editGiftCardModal{{ $giftCard->id }}">Edit</a>
                                <form action="{{ route('admin.gift-cards.delete', $giftCard->id) }}" method="POST"
                                    style="display: inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this giftcard?');">
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
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Name</label>
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ $giftCard->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Category</label>
                                                <input type="text" class="form-control" name="category"
                                                    value="{{ $giftCard->category }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Denomination</label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    name="denomination" value="{{ $giftCard->denomination }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Stock</label>
                                                <input type="number" class="form-control" name="stock"
                                                    value="{{ $giftCard->stock }}" placeholder="e.g., 100" required>
                                            </div>
                                            {{-- <div class="mb-3">
                                                <label class="form-label">Ranges</label>
                                                <div id="edit-ranges-container-{{ $giftCard->id }}">
                                                    @php
                                                        $ranges = is_string($giftCard->ranges)
                                                            ? json_decode($giftCard->ranges, true)
                                                            : $giftCard->ranges;
                                                        $ranges = is_array($ranges) ? $ranges : [];
                                                    @endphp
                                                    @if (!empty($ranges))
                                                        @foreach ($ranges as $range)
                                                            <div class="range-input mb-2 d-flex align-items-center">
                                                                <input type="text" class="form-control me-2"
                                                                    name="ranges[]" value="{{ $range }}"
                                                                    placeholder="e.g., $100 - $200">
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                    onclick="this.parentElement.remove()">Remove</button>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="range-input mb-2 d-flex align-items-center">
                                                            <input type="text" class="form-control me-2"
                                                                name="ranges[]" placeholder="e.g., 100 - 200">
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="this.parentElement.remove()">Remove</button>
                                                        </div>
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-outline-primary mt-2"
                                                    onclick="addRange('edit-ranges-container-{{ $giftCard->id }}')">Add
                                                    Range</button>
                                            </div> --}}
                                            <div class="mb-3">
                                                <label class="form-label">Image</label>
                                                <input type="file" class="form-control" name="image"
                                                    accept="image/*">
                                                @if ($giftCard->image)
                                                    <img src="{{ asset($giftCard->image) }}" alt="{{ $giftCard->name }}"
                                                        class="mt-2" style="max-width: 100px;">
                                                @endif
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
                                <th>Quantity</th>
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
                                    <td>{{ $transaction->amount }}</td>
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
                    <form action="{{ route('admin.gift-cards.update-rates') }}" method="POST" id="rateForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Select Gift Card</label>
                            <select class="form-select" name="gift_card_id" id="giftCardSelect">
                                @forelse ($giftCards as $giftCard)
                                    <option value="{{ $giftCard->id }}" data-buy-rate="{{ $giftCard->buy_rate }}"
                                        data-sell-rate="{{ $giftCard->sell_rate }}">{{ $giftCard->name }}
                                        ({{ $giftCard->denomination }})
                                    </option>
                                @empty
                                    <option value="">No gift cards available</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Currency</label>
                            <input type="text" class="form-control" name="currency" value="USD" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Buy Rate <small>(Current: <span
                                        id="currentBuyRate">{{ $giftCards->isNotEmpty() ? $giftCards->first()->buy_rate : 'N/A' }}</span>)</small></label>
                            <input type="number" step="0.01" class="form-control" name="buy_rate" id="buyRate"
                                placeholder="e.g., 0.80" {{ $giftCards->isEmpty() ? 'disabled' : '' }} required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sell Rate <small>(Current: <span
                                        id="currentSellRate">{{ $giftCards->isNotEmpty() ? $giftCards->first()->sell_rate : 'N/A' }}</span>)</small></label>
                            <input type="number" step="0.01" class="form-control" name="sell_rate" id="sellRate"
                                placeholder="e.g., 0.95" {{ $giftCards->isEmpty() ? 'disabled' : '' }} required>
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
                        <strong>Completed Transactions:</strong> {{ $transactions->where('status', 'completed')->count() }}
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
                <form action="{{ route('admin.gift-cards.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" name="category" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Denomination</label>
                            <input type="number" step="0.01" min="0" class="form-control"
                                name="denomination" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Buy Rate</label>
                            <input type="number" step="0.01" class="form-control" name="buy_rate" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sell Rate</label>
                            <input type="number" step="0.01" class="form-control" name="sell_rate" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stock" placeholder="e.g., 100" required>
                        </div>
                        {{-- <div class="mb-3">
                            <label class="form-label">Ranges</label>
                            <div id="add-ranges-container">
                                <div class="range-input mb-2 d-flex align-items-center">
                                    <input type="text" class="form-control me-2" name="ranges[]"
                                        placeholder="e.g., $100 - $200">
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="this.parentElement.remove()">Remove</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary mt-2"
                                onclick="addRange('add-ranges-container')">Add Range</button>
                        </div> --}}
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
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
        // Real-time Rate Preview
        const giftCardSelect = document.getElementById('giftCardSelect');
        const currentBuyRate = document.getElementById('currentBuyRate');
        const currentSellRate = document.getElementById('currentSellRate');
        giftCardSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            currentBuyRate.textContent = selectedOption.dataset.buyRate || 'N/A';
            currentSellRate.textContent = selectedOption.dataset.sellRate || 'N/A';
        });

        // Function to add a new range input
        // function addRange(containerId) {
        //     const container = document.getElementById(containerId);
        //     const newInput = document.createElement('div');
        //     newInput.className = 'range-input mb-2 d-flex align-items-center';
        //     newInput.innerHTML = `
        //         <input type="text" class="form-control me-2" name="ranges[]" placeholder="e.g., $100 - $200">
        //         <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">Remove</button>
        //     `;
        //     container.appendChild(newInput);
        // }

        // Initialize Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
@endsection

@push('other_scripts')
    <script src="{{ asset('assets/js/gift_card_management/toggle_status.js') }}"></script>
@endpush
