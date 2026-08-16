@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')
@inject('preloaderHelper', 'JeroenNoten\LaravelAdminLte\Helpers\PreloaderHelper')

@section('adminlte_css')
    @stack('css')
    @yield('css')
    <style>
        /* =========================================================
           Avalon Solutions - Sidebar Dark Theme
           Palette: deep slate with cyan accent for active items
           ========================================================= */
        :root {
            --sidebar-bg: #1e293b;          /* slate-800 */
            --sidebar-bg-2: #0f172a;        /* slate-900 (brand) */
            --sidebar-bg-3: #334155;        /* slate-700 (hover) */
            --sidebar-accent: #06b6d4;      /* cyan-500 (active) */
            --sidebar-accent-soft: rgba(6, 182, 212, 0.18);
            --sidebar-text: #e2e8f0;        /* slate-200 */
            --sidebar-text-muted: #94a3b8;  /* slate-400 */
            --sidebar-border: rgba(255, 255, 255, 0.08);
        }

        /* Main sidebar shell ------------------------------------------------- */
        .main-sidebar,
        .main-sidebar .sidebar {
            background: linear-gradient(180deg, #1e293b 0%, #172033 100%) !important;
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.25);
            border-right: 1px solid var(--sidebar-border);
        }

        /* Brand / logo area -------------------------------------------------- */
        .main-sidebar .brand-link {
            background-color: var(--sidebar-bg-2) !important;
            border-bottom: 1px solid var(--sidebar-border) !important;
            color: #f8fafc !important;
            padding: 14px 16px;
            transition: background-color 0.2s ease;
        }

        .main-sidebar .brand-link:hover {
            background-color: #020617 !important;
            color: #ffffff !important;
        }

        .main-sidebar .brand-link .brand-image {
            box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.35);
            opacity: 1;
        }

        .main-sidebar .brand-text {
            color: #f1f5f9 !important;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        /* Sidebar user panel (when enabled) ---------------------------------- */
        .sidebar .user-panel {
            border-bottom: 1px solid var(--sidebar-border) !important;
            padding: 16px 12px;
        }

        .sidebar .user-panel .info a {
            color: #f1f5f9 !important;
            font-weight: 600;
        }

        .sidebar .user-panel .info small,
        .sidebar .user-panel .info .text-muted {
            color: var(--sidebar-text-muted) !important;
        }

        /* Section headers ---------------------------------------------------- */
        .main-sidebar .nav-sidebar .nav-header {
            color: var(--sidebar-text-muted) !important;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 1.2px;
            padding: 18px 16px 8px;
            text-transform: uppercase;
            opacity: 0.85;
        }

        /* Menu items ---------------------------------------------------------- */
        .main-sidebar .nav-sidebar > .nav-item > .nav-link {
            color: var(--sidebar-text) !important;
            padding: 10px 14px;
            border-left: 3px solid transparent;
            border-radius: 0;
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        .main-sidebar .nav-sidebar > .nav-item > .nav-link .nav-icon,
        .main-sidebar .nav-sidebar > .nav-item > .nav-link i {
            color: var(--sidebar-text-muted);
            margin-right: 10px;
            width: 1.25rem;
            text-align: center;
            transition: color 0.2s ease;
        }

        /* Hover / open ------------------------------------------------------- */
        .main-sidebar .nav-sidebar > .nav-item.menu-open > .nav-link,
        .main-sidebar .nav-sidebar > .nav-item:hover > .nav-link,
        .main-sidebar .nav-sidebar > .nav-item > .nav-link:focus {
            background-color: rgba(255, 255, 255, 0.06) !important;
            color: #ffffff !important;
            border-left-color: rgba(6, 182, 212, 0.5);
        }

        .main-sidebar .nav-sidebar > .nav-item:hover > .nav-link .nav-icon,
        .main-sidebar .nav-sidebar > .nav-item:hover > .nav-link i,
        .main-sidebar .nav-sidebar > .nav-item.menu-open > .nav-link .nav-icon,
        .main-sidebar .nav-sidebar > .nav-item.menu-open > .nav-link i {
            color: var(--sidebar-accent);
        }

        /* Active item -------------------------------------------------------- */
        .main-sidebar .nav-sidebar > .nav-item > .nav-link.active {
            background-color: var(--sidebar-accent-soft) !important;
            color: #ffffff !important;
            border-left-color: var(--sidebar-accent);
            box-shadow: inset 0 0 0 1px rgba(6, 182, 212, 0.15);
        }

        .main-sidebar .nav-sidebar > .nav-item > .nav-link.active .nav-icon,
        .main-sidebar .nav-sidebar > .nav-item > .nav-link.active i {
            color: var(--sidebar-accent);
        }

        /* Right caret on items with submenu ---------------------------------- */
        .main-sidebar .nav-sidebar .nav-link .right,
        .main-sidebar .nav-sidebar .nav-link p .right {
            color: var(--sidebar-text-muted);
        }

        /* Sub-menu (treeview) ------------------------------------------------ */
        .main-sidebar .nav-sidebar .nav-treeview {
            background-color: rgba(0, 0, 0, 0.18) !important;
            border-radius: 0;
            padding: 4px 0;
        }

        .main-sidebar .nav-sidebar .nav-treeview > .nav-item > .nav-link {
            color: #cbd5e1 !important;
            padding: 8px 14px 8px 44px;
            border-left: 3px solid transparent;
        }

        .main-sidebar .nav-sidebar .nav-treeview > .nav-item > .nav-link:hover,
        .main-sidebar .nav-sidebar .nav-treeview > .nav-item > .nav-link.active {
            color: #ffffff !important;
            background-color: rgba(6, 182, 212, 0.12) !important;
            border-left-color: var(--sidebar-accent);
        }

        .main-sidebar .nav-sidebar .nav-treeview > .nav-item > .nav-link .nav-icon {
            color: var(--sidebar-text-muted);
        }

        /* Search box --------------------------------------------------------- */
        .main-sidebar .form-control-sidebar,
        .main-sidebar .btn-sidebar {
            background-color: rgba(255, 255, 255, 0.06) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            color: #f1f5f9 !important;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .main-sidebar .form-control-sidebar:focus {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border-color: var(--sidebar-accent) !important;
            box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.2) !important;
            color: #ffffff !important;
        }

        .main-sidebar .form-control-sidebar::placeholder {
            color: rgba(148, 163, 184, 0.8) !important;
        }

        .main-sidebar .btn-sidebar:hover {
            background-color: rgba(6, 182, 212, 0.18) !important;
            color: var(--sidebar-accent) !important;
        }

        /* Scrollbar ---------------------------------------------------------- */
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 3px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(6, 182, 212, 0.4);
        }
    </style>
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

@section('body')
    <div class="wrapper">

        {{-- Preloader Animation (fullscreen mode) --}}
        @if($preloaderHelper->isPreloaderEnabled())
            @include('adminlte::partials.common.preloader')
        @endif

        {{-- Top Navbar --}}
        @if($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.navbar.navbar-layout-topnav')
        @else
            @include('adminlte::partials.navbar.navbar')
        @endif

        {{-- Left Main Sidebar --}}
        @if(!$layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.sidebar.left-sidebar')
        @endif

        {{-- Content Wrapper --}}
        @empty($iFrameEnabled)
            @include('adminlte::partials.cwrapper.cwrapper-default')
        @else
            @include('adminlte::partials.cwrapper.cwrapper-iframe')
        @endempty

        {{-- Footer --}}
        @include('adminlte::partials.footer.footer')

        {{-- Right Control Sidebar --}}
        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.sidebar.right-sidebar')
        @endif

    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
    @include('partials.notifications')
    @include('partials.receipt-popup')
@stop
