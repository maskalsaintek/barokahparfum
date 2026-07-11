<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Barokah Parfum back office">
    <meta name="author" content="Barokah Parfum">
    <link rel="icon" href="{{ asset('viho/assets/images/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('viho/assets/images/favicon.png') }}" type="image/x-icon">
    <title>@yield('title', 'Barokah Parfum')</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('viho/assets/css/fontawesome.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('viho/assets/css/icofont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('viho/assets/css/themify.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('viho/assets/css/flag-icon.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('viho/assets/css/feather-icon.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('viho/assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('viho/assets/css/style.css') }}">
    <link id="color" rel="stylesheet" href="{{ asset('viho/assets/css/color-1.css') }}" media="screen">
    <link rel="stylesheet" type="text/css" href="{{ asset('viho/assets/css/responsive.css') }}">

    <style>
        :root {
            --bp-border: #e6edef;
            --bp-muted: #6c757d;
            --bp-page: #f8f9fe;
        }

        body {
            background: var(--bp-page);
            letter-spacing: 0;
        }

        .brand-wordmark {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #24695c;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0;
            text-decoration: none;
            white-space: nowrap;
        }

        .brand-wordmark img {
            width: 34px;
            height: 34px;
        }

        .main-header-left .logo-wrapper,
        .main-header-left .dark-logo-wrapper {
            padding-left: 20px;
        }

        .main-nav .nav-menu > li > a.active,
        .main-nav .nav-menu > li > a:hover {
            color: #24695c;
        }

        .main-nav .nav-menu > li > a.active svg,
        .main-nav .nav-menu > li > a:hover svg {
            color: #24695c;
        }

        .sidebar-user {
            padding: 24px 16px 18px;
        }

        .sidebar-user .avatar-initial {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: linear-gradient(135deg, #24695c, #ba895d);
            color: #fff;
            font-size: 24px;
            font-weight: 800;
        }

        .page-body .page-header .breadcrumb {
            margin-bottom: 0;
        }

        .app-page-content > h1:first-child {
            display: none;
        }

        .app-page-content > .btn:first-of-type,
        .app-page-content > form + .btn,
        .app-page-content > .text-end,
        .app-page-content > h3 {
            margin-bottom: 16px;
        }

        .app-page-content > form,
        .app-page-content .report-panel,
        .app-page-content > .card,
        .app-page-content > table,
        .app-page-content > .table-responsive,
        .app-page-content > p {
            margin-bottom: 20px;
        }

        .app-page-content > form:not([id]),
        .app-page-content > table,
        .app-page-content > .table-responsive,
        .app-page-content > p,
        .app-page-content .report-panel,
        .app-page-content .report-table {
            background: #fff;
            border: 1px solid var(--bp-border);
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(89, 102, 122, .06);
        }

        .app-page-content > form:not([id]) {
            padding: 18px;
        }

        .app-page-content > p {
            padding: 22px;
            color: var(--bp-muted);
        }

        .app-page-content table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .app-page-content table th,
        .app-page-content table td {
            padding: 12px 14px;
            vertical-align: middle;
            border-bottom: 1px solid var(--bp-border);
        }

        .app-page-content table thead th {
            color: #2c323f;
            background: #f7f8fa;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .app-page-content table tbody tr:hover {
            background: #fbfcff;
        }

        .app-page-content table tbody tr:last-child td {
            border-bottom: 0;
        }

        .app-page-content input[type="text"],
        .app-page-content input[type="date"],
        .app-page-content input[type="datetime-local"],
        .app-page-content input[type="number"],
        .app-page-content select,
        .app-page-content textarea {
            width: 100%;
            min-height: 40px;
            padding: 8px 12px;
            border: 1px solid #d7e2e9;
            border-radius: 6px;
            background: #fff;
            color: #2c323f;
        }

        .app-page-content textarea {
            min-height: 82px;
        }

        .app-page-content input:focus,
        .app-page-content select:focus,
        .app-page-content textarea:focus {
            outline: 0;
            border-color: #24695c;
            box-shadow: 0 0 0 3px rgba(36, 105, 92, .12);
        }

        .app-page-content label {
            display: block;
            margin-bottom: 6px;
            color: #2c323f;
            font-size: 13px;
            font-weight: 700;
        }

        .field {
            margin-bottom: 16px;
        }

        .error {
            margin-top: 5px;
            color: #d22d3d;
            font-size: 13px;
        }

        .alert {
            border-radius: 8px;
        }

        .pagination {
            gap: 4px;
            flex-wrap: wrap;
        }

        .page-link {
            border-radius: 6px;
            color: #24695c;
        }

        .page-item.active .page-link {
            background: #24695c;
            border-color: #24695c;
        }

        .report-page {
            display: grid;
            gap: 18px;
        }

        .report-hero {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: flex-end;
            padding: 22px;
            background: linear-gradient(135deg, #ffffff 0%, #eef9f7 55%, #fff8f1 100%);
            border: 1px solid var(--bp-border);
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(89, 102, 122, .06);
        }

        .report-eyebrow {
            margin: 0 0 6px;
            color: #24695c;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .report-subtitle {
            margin: 0;
            color: var(--bp-muted);
            max-width: 720px;
        }

        .report-period {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 6px 10px;
            border: 1px solid #d8eee9;
            border-radius: 999px;
            background: #eef9f7;
            color: #24695c;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
        }

        .report-panel,
        .report-table,
        .metric-card {
            background: #fff;
            border: 1px solid var(--bp-border);
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(89, 102, 122, .06);
        }

        .report-panel {
            padding: 16px;
        }

        .report-filter {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 12px;
            align-items: end;
        }

        .report-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 12px;
        }

        .metric-card {
            min-height: 112px;
            padding: 16px;
        }

        .metric-label {
            color: var(--bp-muted);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .metric-value {
            margin-top: 8px;
            font-size: clamp(24px, 3vw, 34px);
            line-height: 1.1;
            font-weight: 800;
        }

        .metric-note {
            margin-top: 8px;
            color: var(--bp-muted);
            font-size: 13px;
        }

        .report-section-title {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            padding: 16px 16px 0;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 999px;
            background: #eef9f7;
            color: #24695c;
            font-weight: 800;
        }

        .empty-state {
            margin: 0;
            padding: 28px 16px;
            color: var(--bp-muted);
            text-align: center;
        }

        @media (max-width: 720px) {
            .report-hero {
                display: grid;
                padding: 16px;
            }

            .report-period {
                width: fit-content;
                white-space: normal;
            }

            .report-actions .btn {
                flex: 1;
            }

            .app-page-content table th,
            .app-page-content table td {
                padding: 10px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="loader-wrapper">
    <div class="theme-loader">
        <div class="loader-p"></div>
    </div>
</div>

<div class="page-wrapper compact-wrapper" id="pageWrapper">
    <div class="page-main-header">
        <div class="main-header-right row m-0">
            <div class="main-header-left">
                <div class="logo-wrapper">
                    <a class="brand-wordmark" href="{{ url('/') }}">
                        <img src="{{ asset('viho/assets/images/logo/icon-logo.png') }}" alt="">
                        <span>Barokah Parfum</span>
                    </a>
                </div>
                <div class="dark-logo-wrapper">
                    <a class="brand-wordmark" href="{{ url('/') }}">
                        <img src="{{ asset('viho/assets/images/logo/icon-logo.png') }}" alt="">
                        <span>Barokah Parfum</span>
                    </a>
                </div>
                <div class="toggle-sidebar">
                    <i class="status_toggle middle" data-feather="align-center" id="sidebar-toggle"></i>
                </div>
            </div>

            <div class="left-menu-header col">
                <ul>
                    <li>
                        <form class="form-inline search-form" method="GET" action="{{ route('fragrances.index') }}">
                            <div class="search-bg">
                                <i class="fa fa-search"></i>
                                <input class="form-control-plaintext" name="q" placeholder="Search fragrance...">
                            </div>
                        </form>
                    </li>
                </ul>
            </div>

            <div class="nav-right col pull-right right-menu p-0 box-col-6">
                <ul class="nav-menus">
                    <li>
                        <a class="text-dark" href="#!" onclick="javascript:toggleFullScreen()">
                            <i data-feather="maximize"></i>
                        </a>
                    </li>
                    <li>
                        <div class="mode">
                            <i class="fa fa-moon-o"></i>
                        </div>
                    </li>
                    <li class="onhover-dropdown p-0">
                        <button class="btn btn-primary-light" type="button">
                            <i data-feather="user"></i> Admin
                        </button>
                    </li>
                </ul>
            </div>

            <div class="d-lg-none mobile-toggle pull-right w-auto">
                <i data-feather="more-horizontal"></i>
            </div>
        </div>
    </div>

    <div class="page-body-wrapper sidebar-icon">
        <header class="main-nav">
            <div class="sidebar-user text-center">
                <span class="avatar-initial">BP</span>
                <h6 class="mt-3 f-14 f-w-600">Barokah Parfum</h6>
                <p class="mb-0 font-roboto">Inventory & Sales</p>
            </div>

            <nav>
                <div class="main-navbar">
                    <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
                    <div id="mainnav">
                        <ul class="nav-menu custom-scrollbar">
                            <li class="back-btn">
                                <div class="mobile-back text-end">
                                    <span>Back</span>
                                    <i class="fa fa-angle-right ps-2" aria-hidden="true"></i>
                                </div>
                            </li>
                            <li class="sidebar-main-title">
                                <div><h6>Menu</h6></div>
                            </li>
                            <li class="dropdown">
                                <a class="nav-link menu-title link-nav {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                                    <i data-feather="home"></i><span>Home</span>
                                </a>
                            </li>
                            <li class="dropdown">
                                <a class="nav-link menu-title link-nav {{ request()->routeIs('fragrances.*') ? 'active' : '' }}" href="{{ route('fragrances.index') }}">
                                    <i data-feather="droplet"></i><span>Fragrances</span>
                                </a>
                            </li>
                            <li class="dropdown">
                                <a class="nav-link menu-title link-nav {{ request()->routeIs('variant-types.*') ? 'active' : '' }}" href="{{ route('variant-types.index') }}">
                                    <i data-feather="tag"></i><span>Variant Types</span>
                                </a>
                            </li>
                            <li class="dropdown">
                                <a class="nav-link menu-title link-nav {{ request()->routeIs('product-variants.*') ? 'active' : '' }}" href="{{ route('product-variants.index') }}">
                                    <i data-feather="package"></i><span>Product Variants</span>
                                </a>
                            </li>
                            <li class="dropdown">
                                <a class="nav-link menu-title link-nav {{ request()->routeIs('sales-orders.*') ? 'active' : '' }}" href="{{ route('sales-orders.index') }}">
                                    <i data-feather="shopping-cart"></i><span>Sales Orders</span>
                                </a>
                            </li>
                            <li class="sidebar-main-title">
                                <div><h6>Reports</h6></div>
                            </li>
                            <li class="dropdown">
                                <a class="nav-link menu-title link-nav {{ request()->routeIs('dashboard.profit') ? 'active' : '' }}" href="{{ route('dashboard.profit') }}">
                                    <i data-feather="bar-chart-2"></i><span>Profit Dashboard</span>
                                </a>
                            </li>
                            <li class="dropdown">
                                <a class="nav-link menu-title link-nav {{ request()->routeIs('reports.best-seller-fragrances') ? 'active' : '' }}" href="{{ route('reports.best-seller-fragrances') }}">
                                    <i data-feather="award"></i><span>Best Sellers</span>
                                </a>
                            </li>
                            <li class="dropdown">
                                <a class="nav-link menu-title link-nav {{ request()->routeIs('reports.total-profit') ? 'active' : '' }}" href="{{ route('reports.total-profit') }}">
                                    <i data-feather="trending-up"></i><span>Total Profit</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
                </div>
            </nav>
        </header>

        <div class="page-body">
            <div class="container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3>@yield('title', 'Barokah Parfum')</h3>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                                <li class="breadcrumb-item active">@yield('title', 'Dashboard')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid app-page-content">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @yield('content')
            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 footer-copyright">
                        <p class="mb-0">Copyright {{ date('Y') }} © Barokah Parfum.</p>
                    </div>
                    <div class="col-md-6">
                        <p class="pull-right mb-0">Back office powered by Viho UI</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

<script src="{{ asset('viho/assets/js/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('viho/assets/js/icons/feather-icon/feather.min.js') }}"></script>
<script src="{{ asset('viho/assets/js/icons/feather-icon/feather-icon.js') }}"></script>
<script src="{{ asset('viho/assets/js/sidebar-menu.js') }}"></script>
<script src="{{ asset('viho/assets/js/config.js') }}"></script>
<script src="{{ asset('viho/assets/js/bootstrap_bundle.js') }}"></script>
<script src="{{ asset('viho/assets/js/fullscreen.js') }}"></script>
<script src="{{ asset('viho/assets/js/tooltip-init.js') }}"></script>
<script src="{{ asset('viho/assets/js/script.js') }}"></script>

@stack('scripts')
</body>
</html>
