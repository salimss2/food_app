@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 text-danger">Blocked Users</h2>
    <a href="index.blade.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back to All Users</a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Reason</th>
                        <th>Blocked Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>3</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="Avatar">
                                <div>
                                    <div class="fw-bold">John Doe</div>
                                    <small class="text-muted">john@example.com</small>
                                </div>
                            </div>
                        </td>
                        <td>Violation of Terms</td>
                        <td>2023-09-10</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                            <button class="btn btn-sm btn-success" title="Unblock"><i class="fas fa-check"></i> Unblock</button>
                        </td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="Avatar">
                                <div>
                                    <div class="fw-bold">Fake Account</div>
                                    <small class="text-muted">fake@example.com</small>
                                </div>
                            </div>
                        </td>
                        <td>Suspected Spam</td>
                        <td>2023-11-05</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                            <button class="btn btn-sm btn-success" title="Unblock"><i class="fas fa-check"></i> Unblock</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
