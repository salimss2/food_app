{{-- @extends('admin::components.layouts.admin')


@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4">Driver Details</h2>
    <div>
        <a href="#" class="btn btn-primary"><i class="fas fa-edit"></i> Edit Driver</a>
        <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="row">
    <!-- Profile Card -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm text-center p-4">
            <div class="mb-3">
                <img src="https://via.placeholder.com/150" class="rounded-circle img-fluid" style="width: 120px; height: 120px; object-fit: cover;" alt="Driver Avatar">
            </div>
            <h5 class="mb-1">{{ $driver->name }}</h5>
            <p class="text-muted mb-3">{{ $driver->email }}</p>
            <span class="badge bg-success">
{{ $driver->status ?? 'Offline' }}
</span>
<span class="fw-bold">{{ $driver->phone ?? '-' }}</span>
            
            <div class="d-flex justify-content-center gap-2">
                <button class="btn btn-outline-danger btn-sm"><i class="fas fa-ban"></i> Block</button>
                <button class="btn btn-outline-primary btn-sm"><i class="fas fa-phone"></i> Call</button>
            </div>
        </div>
        
        <!-- Vehicle Info -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white font-weight-bold">
                Vehicle Information
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Type</span> <span class="fw-bold">Motorcycle</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Model</span> <span class="fw-bold">Yamaha R15</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Plate Number</span> <span class="fw-bold">NYC-1234</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Color</span> <span class="fw-bold">Red</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Stats & History -->
    <div class="col-lg-8">
        <!-- Stats Row -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-light border-0 h-100">
                    <div class="card-body text-center">
                        <h4 class="fw-bold text-primary">450</h4>
                        <small class="text-muted">Completed Deliveries</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light border-0 h-100">
                    <div class="card-body text-center">
                        <h4 class="fw-bold text-success">$1,500</h4>
                        <small class="text-muted">Total Earnings</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light border-0 h-100">
                    <div class="card-body text-center">
                        <h4 class="fw-bold text-warning">4.9</h4>
                        <small class="text-muted">Average Rating</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Deliveries -->
        <div class="card shadow-sm">
            <div class="card-header bg-transparent py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Deliveries</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Restaurant</th>
                            <th>Earnings</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#ORD-1001</td>
                            <td>Today, 10:30 AM</td>
                            <td>Burger King</td>
                            <td>$5.00</td>
                            <td><span class="badge bg-success">Delivered</span></td>
                        </tr>
                        <tr>
                            <td>#ORD-0985</td>
                            <td>Yesterday, 2:15 PM</td>
                            <td>Pizza Hut</td>
                            <td>$7.50</td>
                            <td><span class="badge bg-success">Delivered</span></td>
                        </tr>
                        <tr>
                            <td>#ORD-0970</td>
                            <td>Yesterday, 11:00 AM</td>
                            <td>Sushi Place</td>
                            <td>$4.00</td>
                            <td><span class="badge bg-danger">Cancelled</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection --}}


@extends('admin::components.layouts.admin')

@section('content')

<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Driver Details</h4>

        <div>
            <a href="#" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>

            <a href="{{ route('admin.drivers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>


    <div class="row">

        <!-- Driver Profile -->
        <div class="col-lg-4">

            <div class="card shadow-sm text-center p-4">

                <img src="https://via.placeholder.com/120"
                     class="rounded-circle mx-auto mb-3"
                     width="120" height="120">

                <h5 class="fw-bold">{{ $driver->name }}</h5>

                <p class="text-muted mb-2">
                    {{ $driver->email }}
                </p>

                <p class="mb-2">
                    <strong>Phone:</strong>
                    {{ $driver->phone ?? 'N/A' }}
                </p>

                <span class="badge bg-success mb-3">
                    {{ $driver->status ?? 'Offline' }}
                </span>

                <div class="d-flex justify-content-center gap-2">

                    <button class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-ban"></i> Block
                    </button>

                    <button class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-phone"></i> Call
                    </button>

                </div>

            </div>


            <!-- Vehicle Info -->
            <div class="card shadow-sm mt-4">

                <div class="card-header fw-bold">
                    Vehicle Information
                </div>

                <ul class="list-group list-group-flush">

                    <li class="list-group-item d-flex justify-content-between">
                        <span>Type</span>
                        <strong>{{ $driver->vehicle_type ?? 'Motorcycle' }}</strong>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <span>Model</span>
                        <strong>{{ $driver->vehicle_model ?? '-' }}</strong>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <span>Plate</span>
                        <strong>{{ $driver->plate_number ?? '-' }}</strong>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <span>Color</span>
                        <strong>{{ $driver->vehicle_color ?? '-' }}</strong>
                    </li>

                </ul>

            </div>

        </div>



        <!-- Driver Stats -->
        <div class="col-lg-8">

            <div class="row mb-4">

                <div class="col-md-4">

                    <div class="card text-center shadow-sm">

                        <div class="card-body">

                            <h4 class="text-primary fw-bold">
                                {{ $completedOrders ?? 0 }}
                            </h4>

                            <small class="text-muted">
                                Completed Deliveries
                            </small>

                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="card text-center shadow-sm">

                        <div class="card-body">

                            <h4 class="text-success fw-bold">
                                ${{ $earnings ?? 0 }}
                            </h4>

                            <small class="text-muted">
                                Total Earnings
                            </small>

                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="card text-center shadow-sm">

                        <div class="card-body">

                            <h4 class="text-warning fw-bold">
                                {{ $rating ?? '0.0' }}
                            </h4>

                            <small class="text-muted">
                                Average Rating
                            </small>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Recent Orders -->
            <div class="card shadow-sm">

                <div class="card-header fw-bold">
                    Recent Deliveries
                </div>

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead class="table-light">

                        <tr>
                            <th>Order</th>
                            <th>Date</th>
                            <th>Restaurant</th>
                            <th>Earnings</th>
                            <th>Status</th>
                        </tr>

                        </thead>

                        <tbody>

                        @forelse($orders ?? [] as $order)

                            <tr>

                                <td>#{{ $order->id }}</td>

                                <td>{{ $order->created_at }}</td>

                                <td>{{ $order->restaurant->name ?? '-' }}</td>

                                <td>${{ $order->driver_profit ?? 0 }}</td>

                                <td>
                                    <span class="badge bg-success">
                                        Delivered
                                    </span>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No deliveries yet
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection