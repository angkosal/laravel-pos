@extends('layouts.auth')

@section('title', 'Register - Deeen Coffee')

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

    .register-container {
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

    /* Register Card */
    .register-box {
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid #dfe6e2;
        border-radius: var(--radius);
        padding: 35px 40px;
        box-shadow: var(--shadow);
        width: 420px;
        text-align: left;
        backdrop-filter: blur(6px);
    }

    .register-box h2 {
        text-align: center;
        font-weight: 700;
        margin-bottom: 5px;
        color: var(--green-dark);
    }

    .register-box p {
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

    .btn-register {
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

    .btn-register:hover {
        background-color: #244f43;
    }

    .login-link {
        text-align: center;
        margin-top: 18px;
        font-size: 13px;
    }

    .login-link a {
        color: var(--green-dark);
        font-weight: 600;
        text-decoration: none;
        transition: 0.2s;
    }

    .login-link a:hover {
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
<div class="register-container">
    <!-- Logo -->
    <div class="logo-box">
        <h1>Deeen</h1>
        <span>COFFEE</span>
    </div>

    <!-- Register Form -->
    <div class="register-box">
        <h2>REGISTER</h2>
        <p>Create your Deeen Coffee account</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <label for="first_name">First Name</label>
            <input id="first_name" type="text"
                class="form-control @error('first_name') is-invalid @enderror"
                name="first_name" value="{{ old('first_name') }}" required autofocus>
            @error('first_name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror

            <label for="last_name">Last Name</label>
            <input id="last_name" type="text"
                class="form-control @error('last_name') is-invalid @enderror"
                name="last_name" value="{{ old('last_name') }}" required>
            @error('last_name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror

            <label for="email">Email Address</label>
            <input id="email" type="email"
                class="form-control @error('email') is-invalid @enderror"
                name="email" value="{{ old('email') }}" required autocomplete="email">
            @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror

            <label for="password">Password</label>
            <input id="password" type="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password" required autocomplete="new-password">
            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror

            <label for="password-confirm">Confirm Password</label>
            <input id="password-confirm" type="password"
                class="form-control" name="password_confirmation" required autocomplete="new-password">

            <button type="submit" class="btn-register">Register</button>
        </form>

        <div class="login-link">
            Sudah punya akun?
            <a href="{{ route('login') }}">Login di sini</a>
        </div>
    </div>
</div>
@endsection
