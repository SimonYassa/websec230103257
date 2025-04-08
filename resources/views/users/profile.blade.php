@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">{{ __('Profile') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Name:</div>
                        <div class="col-md-8">{{ $user->name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Email:</div>
                        <div class="col-md-8">{{ $user->email }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Role:</div>
                        <div class="col-md-8">{{ $user->getRoleNames()->implode(', ') }}</div>
                    </div>

                    @if($user->isCustomer())
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Account Credit:</div>
                        <div class="col-md-8">${{ number_format($user->credit, 2) }}</div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Member Since:</div>
                        <div class="col-md-8">{{ $user->created_at->format('F j, Y') }}</div>
                    </div>

                    <div class="mt-3">
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</a>
                        <a href="{{ route('edit_password') }}" class="btn btn-secondary">Change Password</a>
                    </div>
                </div>
            </div>

            @if($user->isCustomer())
            <div class="card">
                <div class="card-header">{{ __('My Recent Purchases') }}</div>

                <div class="card-body">
                    @if(count($purchases) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchases->take(5) as $purchase)
                                <tr>
                                    <td>{{ $purchase->product->name }}</td>
                                    <td>{{ $purchase->quantity }}</td>
                                    <td>${{ number_format($purchase->total, 2) }}</td>
                                    <td>{{ $purchase->created_at->format('Y-m-d') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ route('purchases_index') }}" class="btn btn-info">View All Purchases</a>
                    @else
                    <p>You have not made any purchases yet.</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('profile_update') }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

