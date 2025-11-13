<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('home')}}" class="brand-link">
        <img src="{{ asset('images/logo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{route('home')}}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __('dashboard.title') }}</p>
                    </a>
                </li>

                <!-- Products -->
                <li class="nav-item">
                    <a href="{{ route('products.index') }}" class="nav-link {{ activeSegment('products') }}">
                        <i class="nav-icon fas fa-th-large"></i>
                        <p>{{ __('product.title') }}</p>
                    </a>
                </li>

                <li class="nav-header">{{ __('Sales') }}</li>
                <!-- POS Cart -->
                <li class="nav-item">
                    <a href="{{ route('cart.index') }}" class="nav-link {{ activeSegment('cart') }}">
                        <i class="nav-icon fas fa-cart-plus"></i>
                        <p>{{ __('POS') }}</p>
                    </a>
                </li>

                <!-- Orders -->
                <li class="nav-item">
                    <a href="{{ route('orders.index') }}" class="nav-link {{ activeSegment('orders') }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>{{ __('Order List') }}</p>
                    </a>
                </li>

                <!-- Customers -->
                <li class="nav-item">
                    <a href="{{ route('customers.index') }}" class="nav-link {{ activeSegment('customers') }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>{{ __('customer.title') }}</p>
                    </a>
                </li>

                <li class="nav-header">{{ __('Purchases') }}</li>
                <!-- Purchases (Dropdown) -->
                <li class="nav-item {{ request()->routeIs('purchases.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ activeSegment('purchases') }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>
                            {{ __('Purchases') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('purchases.create') }}" class="nav-link {{ request()->routeIs('purchases.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('New Purchase') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('All Purchases') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Suppliers -->
                <li class="nav-item">
                    <a href="{{ route('suppliers.index') }}" class="nav-link {{ activeSegment('suppliers') }}">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>{{ __('Supplier') }}</p>
                    </a>
                </li>

                <li class="nav-header">{{ __('Extra') }}</li>
                <!-- Settings -->
                <li class="nav-item">
                    <a href="{{ route('settings.index') }}" class="nav-link {{ activeSegment('settings') }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>{{ __('settings.title') }}</p>
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>{{ __('common.Logout') }}</p>
                    </a>
                    <form action="{{route('logout')}}" method="POST" id="logout-form" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
