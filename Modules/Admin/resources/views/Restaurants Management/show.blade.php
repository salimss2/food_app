@extends('admin::components.layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4">Restaurant Details</h2>
    <div>
        <a href="#" class="btn btn-primary"><i class="fas fa-edit"></i> Edit Restaurant</a>
        <a href="index.blade.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="row">
    <!-- Info Card -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm text-center p-4">
            <div class="mb-3">
                <img src="https://via.placeholder.com/150" class="rounded img-fluid" style="width: 150px; height: 100px; object-fit: cover;" alt="Restaurant Logo">
            </div>
            <h5 class="mb-1">Burger King</h5>
            <p class="text-muted mb-2">Fast Food, American</p>
            <div class="mb-3">
                <span class="badge bg-success">Open</span>
                <span class="badge bg-warning text-dark"><i class="fas fa-star"></i> 4.5</span>
            </div>
            
            <div class="d-flex justify-content-center gap-2">
                <a href="#" class="btn btn-outline-primary btn-sm w-100"><i class="fas fa-utensils"></i> Manage Menu</a>
            </div>
        </div>
        
        <!-- Contact Info -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white font-weight-bold">
                Contact Information
            </div>
            <div class="card-body">
                <p><strong>Owner:</strong> James Smith</p>
                <p><strong>Phone:</strong> +1 555 0199</p>
                <p><strong>Email:</strong> burgerking@example.com</p>
                <p><strong>Address:</strong><br> 456 Fast Food Lane,<br> Food City, FC 90210</p>
            </div>
        </div>
    </div>

    <!-- Stats & Orders -->
    <div class="col-lg-8">
        <!-- Stats Row -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-primary text-primary h-100">
                    <div class="card-body text-center">
                        <h3 class="fw-bold">1,250</h3>
                        <small>Total Orders</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-success text-success h-100">
                    <div class="card-body text-center">
                        <h3 class="fw-bold">$45,800</h3>
                        <small>Total Sales</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-info text-info h-100">
                    <div class="card-body text-center">
                        <h3 class="fw-bold">$4,580</h3>
                        <small>Commission (10%)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card shadow-sm">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                <a href="#" class="small text-decoration-none">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#ORD-999</td>
                            <td>Alice Freeman</td>
                            <td>2x Whopper Meal</td>
                            <td>$25.00</td>
                            <td><span class="badge bg-warning">Cooking</span></td>
                        </tr>
                        <tr>
                            <td>#ORD-998</td>
                            <td>Bob Johnson</td>
                            <td>1x Chicken Royale</td>
                            <td>$12.50</td>
                            <td><span class="badge bg-info">Ready</span></td>
                        </tr>
                        <tr>
                            <td>#ORD-997</td>
                            <td>Charlie Brown</td>
                            <td>3x Fries</td>
                            <td>$9.00</td>
                            <td><span class="badge bg-success">Delivered</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
