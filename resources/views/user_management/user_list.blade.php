@extends('layout.default')

@section('title', 'Users')

@section('content')
	<div class="d-flex align-items-center mb-3">
		<div>
			<ul class="breadcrumb">
				<li class="breadcrumb-item"><a href="#">PAGES</a></li>
				<li class="breadcrumb-item active">USERS</li>
			</ul>
			<h1 class="page-header mb-0">Users</h1>
		</div>
		
		<div class="ms-auto">
			<a href="/users/create_user" class="btn btn-theme"><i class="fa fa-plus-circle fa-fw me-1"></i> Create User</a>
		</div>
	</div>
	
	{{-- <div class="mb-md-4 mb-3 d-md-flex">
		<div class="mt-md-0 mt-2"><a href="#" class="text-body text-decoration-none"><i class="fa fa-download fa-fw me-1 text-muted"></i> Export</a></div>
		<div class="ms-md-4 mt-md-0 mt-2 dropdown-toggle">
			<a href="#" data-bs-toggle="dropdown" class="text-body text-decoration-none">More Actions</a>
			<div class="dropdown-menu">
				<a class="dropdown-item" href="#">Action</a>
				<a class="dropdown-item" href="#">Another action</a>
				<a class="dropdown-item" href="#">Something else here</a>
				<div role="separator" class="dropdown-divider"></div>
				<a class="dropdown-item" href="#">Separated link</a>
			</div>
		</div>
	</div> --}}
	
	<div class="card">
		{{-- <ul class="nav nav-tabs nav-tabs-v2 px-4">
			<li class="nav-item me-3"><a href="#allTab" class="nav-link active px-2" data-bs-toggle="tab">All</a></li>
			<li class="nav-item me-3"><a href="#activeTab" class="nav-link px-2" data-bs-toggle="tab">Active</a></li>
			<li class="nav-item me-3"><a href="#bannedTab" class="nav-link px-2" data-bs-toggle="tab">Banned</a></li>
			<li class="nav-item me-3"><a href="#suspendedTab" class="nav-link px-2" data-bs-toggle="tab">Suspended</a></li>
		</ul> --}}
		<div class="tab-content p-4">
			<div class="tab-pane fade show active" id="allTab">
				<!-- BEGIN input-group -->
				<div class="input-group mb-4">
					<div class="flex-fill position-relative">
						<div class="input-group">
							<input type="text" class="form-control ps-35px" placeholder="Filter users">
							<div class="input-group-text position-absolute top-0 bottom-0 bg-none border-0" style="z-index: 1020;">
								<i class="fa fa-search opacity-5"></i>
							</div>
						</div>
					</div>
					<button class="btn btn-default dropdown-toggle rounded-0" type="button" data-bs-toggle="dropdown"><span class="d-none d-md-inline">Status</span><span class="d-inline d-md-none"><i class="fa fa-filter"></i></span> &nbsp;</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="#">Active</a>
						<a class="dropdown-item" href="#">Banned</a>
						<a class="dropdown-item" href="#">Suspended</a>
					</div>
					<button class="btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown"><span class="d-none d-md-inline">Sort By</span><span class="d-inline d-md-none"><i class="fa fa-sort"></i></span></button>
					<div class="dropdown-menu dropdown-menu-end">
						<a class="dropdown-item" href="#">Name</a>
						<a class="dropdown-item" href="#">Date Joined</a>
						<a class="dropdown-item" href="#">Last Login</a>
					</div>
				</div>
				<!-- END input-group -->
				
				<!-- BEGIN table -->
				<div class="table-responsive">
					<table class="table table-hover text-nowrap">
						<thead>
							<tr>
								<th class="border-top-0 pt-0 pb-2"></th>
								<th class="border-top-0 pt-0 pb-2">Name</th>
								<th class="border-top-0 pt-0 pb-2">Email</th>
								<th class="border-top-0 pt-0 pb-2">Phone</th>
								<th class="border-top-0 pt-0 pb-2">Wallet Balance</th>
								<th class="border-top-0 pt-0 pb-2">Status</th>
								<th class="border-top-0 pt-0 pb-2">Date Joined</th>
								{{-- <th class="border-top-0 pt-0 pb-2">Last Login</th> --}}
								<th class="border-top-0 pt-0 pb-2">Actions</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($users as $user)
								<tr data-status="{{ $user['status'] }}">
									<td class="w-10px align-middle">
										<div class="form-check">
											<input type="checkbox" class="form-check-input" id="user{{ $user['id'] }}">
											<label class="form-check-label" for="user{{ $user['id'] }}"></label>
										</div>
									</td>
									<td class="align-middle" data-name="{{ $user['name'] }}">{{ $user['name'] }}</td>
									<td class="align-middle" data-email="{{ $user['email'] }}">{{ $user['email'] }}</td>
									<td class="align-middle" data-phone="{{ $user['phone'] ?? 'N/A' }}">{{ $user['phone'] ?? 'N/A' }}</td>
									<td class="align-middle" data-wallet-balance="{{ $user['wallet_balance'] }}">&#8358;{{ number_format($user['wallet_balance'], 2) }}</td>
									<td class="align-middle" data-status="{{ $user['status'] }}">
										@if ($user['status'] === 'active')
											<span class="badge bg-teal text-teal-800 bg-opacity-25 px-2 pt-5px pb-5px rounded fs-12px d-inline-flex align-items-center">
												<i class="fa fa-circle text-teal fs-9px fa-fw me-5px"></i> Active
											</span>
										@elseif ($user['status'] === 'banned')
											<span class="badge bg-danger bg-opacity-20 text-danger px-2 pt-5px pb-5px rounded fs-12px d-inline-flex align-items-center">
												<i class="fa fa-circle text-danger fs-9px fa-fw me-5px"></i> Banned
											</span>
										@elseif ($user['status'] === 'suspended')
											<span class="badge bg-orange bg-opacity-20 text-orange px-2 pt-5px pb-5px rounded fs-12px d-inline-flex align-items-center">
												<i class="fa fa-circle text-orange fs-9px fa-fw me-5px"></i> Suspended
											</span>
										@endif
									</td>
									<td class="align-middle" data-date-joined="{{ $user['date_joined'] }}">
										{{ \Carbon\Carbon::parse($user['date_joined'])->format('M d, Y h:i A') }}
									</td>
									{{-- <td class="align-middle" data-last-login="{{ $user['last_login'] }}">
										{{ $user['last_login'] ? \Carbon\Carbon::parse($user['last_login'])->format('M d, Y h:i A') : 'N/A' }}
									</td> --}}
									<td class="align-middle">
										<a href="{{ route('users.edit', $user['id']) }}" class="btn btn-sm btn-default">Edit</a>
										<a href="#" class="btn btn-sm btn-danger delete-user" data-id="{{ $user['id'] }}">Delete</a>
										<a href="{{ route('users.get', $user['id']) }}" class="btn btn-sm btn-theme" data-id="{{ $user['id'] }}">View</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<!-- END table -->
				
				<div class="d-md-flex align-items-center">
					<div class="me-md-auto text-md-left text-center mb-2 mb-md-0">
						Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
					</div>
					<ul class="pagination mb-0 justify-content-center">
						<li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
							<a class="page-link" href="{{ $users->previousPageUrl() }}">Previous</a>
						</li>
						@for ($i = 1; $i <= $users->lastPage(); $i++)
							<li class="page-item {{ $users->currentPage() === $i ? 'active' : '' }}">
								<a class="page-link" href="{{ $users->url($i) }}">{{ $i }}</a>
							</li>
						@endfor
						<li class="page-item {{ $users->hasMorePages() ? '' : 'disabled' }}">
							<a class="page-link" href="{{ $users->nextPageUrl() }}">Next</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('other_scripts')
<script>
    @if (session('token'))
        localStorage.setItem("adminToken", "{{ session('token') }}");
        console.log("Token stored: {{ session('token') }}"); // Debug
    @endif
</script>
<script src="{{ asset('assets/js/user_management/index.js') }}"></script>
<script src="{{ asset('assets/js/user_management/delete_user.js') }}"></script>

@endpush