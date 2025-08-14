@extends('layout.default')

@section('content')
<style>
    /* Custom styles for a polished look */
    .payment-settings-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .gateway-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .gateway-card:hover {
        transform: translateY(-3px);
    }

    .gateway-card .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 10px 10px 0 0;
        font-weight: 600;
        text-transform: capitalize;
        color: #343a40;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn-primary {
        background: linear-gradient(45deg, #007bff, #00aaff);
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        transition: background 0.3s;
    }

    .btn-primary:hover {
        background: linear-gradient(45deg, #0056b3, #007bff);
    }

    .alert-success {
        border-radius: 8px;
        animation: fadeIn 0.5s ease-in;
    }

    .form-check-label {
        cursor: pointer;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @media (max-width: 576px) {
        .gateway-card {
            margin-bottom: 1.5rem;
        }
    }
</style>

<div class="container payment-settings-container py-5">
    <h2 class="mb-4 text-center fw-bold" style="color: #343a40;">Payment Gateway Settings</h2>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24" fill="currentColor">
                <use xlink:href="#check-circle-fill"/>
            </svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @foreach (['paystack', 'flutterwave'] as $gateway)
        @php $config = $configs[$gateway] ?? null; @endphp

        <div class="card gateway-card mb-4">
            <div class="card-header py-3">
                {{ ucfirst($gateway) }} Configuration
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.payment-settings.update', $gateway) }}">
                    @csrf
                    @method('POST')

                    <div class="mb-3">
                        <label for="{{ $gateway }}-public-key" class="form-label fw-medium">Public Key</label>
                        <input type="text" name="public_key" id="{{ $gateway }}-public-key" class="form-control"
                               value="{{ old('public_key', $config->public_key ?? '') }}" required>
                        @error('public_key')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="{{ $gateway }}-secret-key" class="form-label fw-medium">Secret Key</label>
                        <input type="text" name="secret_key" id="{{ $gateway }}-secret-key" class="form-control"
                               value="{{ old('secret_key', $config->secret_key ?? '') }}" required>
                        @error('secret_key')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    @if ($gateway === 'flutterwave')
                        <div class="mb-3">
                            <label for="encryption-key" class="form-label fw-medium">Encryption Key</label>
                            <input type="text" name="encryption_key" id="encryption-key" class="form-control"
                                   value="{{ old('encryption_key', $config->encryption_key ?? '') }}">
                            @error('encryption_key')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="{{ $gateway }}-webhook-secret" class="form-label fw-medium">Webhook Secret</label>
                        <input type="text" name="webhook_secret" id="{{ $gateway }}-webhook-secret" class="form-control"
                               value="{{ old('webhook_secret', $config->webhook_secret ?? '') }}">
                        @error('webhook_secret')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_active" id="{{ $gateway }}-is-active" class="form-check-input"
                               {{ $config && $config->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="{{ $gateway }}-is-active">Active</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Configuration</button>
                </form>
            </div>
        </div>
    @endforeach
</div>

<!-- Bootstrap Icons for alert -->
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
    </symbol>
</svg>
@endsection