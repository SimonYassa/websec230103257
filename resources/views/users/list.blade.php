@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>{{ __('Customers') }}</h4>
                        <a href="{{ route('users_create') }}" class="btn btn-primary">{{ __('Add New Customer') }}</a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif
                    
                    @if(count($users) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Credit</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>${{ number_format($user->credit, 2) }}</td>
                                    <td>
                                        <a class="btn btn-info btn-sm" href="{{ route('users_show', $user->id) }}">View</a>
                                        <a class="btn btn-primary btn-sm" href="{{ route('users_edit', $user->id) }}">Edit</a>
                                        <a class="btn btn-success btn-sm" href="{{ route('users_show_add_credit', $user->id) }}">Add Credit</a>
                                        <form action="{{ route('users_destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        No customers found. <a href="{{ route('users_create') }}">Create a new customer</a>.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

