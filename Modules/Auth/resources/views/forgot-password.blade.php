@extends('admin::components.layouts.guest')

@section('content')
<div class="card auth-card p-4">
    <div class="auth-header">
        <i class="fas fa-key mb-3"></i>
        <h4>Forgot Password</h4>
        <p class="text-muted">Enter your email to reset password</p>
    </div>
    
    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" class="form-control" id="email" placeholder="admin@example.com" required>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
        
        <div class="text-center mt-3">
            <a href="{{ route('admin.login') }}" class="text-decoration-none"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </form>
</div>
@endsection