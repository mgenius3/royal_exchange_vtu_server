@extends('layout.default')

@section('title', 'User Details')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:;">PAGES</a></li>
                <li class="breadcrumb-item"><a href="/users">USERS</a></li>
                <li class="breadcrumb-item active">USER DETAILS</li>
            </ol>
            <h4 class="mb-0">WALLET BALANCE - &#8358;{{ number_format($user['wallet_balance'], 2) }}</h4>
        </div>
    </div>

    {{-- <div class="mb-md-3 mb-2 d-flex flex-wrap">
        <div class="me-4 mb-md-0 mb-2">
            <a href="#" class="text-decoration-none text-body d-flex align-items-center">
                <i class="fa fa-ban me-2 text-body text-opacity-50 my-n1"></i> Delete User
            </a>
        </div>
        <div class="me-4 mb-md-0 mb-2">
            <a href="#" class="text-decoration-none text-body d-flex align-items-center">
                <i class="fa fa-pause me-2 text-body text-opacity-50 my-n1"></i> Suspend User
            </a>
        </div>
        <div class="me-4 mb-md-0 mb-2 dropdown-toggle">
            <a href="#" data-bs-toggle="dropdown" class="text-decoration-none text-body d-inline-flex align-items-center">
                <i class="fa fa-gear me-2 text-body text-opacity-50 my-n1"></i> More Actions
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="#">Reset Password</a>
                <a class="dropdown-item" href="#">Export Data</a>
                <div role="separator" class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">Delete User</a>
            </div>
        </div>
    </div> --}}

    <div class="row gx-4">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center bg-none fw-semibold">
                    User Information
                    <a href="{{ route('users.edit', $user['id']) }}" class="ms-auto text-decoration-none text-body text-opacity-50 fs-13px d-flex align-items-center">
                        <i class="fa fa-edit me-2 my-n1"></i> Edit
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ID</label>
                                <input type="text" class="form-control" value="{{ $user['id'] }}" data-id="{{ $user['id']}}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" value="{{ $user['name'] }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="{{ $user['email'] }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" value="{{ $user['phone'] ?? 'N/A' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- <div class="mb-3">
                                <label class="form-label">Wallet Address</label>
                                <input type="text" class="form-control" value="{{ $user['wallet_addresses'] ?? 'N/A' }}" id="walletAddress">
                                <button class="btn btn-theme mt-2" id = "updateWalletAddressButton">Update Wallet Address</button>
                            </div> --}}
                            <div class="mb-3">
                                <label class="form-label">Fund Wallet</label>
                                <input type="number" class="form-control" id="fundAmount" placeholder="Enter amount to fund">
                                <button class="btn btn-success mt-2" id="fundWalletBalanceButton">Fund Wallet</button>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deduct from Wallet</label>
                                <input type="number" class="form-control" id="deductAmount" placeholder="Enter amount to deduct">
                                <button class="btn btn-danger mt-2" id="deductWalletBalanceButton">Deduct from Wallet</button>
                            </div>
                            {{-- <div class="mb-3">
                                <label class="form-label">Wallet Balance</label>
                                <input type="text" class="form-control" value="#{{ number_format($user['wallet_balance'], 2) }}" id="walletBalance">
                                <button class="btn btn-theme mt-2" id="updateWalletBalanceButton">Update Wallet Balance</button>
                            </div> --}}
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" id="userStatus">
                                    <option value="active" {{ $user['status'] === 'active' ? 'selected' : '' }}>Active</option>
                                    {{-- <option value="banned" {{ $user['status'] === 'banned' ? 'selected' : '' }}>Banned</option> --}}
                                    <option value="suspended" {{ $user['status'] === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                <button class="btn btn-theme mt-2" id="updateUserStatusButton">Update Status</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex align-items-center bg-none fw-semibold">
                    Activity Log
                    <a href="#" class="ms-auto text-decoration-none fs-13px text-body text-opacity-50">
                        <i class="fa fa-history me-1 fa-lg"></i> View Full Log
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm m-0">
                        <tbody>
                            <tr>
                                <td class="w-150px">Date Joined</td>
                                <td>{{ \Carbon\Carbon::parse($user['date_joined'])->format('M d, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <td>Last Login</td>
                                <td>{{ $user['last_login'] ? \Carbon\Carbon::parse($user['last_login'])->format('M d, Y h:i A') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Referral Code</td>
                                <td>{{ $user['referral_code'] ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center bg-none fw-semibold">
                    Update Password
                </div>
                <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="text" class="form-control"  id="password">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control"  id="confirm-password">
                </div>
                <div>
                    <button class="btn btn-theme mt-2" id="updateUserPasswordButton">Update Password</button>
            </div>
        </div>
    </div>

            <div class="card mb-4">
                <div class="card-header d-flex align-items-center bg-none fw-semibold">
                    Actions
                </div>
                <div class="card-body">
                    <button class="btn btn-danger w-100 mb-2" id="deleteUserButton">
                        <i class="fa fa-trash me-2"></i> Delete User
                    </button>
                    {{-- <button class="btn btn-warning w-100 mb-2" id="suspendUserButton">
                        <i class="fa fa-pause me-2"></i> Suspend User
                    </button>
                    <button class="btn btn-theme w-100" id="resetPasswordButton">
                        <i class="fa fa-key me-2"></i> Reset Password
                    </button> --}}
                </div>
            </div>
        </div>

        
    </div>
@endsection

@push('other_scripts')
<script src="{{ asset('assets/js/user_management/user_update.js') }}"></script>
@endpush