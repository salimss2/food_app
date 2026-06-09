@extends('admin::components.layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4">Menu Management - Burger King</h2>
    <button class="btn btn-primary"><i class="fas fa-plus"></i> Add Item</button>
</div>

<div class="row">
    <!-- Categories Sidebar -->
    <div class="col-md-3 mb-4">
        <div class="list-group shadow-sm">
            <a href="#" class="list-group-item list-group-item-action active">All Items</a>
            <a href="#" class="list-group-item list-group-item-action">Burgers</a>
            <a href="#" class="list-group-item list-group-item-action">Beverages</a>
            <a href="#" class="list-group-item list-group-item-action">Sides</a>
            <a href="#" class="list-group-item list-group-item-action">Desserts</a>
        </div>
    </div>

    <!-- Items Grid -->
    <div class="col-md-9">
        <div class="row">
            <!-- Item 1 -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Food Item">
                    <div class="card-body">
                        <h5 class="card-title">Whopper Meal</h5>
                        <p class="card-text text-muted small">Grilled beef patty with fresh vegetables.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary">$12.50</span>
                            <span class="badge bg-success">In Stock</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between">
                        <small class="text-muted"><i class="fas fa-star text-warning"></i> 4.8</small>
                        <div>
                            <a href="#" class="text-secondary me-2"><i class="fas fa-edit"></i></a>
                            <a href="#" class="text-danger"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item 2 -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Food Item">
                    <div class="card-body">
                        <h5 class="card-title">Chicken Royale</h5>
                        <p class="card-text text-muted small">Crispy chicken breast with mayo and lettuce.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary">$8.00</span>
                            <span class="badge bg-danger">Out of Stock</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between">
                        <small class="text-muted"><i class="fas fa-star text-warning"></i> 4.5</small>
                        <div>
                            <a href="#" class="text-secondary me-2"><i class="fas fa-edit"></i></a>
                            <a href="#" class="text-danger"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item 3 -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Food Item">
                    <div class="card-body">
                        <h5 class="card-title">French Fries (L)</h5>
                        <p class="card-text text-muted small">Golden crispy salted fries.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary">$3.50</span>
                            <span class="badge bg-success">In Stock</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between">
                        <small class="text-muted"><i class="fas fa-star text-warning"></i> 4.2</small>
                        <div>
                            <a href="#" class="text-secondary me-2"><i class="fas fa-edit"></i></a>
                            <a href="#" class="text-danger"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
