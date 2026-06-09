@extends('admin::components.layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4">Drivers Management</h2>
    <a href="#" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Driver</a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <!-- Search & Filter -->
        <div class="row mb-3">
            <div class="col-md-5">
                <input type="text" class="form-control" placeholder="Search driver name, phone...">
            </div>
            <div class="col-md-3">
                <select class="form-select">
                    <option value="">All Statuses</option>
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                    <option value="busy">Busy</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100">Filter</button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>Driver</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Current Order</th>
                        <th>Earnings</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
@foreach($drivers as $driver)
<tr>

<td>{{ $loop->iteration }}</td>

<td>
<div class="d-flex align-items-center">
<img src="https://via.placeholder.com/40" class="rounded-circle me-2">

<div>
<div class="fw-bold">{{ $driver->name }}</div>

<small class="text-muted">
{{ $driver->vehicle_type ?? 'Bike' }}
</small>

</div>
</div>
</td>

<td>{{ $driver->phone ?? '-' }}</td>

<td>
<span class="badge bg-success">
{{ $driver->status ?? 'offline' }}
</span>
</td>

<td>
<span class="text-muted">None</span>
</td>

<td>$0</td>

<td>

<a href="{{ route('admin.drivers.show',$driver->id) }}" class="btn btn-sm btn-outline-primary">
<i class="fas fa-eye"></i>
</a>

<a href="#" class="btn btn-sm btn-outline-secondary">
<i class="fas fa-edit"></i>
</a>

</td>

</tr>
@endforeach
</tbody>
            </table>
        </div>
    </div>
</div>
@endsection
