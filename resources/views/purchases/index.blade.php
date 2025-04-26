@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>My Purchases</h4>
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
                    
                    @if($purchases->isEmpty())
                        <div class="alert alert-info">
                            You have no purchases yet.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchases as $purchase)
                                        <tr>
                                            <td>
                                                @if($purchase->product)
                                                    <a href="{{ route('products_show', $purchase->product->id) }}">
                                                        {{ $purchase->product->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">Product no longer available</span>
                                                @endif
                                            </td>
                                            <td>${{ number_format($purchase->price, 2) }}</td>
                                            <td>{{ $purchase->quantity }}</td>
                                            <td>${{ number_format($purchase->total, 2) }}</td>
                                            <td>{{ $purchase->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('purchases_show', $purchase->id) }}" class="btn btn-sm btn-info">
                                                        View
                                                    </a>
                                                    <form action="{{ route('purchases_destroy', $purchase->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this item from your cart? Your credit will be refunded.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <h5>Total Spent: ${{ number_format($purchases->sum('total'), 2) }}</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
