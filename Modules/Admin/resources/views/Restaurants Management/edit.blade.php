@extends('admin::components.layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4">Edit Restaurant</h2>
    <a href="index.blade.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Restaurant Information</h5>
            </div>
            <div class="card-body">
                <form action="#" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Restaurant Name</label>
                            <input type="text" class="form-control" value="Burger King" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cuisine Type</label>
                            <select class="form-select">
                                <option>Fast Food</option>
                                <option>Italian</option>
                                <option>Japanese</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="3">Home of the Whopper.</textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Delivery Time (mins)</label>
                            <input type="number" class="form-control" value="30">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Delivery Fee ($)</label>
                            <input type="number" class="form-control" value="2.99" step="0.01">
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3">Owner Information</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Owner Name</label>
                            <input type="text" class="form-control" value="James Smith" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" value="+1 555 0199">
                        </div>
                    </div>
                    <div class="mb-3">
                         <label class="form-label">Email</label>
                         <input type="email" class="form-control" value="burgerking@example.com">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Status</label>
                        <select class="form-select">
                            <option value="active" selected>Open</option>
                            <option value="inactive">Closed (Busy)</option>
                            <option value="banned">Banned</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
