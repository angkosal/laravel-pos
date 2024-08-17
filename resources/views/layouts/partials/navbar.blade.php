<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="{{route('home')}}" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- SEARCH FORM -->
    {{--
    <form class="form-inline ml-3">
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-navbar" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form>
    --}}

    {{--
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->

      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">{{ __('common.Notifications', ['total' => 15]) }}</span>
    <div class="dropdown-divider"></div>
    <a href="#" class="dropdown-item">
        <i class="fas fa-envelope mr-2"></i> {{ __('common.new_messages', ['total_msg' => 4]) }}
        <span class="float-right text-muted text-sm">{{ __('common.no_mins', ['mins' => 3]) }}</span>
    </a>
    <div class="dropdown-divider"></div>
    <a href="#" class="dropdown-item">
        <i class="fas fa-users mr-2"></i> {{ __('common.total_friend_request', ['total' => 8]) }}
        <span class="float-right text-muted text-sm">{{ __('common.no_hours', ['hours' => 12]) }}</span>
    </a>
    <div class="dropdown-divider"></div>
    <a href="#" class="dropdown-item">
        <i class="fas fa-file mr-2"></i> {{ __('common.total_new_reports', ['total' => 3]) }}
        <span class="float-right text-muted text-sm">{{ __('common.no_days', ['days' => 2]) }}</span>
    </a>
    <div class="dropdown-divider"></div>
    <a href="#" class="dropdown-item dropdown-footer">{{ __('common.see_all') }}</a>
    </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
            <i class="fas fa-th-large"></i>
        </a>
    </li>
    </ul>
    --}}
</nav>
<!-- /.navbar -->
