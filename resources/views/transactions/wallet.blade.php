@extends('layout.default')

@section('title', 'Wallet Transactions')

@section('content')
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 5px 0;
        }

        .page-subtitle {
            color: #6b7280;
            margin: 0;
        }

        .search-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin-bottom: 25px;
        }

        .search-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
            gap: 15px;
            align-items: end;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }

        .search-btn:hover {
            background: #2563eb;
        }

        .clear-btn {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }

        .clear-btn:hover {
            background: #e5e7eb;
            text-decoration: none;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            margin: 0;
            min-width: 800px;
        }

        .table th {
            background: #f9fafb;
            padding: 12px;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        .table td {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }

        .table tbody tr:hover {
            background: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        .text-success { color: #059669; font-weight: 500; }
        .text-danger { color: #dc2626; font-weight: 500; }

        .btn-danger {
            background: #dc2626;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .user-info {
            font-weight: 500;
        }

        .user-email {
            font-size: 12px;
            color: #6b7280;
        }

        .reference-code {
            font-family: monospace;
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
        }

        .date-info {
            white-space: nowrap;
        }

        .no-results {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 25px;
        }

        @media (max-width: 768px) {
            .search-grid {
                grid-template-columns: 1fr;
            }
            
            .table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .table {
                min-width: 900px;
            }
        }
    </style>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Wallet Transactions</h1>
            <p class="page-subtitle">Manage and monitor wallet transactions</p>
        </div>

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

        <div class="search-section">
            <div class="search-grid">
                <div class="form-group">
                    <label for="searchInput">Search</label>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search by name, email, or reference...">
                </div>
                
                <div class="form-group">
                    <label for="typeFilter">Type</label>
                    <select id="typeFilter" class="form-control">
                        <option value="">All Types</option>
                        <option value="deposit">Deposit</option>
                        <option value="withdrawal">Withdrawal</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="statusFilter">Status</label>
                    <select id="statusFilter" class="form-control">
                        <option value="">All Status</option>
                        <option value="success">Success</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="gatewayFilter">Gateway</label>
                    <select id="gatewayFilter" class="form-control">
                        <option value="">All Gateways</option>
                        <option value="paystack">Paystack</option>
                        <option value="flutterwave">Flutterwave</option>
                        <option value="bank">Bank</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 8px;">
                    <button type="button" class="clear-btn" onclick="clearFilters()">Clear</button>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="table" id="transactionsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Reference</th>
                        <th>Amount (NGN)</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Gateway</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $index => $transaction)
                        <tr class="transaction-row">
                            <td>{{ $transactions->firstItem() + $index }}</td>
                            <td>
                                <div class="user-info">{{ $transaction->user->name ?? 'N/A' }}</div>
                                <div class="user-email">{{ $transaction->user->email ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <span class="reference-code">{{ $transaction->reference }}</span>
                            </td>
                            <td>
                                @if ($transaction->amount >= 0)
                                    <span class="text-success">+{{ number_format($transaction->amount, 2) }}</span>
                                @else
                                    <span class="text-danger">{{ number_format($transaction->amount, 2) }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $transaction->type === 'deposit' ? 'success' : 'warning' }}">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $transaction->status === 'success' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td>{{ ucfirst($transaction->gateway) }}</td>
                            <td class="date-info">{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <form action="{{ route('admin.wallet-transactions.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transaction?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr id="noResults">
                            <td colspan="9" class="no-results">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="pagination">
                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <script>
        function performSearch() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const typeFilter = document.getElementById('typeFilter').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
            const gatewayFilter = document.getElementById('gatewayFilter').value.toLowerCase();
            
            const rows = document.querySelectorAll('.transaction-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const userName = cells[1].textContent.toLowerCase();
                const reference = cells[2].textContent.toLowerCase();
                const type = cells[4].textContent.toLowerCase();
                const status = cells[5].textContent.toLowerCase();
                const gateway = cells[6].textContent.toLowerCase();
                
                const matchesSearch = searchTerm === '' || 
                    userName.includes(searchTerm) || 
                    reference.includes(searchTerm);
                
                const matchesType = typeFilter === '' || type.includes(typeFilter);
                const matchesStatus = statusFilter === '' || status.includes(statusFilter);
                const matchesGateway = gatewayFilter === '' || gateway.includes(gatewayFilter);
                
                if (matchesSearch && matchesType && matchesStatus && matchesGateway) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            const noResultsRow = document.getElementById('noResults');
            if (noResultsRow) {
                noResultsRow.style.display = visibleCount === 0 && rows.length > 0 ? '' : 'none';
            }
        }
        
        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('typeFilter').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('gatewayFilter').value = '';
            performSearch();
        }
        
        // Add event listeners
        document.getElementById('searchInput').addEventListener('input', performSearch);
        document.getElementById('typeFilter').addEventListener('change', performSearch);
        document.getElementById('statusFilter').addEventListener('change', performSearch);
        document.getElementById('gatewayFilter').addEventListener('change', performSearch);
        
        // Initialize search on page load
        document.addEventListener('DOMContentLoaded', performSearch);
    </script>
@endsection