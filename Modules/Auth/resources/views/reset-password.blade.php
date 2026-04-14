@extends('admin::components.layouts.guest')

@section('content')
<div class="card auth-card p-4">
    
    <div class="auth-header">
        <i class="fas fa-lock mb-3"></i>
        <h4>Reset Password</h4>
        <p class="text-muted">Enter your new password</p>
    </div>

    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        {{-- Token --}}
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Email --}}
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-envelope"></i>
                </span>
                <input 
                    type="email"
                    name="email"
                    class="form-control"
                    value="{{ $email ?? old('email') }}"
                    required
                >
            </div>
        </div>

        {{-- New Password --}}
        <div class="mb-3">
            <label class="form-label">New Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-key"></i>
                </span>
                <input 
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="******"
                    required
                >
            </div>
        </div>

        {{-- Confirm Password --}}
        <div class="mb-4">
            <label class="form-label">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-check"></i>
                </span>
                <input 
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                    placeholder="******"
                    required
                >
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Reset Password
        </button>

        <div class="text-center mt-3">
            <a href="{{ route('admin.login') }}" class="text-decoration-none small">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>

    </form>
</div>
@endsection