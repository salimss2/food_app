{{-- @extends('admin::components.layouts.guest')

@section('content')
<div class="card auth-card p-4">
    <div class="auth-header">
        <i class="fas fa-shield-alt mb-3"></i>
        <h4>OTP Verification</h4>
        <p class="text-muted">Enter the 4-digit code sent to your email</p>
    </div>
    
    <form action="{{ route('admin.dashboard') }}" method="GET">
        <div class="mb-4 d-flex justify-content-center gap-2">
            <input type="text" class="form-control text-center fs-4" maxlength="1" style="width: 50px; height: 50px;">
            <input type="text" class="form-control text-center fs-4" maxlength="1" style="width: 50px; height: 50px;">
            <input type="text" class="form-control text-center fs-4" maxlength="1" style="width: 50px; height: 50px;">
            <input type="text" class="form-control text-center fs-4" maxlength="1" style="width: 50px; height: 50px;">
        </div>
        
        <button type="submit" class="btn btn-primary mb-3">Verify Code</button>
        
        <div class="text-center">
            <p class="text-muted small">Didn't receive code? <a href="#" class="text-decoration-none">Resend</a></p>
            <a href="{{ route('admin.login') }}" class="text-decoration-none text-muted small">Cancel</a>
        </div>
    </form>
</div>
@endsection --}}
