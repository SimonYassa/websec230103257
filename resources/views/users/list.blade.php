@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>
                            @if(Auth::user()->hasRole('Admin'))
                                @if($activeFilter === 'all')
                                    {{ __('All Users') }}
                                @elseif($activeFilter === 'employees')
                                    {{ __('Employees') }}
                                @elseif($activeFilter === 'admins')
                                    {{ __('Administrators') }}
                                @else
                                    {{ __('Customers') }}
                                @endif
                            @else
                                {{ __('Customers') }}
                            @endif
                        </h4>
                        <div>
                            {{-- Removed "Add New Customer" button for both admins and employees --}}
                            @if(Auth::user()->hasRole('Admin'))
                                <a href="{{ route('users_create_employee') }}" class="btn btn-success">{{ __('Add New Employee') }}</a>
                            @endif
                        </div>
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
                    
                    @if(Auth::user()->hasRole('Admin'))
                    <div class="mb-4">
                        <div class="btn-group" role="group" aria-label="User filters">
                            <a href="{{ route('users_index') }}" class="btn btn-{{ $activeFilter === 'all' ? 'primary' : 'outline-primary' }}">All Users</a>
                            <a href="{{ route('users_index', ['filter' => 'customers']) }}" class="btn btn-{{ $activeFilter === 'customers' ? 'primary' : 'outline-primary' }}">Customers</a>
                            <a href="{{ route('users_index', ['filter' => 'employees']) }}" class="btn btn-{{ $activeFilter === 'employees' ? 'primary' : 'outline-primary' }}">Employees</a>
                            <a href="{{ route('users_index', ['filter' => 'admins']) }}" class="btn btn-{{ $activeFilter === 'admins' ? 'primary' : 'outline-primary' }}">Admins</a>
                        </div>
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
                                    @if(Auth::user()->hasRole('Admin'))
                                    <th>Role</th>
                                    @endif
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
                                    @if(Auth::user()->hasRole('Admin'))
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-{{ $role->name == 'Admin' ? 'danger' : ($role->name == 'Employee' ? 'warning' : 'success') }}">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    @endif
                                    <td>${{ number_format($user->credit, 2) }}</td>
                                    <td>
                                        <a class="btn btn-info btn-sm" href="{{ route('users_show', $user->id) }}">View</a>
                                        <a class="btn btn-primary btn-sm" href="{{ route('users_edit', $user->id) }}">Edit</a>
                                        @if(!Auth::user()->hasRole('Admin') && $user->hasRole('Customer'))
                                            <a class="btn btn-success btn-sm" href="{{ route('users_show_add_credit', $user->id) }}">Add Credit</a>
                                
                                        @endif
                                        
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
                        @if(Auth::user()->hasRole('Admin'))
                            @if($activeFilter === 'all')
                                No users found.
                            @elseif($activeFilter === 'employees')
                                No employees found. <a href="{{ route('users_create_employee') }}">Create a new employee</a>.
                            @elseif($activeFilter === 'admins')
                                No administrators found.
                            @else
                                No customers found.
                            @endif
                        @else
                            No customers found.
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
