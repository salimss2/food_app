@extends('admin::components.layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4">Restaurants Management</h2>
    <a href="#" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Restaurant</a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <!-- Search & Filter -->
        <div class="row mb-3">
            <div class="col-md-5">
                <input type="text" class="form-control" placeholder="Search restaurant name, owner...">
            </div>
            <div class="col-md-3">
                <select class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active">Open</option>
                    <option value="inactive">Closed</option>
                    <option value="pending">Pending Approval</option>
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
                        <th>ID</th>
                        <th>Restaurant</th>
                        <th>Owner</th>
                        <th>Status</th>
                        <th>Rating</th>
                        <th>Orders</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

@foreach($restaurants as $restaurant)

<tr>

<td>{{ $restaurant->id }}</td>

<td>
<div class="d-flex align-items-center">

<img src="{{ $restaurant->logo ?? 'https://via.placeholder.com/40' }}"
class="rounded me-2" width="40">

<div>
<div class="fw-bold">{{ $restaurant->name }}</div>

<small class="text-muted">
{{ $restaurant->category }}
</small>

</div>

</div>
</td>

<td>{{ $restaurant->owner_name }}</td>

<td>

@if($restaurant->status == 'open')
<span class="badge bg-success">Open</span>

@elseif($restaurant->status == 'closed')
<span class="badge bg-danger">Closed</span>

@elseif($restaurant->status == 'pending')
<span class="badge bg-warning text-dark">Pending</span>
@endif

</td>

<td>
<i class="fas fa-star text-warning"></i>
{{ $restaurant->rating ?? '-' }}
</td>

<td>{{ $restaurant->orders_count ?? 0 }}</td>

<td>

<a href="{{ route('restaurants.show',$restaurant->id) }}"
class="btn btn-sm btn-outline-primary">

<i class="fas fa-eye"></i>

</a>

<a href="{{ route('restaurants.edit',$restaurant->id) }}"
class="btn btn-sm btn-outline-secondary">

<i class="fas fa-edit"></i>

</a>

<a href="{{ route('restaurants.menu',$restaurant->id) }}"
class="btn btn-sm btn-outline-info">

<i class="fas fa-utensils"></i>

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
