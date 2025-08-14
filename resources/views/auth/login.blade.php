@extends('layout.empty')

@section('title', 'Login')

@section('content')
<div class="login">
  <div class="login-content">

       {{-- <!-- Display error message if it exists -->
       @if ($errors->has('email'))
       <div class="error">
           {{ $errors->first('email') }}
       </div>
      @endif --}}

    <form id="login_form" method="POST" action="{{ route('web.login') }}">
      @csrf
      <h1 class="text-center">Sign In</h1>
      <div class="text-muted text-center mb-4">
        For your protection, please verify your identity.
      </div>
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control form-control-lg fs-15px" placeholder="username@address.com" required>
      </div>
      <div class="mb-3">
        <div class="d-flex">
          <label class="form-label">Password</label>
          <a href="#" class="ms-auto text-muted">Forgot password?</a>
        </div>
        <input type="password" id="password" class="form-control form-control-lg fs-15px" placeholder="Enter your password" name="password" required>
      </div>
      <div class="mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="remember_me">
          <label class="form-check-label fw-500" for="remember_me">Remember me</label>
        </div>
      </div>
      <button id="login_button" class="btn btn-theme btn-lg d-block w-100 fw-500 mb-3" type="submit">Sign In</button>
      {{-- <div class="text-center text-muted">
        Don't have an account yet? <a href="/page/register">Sign up</a>.
      </div> --}}
    </form>
  </div>
</div>
@endsection



@if (session('error') || $errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let errorMessage = '{{ session('error') }}';
            @if ($errors->any())
                errorMessage = @json($errors->all()).join('\n');
            @endif
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: errorMessage,
                confirmButtonText: 'OK'
            });
        });
    </script>
@endif


@push('other_scripts')
<script>
  @if (session('token'))
      localStorage.setItem("adminToken", "{{ session('token') }}");
  @endif
</script>
{{-- <script  src="{{ asset('assets/js/auth/login.js') }}"></script> --}}
@endpush