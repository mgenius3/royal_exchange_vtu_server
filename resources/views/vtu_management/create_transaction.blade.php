@extends('layout.default')

@section('title', 'Create VTU Transaction')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <h1 class="page-header mb-0">Create VTU Transaction</h1>
    </div>

    <div class="card">
        <div class="card-header bg-none fw-semibold">New Transaction</div>
        <div class="card-body">
            <form action="{{ route('admin.vtu.store-transaction') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">User</label>
                    <select class="form-select" name="user_id" required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Plan</label>
                    <select class="form-select" name="vtu_plan_id" id="planSelect" required>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}" data-type="{{ $plan->type }}">{{ $plan->network }} - {{ $plan->description }} ({{ $plan->plan_code }}) - ${{ $plan->price }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3" id="phoneField">
                    <label class="form-label">Phone Number</label>
                    <input type="text" class="form-control" name="phone_number">
                </div>
                <div class="mb-3" id="accountField" style="display: none;">
                    <label class="form-label">Account/Meter Number</label>
                    <input type="text" class="form-control" name="account_number">
                </div>
                <button type="submit" class="btn btn-theme">Process Transaction</button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('planSelect').addEventListener('change', function() {
            const type = this.options[this.selectedIndex].dataset.type;
            const phoneField = document.getElementById('phoneField');
            const accountField = document.getElementById('accountField');

            if (type === 'airtime' || type === 'data') {
                phoneField.style.display = 'block';
                accountField.style.display = 'none';
                phoneField.querySelector('input').required = true;
                accountField.querySelector('input').required = false;
            } else if (type === 'tv' || type === 'electricity') {
                phoneField.style.display = 'none';
                accountField.style.display = 'block';
                phoneField.querySelector('input').required = false;
                accountField.querySelector('input').required = true;
            }
        });
    </script>
    @endpush
@endsection