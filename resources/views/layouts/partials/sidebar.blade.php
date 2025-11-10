<!-- Main Sidebar Container -->
<aside class="main-sidebar elevation-0">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
        <div class="pill">
            <div class="top">Deeen</div>
            <div class="sub">COFFEE</div>
        </div>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu (VERTIKAL: ikon di atas, label di bawah) -->
        <nav class="mt-4">
            <ul class="nav flex-column align-items-center sidebar-menu">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link {{ activeSegment('') }}">
                        <div class="menu-pill {{ activeSegment('') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </div>
                    </a>
                </li>

                <!-- Absensi -->
                <li class="nav-item">
                    <a href="{{ route('absensi.index') }}" class="nav-link {{ activeSegment('absensi') }}">
                        <div class="menu-pill {{ activeSegment('absensi') ? 'active' : '' }}">
                            <i class="fas fa-user"></i>
                            <span>Absensi</span>
                        </div>
                    </a>
                </li>

                <!-- Laporan -->
                <li class="nav-item">
                    <a href="{{ route('laporan.index') }}" class="nav-link {{ activeSegment('laporan') }}">
                        <div class="menu-pill {{ activeSegment('laporan') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            <span>Laporan</span>
                        </div>
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-item mt-3">
                    <a href="#" class="nav-link" onclick="document.getElementById('logout-form').submit()">
                        <div class="menu-pill logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" id="logout-form" class="d-none">
                            @csrf
                        </form>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<style>
    :root {
        --green: #1f3d34;
        --green-hover: #294f3d;
        --muted: #9aa5a0;
        --light-bg: #f4f6f5;
    }

    .main-sidebar {
        width: 100px;
        background: #fff !important;
        border-right: 1px solid #e9efec;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .brand-link {
        background: #fff;
        padding: 25px 0 !important;
        text-align: center;
        border-bottom: 1px solid #e9efec;
    }

    .pill {
        background: var(--green);
        color: #fff;
        width: 130px;
        border-radius: 20px;
        padding: 10px 0;
        line-height: 1;
    }

    .pill .top {
        font-size: 22px;
        font-weight: 700;
    }

    .pill .sub {
        font-size: 10px;
        letter-spacing: 3px;
    }

    .sidebar-menu {
        margin-top: 20px;
        padding-left: 0;
        width: 100%;
    }

    .menu-pill {
        background: var(--light-bg);
        border-radius: 20px;
        width: 70px;
        height: 70px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: all 0.3s ease;
        margin: 0 auto 12px;
    }

    .menu-pill i {
        font-size: 22px;
        color: var(--muted);
        margin-bottom: 5px;
    }

    .menu-pill span {
        font-size: 11px;
        color: var(--muted);
        font-weight: 500;
    }

    .menu-pill:hover {
        background: #e9efec;
    }

    .menu-pill.active {
        background: var(--green);
    }

    .menu-pill.active i,
    .menu-pill.active span {
        color: #fff !important;
    }

    .logout {
        background: #fdecea;
    }

    .logout:hover {
        background: #f8d7da;
    }
</style>
