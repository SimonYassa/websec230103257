@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row my-4">
        <div class="col-12">
            <h1>All Products</h1>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <!-- Admin/Employee Controls -->
    @if(Auth::user() && (Auth::user()->hasRole('Employee') || Auth::user()->hasRole('Admin')))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Product Management</h5>
                    <a href="{{ route('products_create') }}" class="btn btn-success">Add New Product</a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Vertical Product List -->
    <div class="row">
        <div class="col-12">
            @if($products->isEmpty())
            <div class="alert alert-info">
                No products available at this time.
            </div>
            @else
            <div class="row">
                @foreach($products as $product)
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="row g-0">
                            <div class="col-md-3">
                                <div style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                    @if($product->photo)
                                    <img src="{{ asset('images/' . $product->photo) }}" alt="{{ $product->name }}" class="img-fluid" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                    @else
                                    <div class="text-center p-4">No Image</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h5 class="card-title">{{ $product->name }}</h5>
                                        <span class="badge bg-primary">${{ number_format($product->price, 2) }}</span>
                                    </div>
                                    <p class="card-text text-muted">{{ $product->model }}</p>
                                    <p class="card-text">
                                        {{ Str::limit($product->description, 150) }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            @if($product->stock > 0)
                                            <span class="badge bg-success">In Stock ({{ $product->stock }})</span>
                                            @else
                                            <span class="badge bg-danger">Out of Stock</span>
                                            @endif
                                        </div>
                                        <div class="btn-group">
                                            <a href="{{ route('products_show', $product->id) }}" class="btn btn-success">View Details</a>
                                            
                                            @if(Auth::user() && (Auth::user()->hasRole('Employee') || Auth::user()->hasRole('Admin')))
                                            <a href="{{ route('products_edit', $product->id) }}" class="btn btn-secondary">Edit</a>
                                            <form action="{{ route('products_destroy', $product->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
