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
            --sidebar-width: 96px;
        }

        body{
            font-family:"Poppins",sans-serif;
            background:#f6f7f5;
            color:#2b2b2b;
        }

        .main-sidebar{
            background:#fff !important;
            color:#111;
            width:var(--sidebar-width) !important;
        }

        .main-header,
        .content-wrapper,
        .main-footer{
            margin-left: var(--sidebar-width) !important;
            transition: margin .2s ease;
        }

        .content-wrapper{
            background:#f3f5f4;
            border-radius:20px 0 0 0;
            padding:20px;
            min-height:100vh;
        }

        /* =======================
        🔥 MOBILE & TABLET FIX
        ======================= */
        @media (max-width: 991.98px){
            .main-header,
            .content-wrapper,
            .main-footer{
                margin-left: 0 !important;
            }

            .content-wrapper{
                padding:12px;
                border-radius:0;
            }

            h1{
                font-size:20px;
            }
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
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@yield('js')
@yield('model')

{{-- 🔥 WAJIB agar script Chart.js dan grafik bisa muncul --}}
@stack('scripts')

</body>
</html>
