@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>{{ __('Product Details') }}</h4>
                        <a href="{{ route('products_index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($product->photo)
                                <img src="{{ asset('images/' . $product->photo) }}" alt="{{ $product->name }}" class="img-fluid">
                            @else
                                <div class="bg-light p-5 text-center">No Image</div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h3>{{ $product->name }}</h3>
                            <p><strong>Code:</strong> {{ $product->code }}</p>
                            <p><strong>Model:</strong> {{ $product->model }}</p>
                            <p><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>
                            <p><strong>Stock:</strong> {{ $product->stock }} units</p>
                            <p><strong>Description:</strong> {{ $product->description }}</p>
                            
                            @if(auth()->user()->isCustomer())
                                @if($product->stock > 0)
                                    <form action="{{ route('products_purchase', $product->id) }}" method="POST">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label for="quantity">Quantity:</label>
                                            <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="{{ $product->stock }}">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Purchase</button>
                                    </form>
                                @else
                                    <div class="alert alert-warning">
                                        This product is currently out of stock.
                                    </div>
                                @endif
                            @endif
                            
                            @if(auth()->user()->isEmployee())
                                <div class="mt-3">
                                    <a href="{{ route('products_edit', $product->id) }}" class="btn btn-primary">Edit</a>
                                    <form action="{{ route('products_destroy', $product->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                    </form>
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

