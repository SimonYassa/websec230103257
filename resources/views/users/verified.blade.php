@extends('layouts.master')
@section('title', 'Email Verified')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Email Verified') }}</div>

                <div class="card-body">
                    <div class="alert alert-success">
                        <strong>Congratulations!</strong> Dear {{ $user->name }}, your email {{ $user->email }} has been verified successfully.
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            {{ __('Proceed to Login') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection