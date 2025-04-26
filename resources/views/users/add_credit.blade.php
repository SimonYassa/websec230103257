@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Add Credit to User Account') }}</div>

                <div class="card-body">
                    <div class="mb-4">
                        <h5>User: {{ $user->name }}</h5>
                        <p>Current Credit: ${{ number_format($user->credit, 2) }}</p>
                    </div>

                    <form method="POST" action="{{ route('users_add_credit', $user->id) }}">
                        @csrf

                        <div class="form-group row mb-3">
                            <label for="amount" class="col-md-4 col-form-label text-md-right">{{ __('Amount to Add ($)') }}</label>

                            <div class="col-md-6">
                                <input id="amount" type="number" step="0.01" min="0.01" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required>

                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Add Credit') }}
                                </button>
                                <a href="{{ route('users_index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

