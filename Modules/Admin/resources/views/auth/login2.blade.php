{{-- @extends('admin::components.layouts.guest')

@section('content')
<div class="card auth-card p-4">
    <div class="auth-header">
        <i class="fas fa-utensils mb-3"></i>
        <h4>Admin Login</h4>
        <p class="text-muted">Sign in to manage the system</p>
    </div>
    


@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif


    <form action="{{ route('admin.login.submit') }}" method="POST">
        @csrf <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" class="form-control" id="email" placeholder="admin@example.com" required>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" class="form-control" id="password" placeholder="******" required>
            </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <a href="{{ route('admin.password.request.index') }}" class="text-decoration-none small">Forgot Password?</a>
        </div>
        
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>


@endsection --}}