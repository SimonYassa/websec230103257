@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Purchase Details</h4>
                    <div>
                        <a href="{{ route('purchases_index') }}" class="btn btn-secondary">Back to Purchases</a>
                        <form action="{{ route('purchases_destroy', $purchase->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this item from your cart? Your credit will be refunded.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Remove from Cart</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
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
                    
                    @if(session('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Purchase Information</h5>
                            <table class="table">
                                <tr>
                                    <th>Purchase ID:</th>
                                    <td>{{ $purchase->id }}</td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td>{{ $purchase->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Quantity:</th>
                                    <td>{{ $purchase->quantity }}</td>
                                </tr>
                                <tr>
                                    <th>Price per Unit:</th>
                                    <td>${{ number_format($purchase->price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total:</th>
                                    <td>${{ number_format($purchase->total, 2) }}</td>
                                </tr>
                            </table>

                            <!-- Simplified Quantity Update Form -->
                            <div class="card mt-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Update Quantity</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('purchases_update_quantity', $purchase->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="new_quantity" class="form-label">Quantity:</label>
                                            <input type="number" class="form-control" id="new_quantity" name="new_quantity" 
                                                min="1" 
                                                value="{{ $purchase->quantity }}" 
                                                max="{{ $purchase->quantity + ($purchase->product ? $purchase->product->stock : 0) }}"
                                                required>
                                            
                                            <div class="form-text">
                                                Use the arrows to increase or decrease the quantity.
                                                @if($purchase->product)
                                                    <br>Maximum available: {{ $purchase->quantity + $purchase->product->stock }} (current + stock)
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Price per Unit:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="text" class="form-control" value="{{ number_format($purchase->price, 2) }}" readonly>
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">Update Cart</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Product Information</h5>
                            @if($purchase->product)
                                <div class="card">
                                    @if($purchase->product->photo)
                                        <img src="{{ asset('images/' . $purchase->product->photo) }}" class="card-img-top" alt="{{ $purchase->product->name }}" style="height: 200px; object-fit: contain;">
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $purchase->product->name }}</h5>
                                        <p class="card-text">{{ $purchase->product->description }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-primary">${{ number_format($purchase->product->price, 2) }}</span>
                                            @if($purchase->product->stock > 0)
                                                <span class="badge bg-success">In Stock ({{ $purchase->product->stock }})</span>
                                            @else
                                                <span class="badge bg-danger">Out of Stock</span>
                                            @endif
                                        </div>
                                        <div class="mt-3">
                                            <a href="{{ route('products_show', $purchase->product->id) }}" class="btn btn-success">View Product</a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    Product is no longer available.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
