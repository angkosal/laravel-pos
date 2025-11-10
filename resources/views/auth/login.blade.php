@extends('layouts.auth')

@section('title', 'Login - Deeen Coffee')

@section('css')
<style>
    :root {
        --green-dark: #1f3d34;
        --radius: 20px;
        --shadow: 0 10px 30px rgba(0, 0, 0, 0.08), 0 2px 6px rgba(0, 0, 0, 0.06);
    }

    body {
        margin: 0;
        height: 100vh;
        background: url('{{ asset('images/background.jpg') }}') no-repeat center center fixed;
        background-size: cover;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: "Poppins", sans-serif;
    }

    .login-container {
        text-align: center;
    }

    /* Logo Pill */
    .logo-box {
        background-color: var(--green-dark);
        color: white;
        width: 220px;
        padding: 25px 0;
        border-radius: 40px;
        margin: 0 auto 30px auto;
        box-shadow: var(--shadow);
    }

    .logo-box h1 {
        font-size: 42px;
        font-weight: 700;
        margin: 0;
        letter-spacing: 1px;
    }

    .logo-box span {
        display: block;
        font-size: 13px;
        letter-spacing: 3px;
        margin-top: 5px;
    }

    /* Login Card */
    .login-box {
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid #dfe6e2;
        border-radius: var(--radius);
        padding: 35px 40px;
        box-shadow: var(--shadow);
        width: 400px;
        text-align: left;
        backdrop-filter: blur(6px);
    }

    .login-box h2 {
        text-align: center;
        font-weight: 700;
        margin-bottom: 5px;
        color: var(--green-dark);
    }

    .login-box p {
        text-align: center;
        font-size: 13px;
        color: #444;
        margin-bottom: 25px;
    }

    label {
        font-weight: 600;
        color: var(--green-dark);
        font-size: 13px;
        display: block;
        margin-bottom: 6px;
    }

    .form-control {
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 10px;
        font-size: 14px;
        margin-bottom: 15px;
        transition: all 0.2s ease;
        background: #f8f9f8;
    }

    .form-control:focus {
        border-color: var(--green-dark);
        background: white;
        outline: none;
        box-shadow: 0 0 0 3px rgba(31, 61, 52, 0.1);
    }

    .btn-login {
        width: 100%;
        background-color: var(--green-dark);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-login:hover {
        background-color: #244f43;
    }

    .register-link {
        text-align: center;
        margin-top: 18px;
        font-size: 13px;
    }

    .register-link a {
        color: var(--green-dark);
        font-weight: 600;
        text-decoration: none;
        transition: 0.2s;
    }

    .register-link a:hover {
        text-decoration: underline;
    }

    .invalid-feedback {
        display: block;
        font-size: 12px;
        color: #d9534f;
        margin-top: -8px;
        margin-bottom: 10px;
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <!-- Logo -->
    <div class="logo-box">
        <h1>Deeen</h1>
        <span>COFFEE</span>
    </div>

    <!-- Form -->
    <div class="login-box">
        <h2>LOGIN</h2>
        <p>Welcome to Deeen Coffee</p>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <label for="email">Username</label>
            <input type="email" name="email" id="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror

            <label for="password">Password</label>
            <input type="password" name="password" id="password"
                class="form-control @error('password') is-invalid @enderror"
                required autocomplete="current-password">
            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror

            <button type="submit" class="btn-login">Login</button>
        </form>

        <!-- Register Link -->
        @if (Route::has('register'))
        <div class="register-link">
            Belum punya akun?
            <a href="{{ route('register') }}">Daftar Sekarang</a>
        </div>
        @endif
    </div>
</div>
@endsection
