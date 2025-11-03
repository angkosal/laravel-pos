<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Styles & Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 240px;
            min-height: 100vh;
            background-color: #1e293b; /* Warna sidebar gelap */
        }

        .sidebar h3 {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .sidebar .nav-link {
            color: #d1d5db;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.2s;
        }

        .sidebar .nav-link:hover {
            background-color: #334155;
            color: #fff;
        }

        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: #fff;
        }

        .main-content {
            flex: 1;
            background-color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .navbar {
            background-color: #fff !important;
            border-bottom: 1px solid #e5e7eb;
        }

        main {
            background-color: #f9fafb;
            min-height: calc(100vh - 70px);
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div id="app" class="d-flex">

        <!-- Sidebar -->
        <aside class="sidebar p-3">
            <h3 class="mb-4 text-center">{{ config('app.name', 'Laravel') }}</h3>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active fw-bold' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active fw-bold' : '' }}">
                        <i class="bi bi-box-seam me-2"></i> Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active fw-bold' : '' }}">
                        <i class="bi bi-people me-2"></i> Pelanggan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active fw-bold' : '' }}">
                        <i class="bi bi-cart-check me-2"></i> Transaksi
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active fw-bold' : '' }}">
                        <i class="bi bi-truck me-2"></i> Supplier
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('absensi.index') }}" class="nav-link {{ request()->routeIs('absensi.*') ? 'active fw-bold' : '' }}">
                        <i class="bi bi-calendar-check me-2"></i> Absensi
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active fw-bold' : '' }}">
                        <i class="bi bi-file-earmark-text me-2"></i> Laporan
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main content -->
        <div class="main-content flex-fill">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-md navbar-light shadow-sm">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <span class="navbar-brand fw-semibold">{{ Auth::user()->getFullname() ?? 'Admin' }}</span>

                    <ul class="navbar-nav ms-auto">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    {{ Auth::user()->getFullname() }}
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>
                                    </li>
                                </ul>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        @endguest
                    </ul>
                </div>
            </nav>

            <!-- Page content -->
            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
