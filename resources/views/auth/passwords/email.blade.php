@extends('layouts.auth')

@section('content')

<p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="form-group">

        <div class="input-group">
            <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email"
                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        @error('email')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Request new password</button>
        </div>
        <!-- /.col -->
    </div>
</form>

<p class="mt-3 mb-1">
    <a href="{{ route('login') }}">Login</a>
</p>
<p class="mb-0">
    <a href="{{ route('register') }}" class="text-center">Register a new membership</a>
</p>

@endsection