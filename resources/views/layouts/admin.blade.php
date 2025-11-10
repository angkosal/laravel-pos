<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite(['resources/sass/app.scss','resources/js/app.js'])
    @yield('css')

    <script>
        window.APP = <?php echo json_encode([
            'currency_symbol'  => config('settings.currency_symbol'),
            'warning_quantity' => config('settings.warning_quantity')
        ]) ?>;
    </script>

    <style>
        :root{
            --green:#1f3d34;
            --muted:#9aa5a0;
            --ring:#e9efec;

            /* ==== UBAH LEBAR SIDEBAR DI SINI ==== */
            /* 96px untuk sidebar slim (ikon di atas, label di bawah) */
            /* 250px untuk sidebar lebar klasik */
            --sidebar-width: 96px;
        }

        body{
            font-family:"Poppins",sans-serif;
            background:#f6f7f5;
            color:#2b2b2b;
        }

        /* ====== Sidebar putih + logo pill ====== */
        .main-sidebar{
            background:#fff !important;
            color:#111;
            width:var(--sidebar-width) !important;
        }
        .brand-link{
            background:#fff;
            border-bottom:1px solid var(--ring);
            padding:22px 0 !important;
            text-align:center;
        }
        .brand-link .pill{
            background:var(--green); color:#fff; width:64px; margin:0 auto;
            border-radius:18px; padding:8px 0; line-height:1.05;
            box-shadow:0 6px 14px rgba(0,0,0,.12);
        }
        .brand-link .pill .top{font-weight:700; font-size:16px; letter-spacing:.3px}
        .brand-link .pill .sub{font-size:8px; letter-spacing:3px; opacity:.95}

        /* ====== Sinkron margin konten dgn lebar sidebar ====== */
        .main-header, .content-wrapper, .main-footer{
            margin-left: var(--sidebar-width) !important;
            transition: margin .2s ease;
        }

        /* ====== MENU VERTIKAL (ikon di atas, label di bawah) ====== */
        .nav-sidebar.vertical .nav-link{
            background:transparent !important; border:0;
            margin:8px 10px; padding:8px 6px;
            display:flex; flex-direction:column; align-items:center; gap:6px;
            width: calc(var(--sidebar-width) - 20px);
            text-align:center; color:#98a29d !important;
            border-radius:14px; transition:.15s; font-weight:500;
        }
        .menu-icon-pill{
            width:56px; height:56px; border-radius:14px;
            display:flex; align-items:center; justify-content:center;
            background:#ecefed; color:#9aa5a0;
        }
        .menu-icon-pill i{font-size:20px}
        .menu-label{font-size:11px; line-height:1; color:#9aa5a0}
        .nav-sidebar.vertical .nav-link:hover{background:#f4f6f5 !important}
        .nav-sidebar.vertical .nav-link.active .menu-icon-pill{
            background:var(--green); color:#fff; box-shadow:0 8px 18px rgba(31,61,52,.25);
        }
        .nav-sidebar.vertical .nav-link.active .menu-label{color:#fff}

        /* Sembunyikan style horizontal default saat memakai layout vertikal */
        .nav-sidebar.vertical .nav-link .nav-icon,
        .nav-sidebar.vertical .nav-link p{display:none !important}

        /* ===== Content area ===== */
        .content-wrapper{
            background:#f3f5f4;
            border-radius:20px 0 0 0;
            padding:20px;
            min-height:100vh;
        }
        .content-header h1{
            font-weight:700;
            color:var(--green);
            margin:0;
        }

        /* Responsif: kecilkan margin saat layar kecil */
        @media (max-width: 991.98px){
            .main-header, .content-wrapper, .main-footer{ margin-left: 0 !important; }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

    @include('layouts.partials.navbar')
    @include('layouts.partials.sidebar')

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-end">
                    <div class="col-sm-6">
                        <h1>@yield('content-header')</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        @yield('content-actions')
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="content">
            @include('layouts.partials.alert.success')
            @include('layouts.partials.alert.error')
            @yield('content')
        </section>
    </div>

    @include('layouts.partials.footer')

    <aside class="control-sidebar control-sidebar-dark"></aside>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@yield('js')
@yield('model')
</body>
</html>
