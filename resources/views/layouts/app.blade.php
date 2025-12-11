<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIMAD - Sistem Manajemen Aset Digital')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <!-- Datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    @stack('styles')
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #1e40af;
            --primary-dark: #1e3a8a;
            --secondary-color: #0891b2;
            --success-color: #059669;
            --warning-color: #f59e0b;
            --danger-color: #dc2626;
            --light-bg: #f8fafc;
            --dark-color: #1e293b;
            --border-color: #e2e8f0;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --navbar-height: 70px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-color);
            line-height: 1.6;
            padding-top: var(--navbar-height);
        }
        
        /* Navbar Styling - Fixed Position */
        .navbar {
            background-color: white !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            height: var(--navbar-height);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .navbar-brand i {
            color: var(--secondary-color);
        }
        
        .navbar .nav-link {
            color: var(--dark-color) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        
        .navbar .nav-link:hover {
            background-color: var(--light-bg);
        }
        
        .navbar .dropdown-menu {
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .navbar .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
        
        /* Sidebar Styling - Fixed Position */
        .sidebar {
            background-color: var(--sidebar-bg);
            color: white;
            height: calc(100vh - var(--navbar-height));
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
            padding: 0;
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1020;
            width: 16.666667%; /* col-lg-2 */
        }
        
        /* Scrollbar styling for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 0.875rem 1.25rem;
            margin: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: white;
            transform: translateX(4px);
        }
        
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 2px 4px rgba(30, 64, 175, 0.4);
        }
        
        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 12px;
            font-size: 1.1rem;
        }
        
        .sidebar-section {
            padding: 1.25rem;
            margin: 1rem 0;
        }
        
        .sidebar-section h6 {
            color: rgba(255,255,255,0.5);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
        }
        
        .opd-info {
            background-color: rgba(255,255,255,0.05);
            padding: 1rem;
            border-radius: 0.5rem;
            border-left: 3px solid var(--secondary-color);
        }
        
        .opd-info p {
            margin-bottom: 0.25rem;
            font-size: 0.95rem;
        }
        
        .opd-info small {
            color: rgba(255,255,255,0.6);
            font-size: 0.8rem;
        }
        
        .stats-widget {
            background-color: rgba(255,255,255,0.05);
            padding: 0.875rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
        }
        
        .stats-widget .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .stats-widget .stat-row:last-child {
            border-bottom: none;
        }
        
        .stats-widget .stat-label {
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
        }
        
        .stats-widget .stat-value {
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        /* Card Styling */
        .card {
            border-radius: 0.75rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            background: white;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            padding: 1.25rem 1.5rem;
            font-size: 1.1rem;
            color: var(--dark-color);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Button Styling */
        .btn {
            border-radius: 0.5rem;
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(30, 64, 175, 0.3);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #0e7490;
        }
        
        .btn-success {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            color: white;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        /* Badge Styling */
        .status-badge {
            padding: 0.375rem 0.875rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }
        
        .badge-aktif { 
            background-color: #d1fae5; 
            color: #065f46; 
        }
        
        .badge-dimutasi { 
            background-color: #fef3c7; 
            color: #92400e; 
        }
        
        .badge-dihapus { 
            background-color: #fee2e2; 
            color: #991b1b; 
        }
        
        .badge-dalam_perbaikan { 
            background-color: #dbeafe; 
            color: #1e40af; 
        }
        
        .badge-nonaktif { 
            background-color: #f1f5f9; 
            color: #475569; 
        }
        
        .badge-valid { 
            background-color: #d1fae5; 
            color: #065f46; 
        }
        
        .badge-belong_diverifikasi { 
            background-color: #fef3c7; 
            color: #92400e; 
        }
        
        .badge-tidak_valid { 
            background-color: #fee2e2; 
            color: #991b1b; 
        }
        
        .badge-disetujui { 
            background-color: #d1fae5; 
            color: #065f46; 
        }
        
        .badge-belum_divalidasi { 
            background-color: #fef3c7; 
            color: #92400e; 
        }
        
        .badge-revisi { 
            background-color: #dbeafe; 
            color: #1e40af; 
        }
        
        .badge-ditolak { 
            background-color: #fee2e2; 
            color: #991b1b; 
        }
        
        /* Table Styling */
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            background-color: var(--light-bg);
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.025em;
            color: var(--dark-color);
            padding: 1rem;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        .table tbody tr {
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.15s;
        }
        
        .table tbody tr:hover {
            background-color: #f8fafc;
        }
        
        /* Tab Styling */
        .nav-tabs {
            border-bottom: 2px solid var(--border-color);
            margin-bottom: 0;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #64748b;
            font-weight: 500;
            padding: 1rem 1.5rem;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }
        
        .nav-tabs .nav-link:hover {
            color: var(--primary-color);
            border-bottom-color: var(--border-color);
        }
        
        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
            background: transparent;
        }
        
        .tab-content {
            background: white;
            border-radius: 0 0 0.75rem 0.75rem;
            padding: 1.5rem;
        }
        
        /* Stat Card Styling */
        .stat-card {
            text-align: center;
            padding: 1.75rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-color);
            background: white;
            transition: all 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card i {
            font-size: 2.5em;
            margin-bottom: 1rem;
        }
        
        .stat-card.stat-primary i { color: var(--primary-color); }
        .stat-card.stat-success i { color: var(--success-color); }
        .stat-card.stat-warning i { color: var(--warning-color); }
        .stat-card.stat-danger i { color: var(--danger-color); }
        .stat-card.stat-secondary i { color: var(--secondary-color); }
        
        .stat-card .stat-value {
            font-size: 2.25em;
            font-weight: 700;
            margin: 0.5rem 0;
            color: var(--dark-color);
        }
        
        .stat-card .stat-label {
            color: #64748b;
            font-size: 0.95em;
            font-weight: 500;
        }
        
        /* Asset Grid */
        .asset-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }
        
        .asset-card {
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            overflow: hidden;
            transition: all 0.2s;
            background: white;
        }
        
        .asset-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .asset-card-header {
            background-color: var(--light-bg);
            padding: 1.25rem;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
        }
        
        .asset-card-body {
            padding: 1.25rem;
        }
        
        /* Quick Actions */
        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.875rem;
            margin-bottom: 1.5rem;
        }
        
        .quick-action-btn {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.25rem;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            color: var(--dark-color);
            text-decoration: none;
            transition: all 0.2s;
            font-weight: 500;
        }
        
        .quick-action-btn:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(30, 64, 175, 0.3);
        }
        
        .quick-action-btn i {
            margin-right: 0.625rem;
            font-size: 1.1em;
        }
        
        /* Content Area - Adjusted for fixed navbar and sidebar */
        main {
            padding: 2rem 1.5rem;
            margin-left: 16.666667%; /* col-lg-2 */
        }
        
        /* Responsive Container */
        .main-container {
            position: relative;
        }
        
        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .page-header p {
            color: #64748b;
            font-size: 1rem;
        }
        
        /* Form Controls */
        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 0.625rem 0.875rem;
            transition: all 0.2s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            body {
                padding-top: var(--navbar-height);
            }
            
            .sidebar {
                position: fixed;
                left: -100%;
                width: 280px;
                transition: left 0.3s ease;
                z-index: 1040;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            main {
                margin-left: 0;
                padding: 1rem;
            }
            
            .stat-card {
                padding: 1.25rem;
            }
            
            .stat-card .stat-value {
                font-size: 1.75em;
            }
            
            /* Mobile sidebar overlay */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: var(--navbar-height);
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1035;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
            
            /* Mobile menu toggle button */
            .mobile-menu-toggle {
                display: inline-block !important;
                padding: 0.5rem;
                margin-right: 0.5rem;
                border: none;
                background: transparent;
                color: var(--primary-color);
                font-size: 1.5rem;
                cursor: pointer;
            }
        }
        
        @media (min-width: 769px) and (max-width: 991px) {
            .sidebar {
                width: 25%; /* col-md-3 */
            }
            
            main {
                margin-left: 25%; /* col-md-3 */
            }
        }
        
        @media (min-width: 992px) {
            .sidebar {
                width: 16.666667%; /* col-lg-2 */
            }
            
            main {
                margin-left: 16.666667%; /* col-lg-2 */
            }
        }
        
        /* Hide mobile menu toggle on desktop */
        .mobile-menu-toggle {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <a class="navbar-brand" href="{{ route('opd.dashboard.index') }}">
                <i class="fas fa-landmark"></i>
                <span>SIMAD</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>
                            {{ Auth::user()->name }}
                            <span class="badge bg-primary ms-1">{{ Auth::user()->display_role }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('opd.profile.index') }}">
                                    <i class="fas fa-user-circle me-2"></i> Profil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div class="container-fluid main-container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 col-md-3 sidebar" id="sidebar">
                <div class="pt-3">
                    <!-- OPD Info -->
                    <div class="sidebar-section">
                        <h6><i class="fas fa-building me-2"></i>Informasi OPD</h6>
                        <div class="opd-info">
                            <p class="fw-bold mb-1">{{ Auth::user()->opdUnit->nama_opd ?? 'OPD Tidak Diketahui' }}</p>
                            <small>{{ Auth::user()->opdUnit->kode_opd ?? '' }}</small>
                        </div>
                    </div>
                    
                    <!-- Navigation Menu -->
                    <ul class="nav flex-column px-2">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('opd/dashboard*') ? 'active' : '' }}" href="{{ route('opd.dashboard.index') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('opd/assets*') ? 'active' : '' }}" href="{{ route('opd.assets.index') }}">
                                <i class="fas fa-boxes"></i> Aset
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('opd/transactions*') ? 'active' : '' }}" href="{{ route('opd.transactions.index') }}">
                                <i class="fas fa-exchange-alt"></i> Transaksi
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('opd/master*') ? 'active' : '' }}" href="{{ route('opd.master.index') }}">
                                <i class="fas fa-database"></i> Data Master
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Quick Stats -->
                    <div class="sidebar-section">
                        <h6><i class="fas fa-chart-pie me-2"></i>Ringkasan</h6>
                        <div class="stats-widget">
                            <div class="stat-row">
                                <span class="stat-label">Total Aset</span>
                                <span class="stat-value" id="sidebar-asset-count">-</span>
                            </div>
                            <div class="stat-row">
                                <span class="stat-label">Aktif</span>
                                <span class="stat-value" id="sidebar-active-count">-</span>
                            </div>
                            <div class="stat-row">
                                <span class="stat-label">Nilai Total</span>
                                <span class="stat-value" id="sidebar-total-value">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <main class="col-lg-10 col-md-9">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Initialize global components
        $(document).ready(function() {
            // Initialize DataTables
            $('.datatable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
                },
                "responsive": true
            });
            
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5'
            });
            
            // Initialize Datepickers
            $('.datepicker').flatpickr({
                dateFormat: "Y-m-d",
                locale: "id"
            });
            
            // CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Load sidebar stats
            loadSidebarStats();
            
            // Mobile sidebar toggle
            $('#sidebarToggle').on('click', function() {
                $('#sidebar').toggleClass('show');
                $('#sidebarOverlay').toggleClass('show');
            });
            
            // Close sidebar when clicking overlay
            $('#sidebarOverlay').on('click', function() {
                $('#sidebar').removeClass('show');
                $('#sidebarOverlay').removeClass('show');
            });
            
            // Close sidebar when clicking a link on mobile
            $('#sidebar .nav-link').on('click', function() {
                if ($(window).width() < 769) {
                    $('#sidebar').removeClass('show');
                    $('#sidebarOverlay').removeClass('show');
                }
            });
        });
        
        // Function to load sidebar statistics
        function loadSidebarStats() {
            $.ajax({
                url: "{{ route('opd.assets.stats') }}",
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#sidebar-asset-count').text(response.stats.total_assets.toLocaleString());
                        $('#sidebar-active-count').text(response.stats.active_assets.toLocaleString());
                        $('#sidebar-total-value').text(formatCurrency(response.stats.total_value));
                    }
                }
            });
        }
        
        // Format currency helper
        function formatCurrency(value) {
            if (value >= 1000000000) {
                return 'Rp ' + (value / 1000000000).toFixed(2) + 'M';
            } else if (value >= 1000000) {
                return 'Rp ' + (value / 1000000).toFixed(2) + 'Jt';
            } else if (value >= 1000) {
                return 'Rp ' + (value / 1000).toFixed(2) + 'K';
            }
            return 'Rp ' + value.toLocaleString('id-ID');
        }
        
        // Show confirmation modal
        function showConfirmationModal(title, message, confirmUrl, confirmMethod = 'POST', confirmButton = 'Ya, Lanjutkan') {
            $('#confirmModalTitle').text(title);
            $('#confirmModalMessage').text(message);
            $('#confirmModalForm').attr('action', confirmUrl);
            $('#confirmModalMethod').val(confirmMethod);
            $('#confirmModalButton').text(confirmButton);
            $('#confirmModal').modal('show');
        }
        
        // Show toast notification
        function showToast(message, type = 'success') {
            const toast = $(`
                <div class="toast align-items-center text-bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `);
            
            $('#toastContainer').append(toast);
            const bsToast = new bootstrap.Toast(toast[0]);
            bsToast.show();
            
            toast.on('hidden.bs.toast', function() {
                $(this).remove();
            });
        }
        
        // Handle AJAX errors
        function handleAjaxError(xhr) {
            let message = 'Terjadi kesalahan. Silakan coba lagi.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.status === 422) {
                message = 'Validasi gagal. Periksa kembali data yang dimasukkan.';
            } else if (xhr.status === 403) {
                message = 'Akses ditolak. Anda tidak memiliki izin untuk melakukan tindakan ini.';
            } else if (xhr.status === 404) {
                message = 'Data tidak ditemukan.';
            }
            showToast(message, 'danger');
        }
    </script>
    
    <!-- Toast Container -->
    <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
        <!-- Toasts will be inserted here -->
    </div>
    
    @stack('scripts')
</body>
</html>