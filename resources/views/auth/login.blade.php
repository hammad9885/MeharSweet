@extends('layouts.user')

@section('content')
<div class="text-center w-75 m-auto">
    <h4 class="text-dark-50 text-center pb-0 fw-bold">Sign In</h4>
</div>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">{{ __('Email Address') }}</label>
        <input id="email" type="email"
               class="form-control @error('email') is-invalid @enderror"
               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        @error('email')
            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">{{ __('Password') }}</label>
        <input id="password" type="password"
               class="form-control @error('password') is-invalid @enderror"
               name="password" required autocomplete="current-password">
        @error('password')
            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    {{-- Company dropdown --}}


    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember"
                   id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
        </div>
    </div>

    <div class="row mb-0">
        <div class="col-md-8 offset-md-4">
            <button type="submit" class="btn btn-primary">{{ __('Login') }}</button>
        </div>
    </div>
</form>

@endsection
