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
            --primary-color: #2563eb;
            --primary-dark: #1e40af;
            --primary-light: #3b82f6;
            --secondary-color: #0ea5e9;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --dark-color: #0f172a;
            --gray-900: #0f172a;
            --gray-800: #1e293b;
            --gray-700: #334155;
            --gray-600: #475569;
            --gray-500: #64748b;
            --gray-400: #94a3b8;
            --gray-300: #cbd5e1;
            --gray-200: #e2e8f0;
            --gray-100: #f1f5f9;
            --gray-50: #f8fafc;
            --white: #ffffff;
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --navbar-height: 70px;
            --sidebar-width: 280px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.6;
            padding-top: var(--navbar-height);
            font-size: 15px;
        }
        
        /* ========================================
           NAVBAR STYLING
        ======================================== */
        .navbar {
            background-color: var(--white) !important;
            box-shadow: var(--shadow);
            padding: 0;
            border-bottom: 1px solid var(--gray-200);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            height: var(--navbar-height);
        }
        
        .navbar .container-fluid {
            padding: 0 1.5rem;
            height: 100%;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0;
            margin: 0;
        }
        
        .navbar-brand i {
            color: var(--primary-color);
            font-size: 1.75rem;
        }
        
        .navbar-brand span {
            letter-spacing: -0.5px;
        }
        
        .navbar .nav-link {
            color: var(--gray-700) !important;
            font-weight: 500;
            padding: 0.625rem 1rem;
            border-radius: var(--border-radius-sm);
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .navbar .nav-link:hover {
            background-color: var(--gray-100);
            color: var(--primary-color) !important;
        }
        
        .navbar .dropdown-menu {
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-lg);
            border-radius: var(--border-radius);
            margin-top: 0.5rem;
            padding: 0.5rem;
            min-width: 200px;
        }
        
        .navbar .dropdown-item {
            padding: 0.625rem 1rem;
            border-radius: var(--border-radius-sm);
            color: var(--gray-700);
            font-weight: 500;
            transition: all 0.15s ease;
        }
        
        .navbar .dropdown-item:hover {
            background-color: var(--gray-100);
            color: var(--primary-color);
        }
        
        .navbar .dropdown-divider {
            margin: 0.5rem 0;
            border-color: var(--gray-200);
        }
        
        .navbar .badge {
            font-weight: 600;
            padding: 0.35em 0.65em;
            font-size: 0.75rem;
        }
        
        /* Sidebar Toggle Button */
        .sidebar-toggle-btn {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius-sm);
            padding: 0.5rem 0.75rem;
            color: var(--gray-700);
            cursor: pointer;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .sidebar-toggle-btn:hover {
            background-color: var(--gray-100);
            color: var(--primary-color);
        }
        
        .sidebar-toggle-btn i {
            font-size: 1.1rem;
        }
        
        /* ========================================
           SIDEBAR STYLING
        ======================================== */
        .sidebar {
            background-color: var(--white);
            color: var(--gray-700);
            height: calc(100vh - var(--navbar-height));
            box-shadow: var(--shadow);
            padding: 0;
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1020;
            width: var(--sidebar-width);
            border-right: 1px solid var(--gray-200);
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar.collapsed .sidebar-section h6,
        .sidebar.collapsed .opd-info p,
        .sidebar.collapsed .opd-info small,
        .sidebar.collapsed .stats-widget,
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        
        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 0.875rem 0;
        }
        
        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }
        
        .sidebar.collapsed .opd-info {
            text-align: center;
            padding: 0.5rem;
        }
        
        .sidebar.collapsed .sidebar-section {
            padding: 0.5rem;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }
        
        .sidebar .nav-link {
            color: var(--gray-700);
            padding: 0.875rem 1.25rem;
            margin: 0.25rem 1rem;
            border-radius: var(--border-radius-sm);
            transition: all 0.15s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
        }
        
        .sidebar .nav-link:hover {
            background-color: var(--gray-100);
            color: var(--primary-color);
        }
        
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: var(--white);
            box-shadow: var(--shadow-sm);
        }
        
        .sidebar .nav-link.active i {
            color: var(--white);
        }
        
        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 12px;
            font-size: 1.1rem;
            color: var(--gray-500);
        }
        
        .sidebar .nav-link:hover i {
            color: var(--primary-color);
        }
        
        .sidebar .nav-link.active:hover {
            background-color: var(--primary-dark);
            color: var(--white);
        }
        
        .sidebar-section {
            padding: 1.25rem 1rem;
            margin: 1rem 0;
        }
        
        .sidebar-section h6 {
            color: var(--gray-500);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
            padding: 0 0.25rem;
        }
        
        .opd-info {
            background-color: var(--gray-50);
            padding: 1rem;
            border-radius: var(--border-radius-sm);
            border-left: 3px solid var(--primary-color);
        }
        
        .opd-info p {
            margin-bottom: 0.25rem;
            font-size: 0.95rem;
            color: var(--gray-900);
        }
        
        .opd-info small {
            color: var(--gray-500);
            font-size: 0.8rem;
        }
        
        .stats-widget {
            background-color: var(--gray-50);
            padding: 1rem;
            border-radius: var(--border-radius-sm);
            margin-top: 1rem;
        }
        
        .stats-widget .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.625rem 0;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .stats-widget .stat-row:last-child {
            border-bottom: none;
        }
        
        .stats-widget .stat-label {
            color: var(--gray-600);
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .stats-widget .stat-value {
            color: var(--gray-900);
            font-weight: 700;
            font-size: 0.9rem;
        }
        
        /* ========================================
           CARD STYLING
        ======================================== */
        .card {
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            background: var(--white);
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-200);
            font-weight: 600;
            padding: 1.25rem 1.5rem;
            font-size: 1.125rem;
            color: var(--gray-900);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* ========================================
           BUTTON STYLING
        ======================================== */
        .btn {
            border-radius: var(--border-radius-sm);
            padding: 0.625rem 1.25rem;
            font-weight: 600;
            transition: all 0.15s ease;
            border: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            color: var(--white);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            color: var(--white);
        }
        
        .btn-secondary:hover {
            background-color: #0284c7;
            color: var(--white);
        }
        
        .btn-success {
            background-color: var(--success-color);
            color: var(--white);
        }
        
        .btn-success:hover {
            background-color: #059669;
            color: var(--white);
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            color: var(--white);
        }
        
        .btn-warning:hover {
            background-color: #d97706;
            color: var(--white);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: var(--white);
        }
        
        .btn-danger:hover {
            background-color: #dc2626;
            color: var(--white);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background-color: transparent;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: var(--white);
        }
        
        /* ========================================
           BADGE STYLING
        ======================================== */
        .status-badge {
            padding: 0.375rem 0.875rem;
            border-radius: 6px;
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
            background-color: var(--gray-200); 
            color: var(--gray-700); 
        }
        
        .badge-valid { 
            background-color: #d1fae5; 
            color: #065f46; 
        }
        
        .badge-belum_diverifikasi { 
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
        
        /* ========================================
           TABLE STYLING
        ======================================== */
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            background-color: var(--gray-50);
            border-top: none;
            border-bottom: 2px solid var(--gray-200);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: var(--gray-700);
            padding: 1rem;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
            color: var(--gray-700);
            border-bottom: 1px solid var(--gray-200);
        }
        
        .table tbody tr:hover {
            background-color: var(--gray-50);
        }
        
        /* ========================================
           TAB STYLING
        ======================================== */
        .nav-tabs {
            border-bottom: 2px solid var(--gray-200);
            margin-bottom: 0;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: var(--gray-600);
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-bottom: 3px solid transparent;
            transition: all 0.15s ease;
        }
        
        .nav-tabs .nav-link:hover {
            color: var(--primary-color);
            border-bottom-color: var(--gray-300);
        }
        
        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
            background: transparent;
        }
        
        .tab-content {
            background: var(--white);
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            padding: 1.5rem;
        }
        
        /* ========================================
           STAT CARD STYLING
        ======================================== */
        .stat-card {
            text-align: center;
            padding: 2rem 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            border: 1px solid var(--gray-200);
            background: var(--white);
            transition: all 0.15s ease;
        }
        
        .stat-card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--gray-300);
        }
        
        .stat-card i {
            font-size: 2.5em;
            margin-bottom: 1rem;
        }
        
        .stat-card.stat-primary { border-top: 4px solid var(--primary-color); }
        .stat-card.stat-primary i { color: var(--primary-color); }
        
        .stat-card.stat-success { border-top: 4px solid var(--success-color); }
        .stat-card.stat-success i { color: var(--success-color); }
        
        .stat-card.stat-warning { border-top: 4px solid var(--warning-color); }
        .stat-card.stat-warning i { color: var(--warning-color); }
        
        .stat-card.stat-danger { border-top: 4px solid var(--danger-color); }
        .stat-card.stat-danger i { color: var(--danger-color); }
        
        .stat-card.stat-secondary { border-top: 4px solid var(--secondary-color); }
        .stat-card.stat-secondary i { color: var(--secondary-color); }
        
        .stat-card .stat-value {
            font-size: 2.25em;
            font-weight: 700;
            margin: 0.5rem 0;
            color: var(--gray-900);
        }
        
        .stat-card .stat-label {
            color: var(--gray-600);
            font-size: 0.95em;
            font-weight: 600;
        }
        
        /* ========================================
           CONTENT AREA
        ======================================== */
        main {
            padding: 2rem 1.5rem;
            margin-left: var(--sidebar-width);
            min-height: calc(100vh - var(--navbar-height));
            transition: margin-left 0.3s ease;
        }
        
        main.expanded {
            margin-left: 70px;
        }
        
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }
        
        .page-header p {
            color: var(--gray-600);
            font-size: 1rem;
        }
        
        /* ========================================
           FORM CONTROLS
        ======================================== */
        .form-control, .form-select {
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius-sm);
            padding: 0.625rem 0.875rem;
            transition: all 0.15s ease;
            font-size: 0.95rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        /* ========================================
           ALERT STYLING
        ======================================== */
        .alert {
            border-radius: var(--border-radius);
            border: none;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border-left-color: var(--success-color);
        }
        
        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border-left-color: var(--danger-color);
        }
        
        .alert-warning {
            background-color: #fef3c7;
            color: #92400e;
            border-left-color: var(--warning-color);
        }
        
        .alert-info {
            background-color: #dbeafe;
            color: #1e40af;
            border-left-color: var(--info-color);
        }
        
        /* ========================================
           QUICK ACTIONS
        ======================================== */
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
            background: var(--white);
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius-sm);
            color: var(--gray-700);
            text-decoration: none;
            transition: all 0.15s ease;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .quick-action-btn:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: var(--white);
            text-decoration: none;
        }
        
        .quick-action-btn i {
            margin-right: 0.625rem;
            font-size: 1.1em;
        }
        
        /* ========================================
           ASSET GRID
        ======================================== */
        .asset-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }
        
        .asset-card {
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            overflow: hidden;
            transition: all 0.15s ease;
            background: var(--white);
        }
        
        .asset-card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--gray-300);
        }
        
        .asset-card-header {
            background-color: var(--gray-50);
            padding: 1.25rem;
            border-bottom: 1px solid var(--gray-200);
            font-weight: 600;
        }
        
        .asset-card-body {
            padding: 1.25rem;
        }
        
        /* ========================================
           TIMELINE STYLING
        ======================================== */
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }
        
        .timeline-marker {
            position: absolute;
            left: -30px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: var(--primary-color);
            border: 3px solid var(--white);
            box-shadow: 0 0 0 2px var(--primary-color);
        }
        
        .timeline-content {
            border-left: 2px solid var(--gray-200);
            padding-left: 20px;
            padding-bottom: 20px;
        }
        
        .timeline-item:last-child .timeline-content {
            border-left: none;
        }
        
        /* ========================================
           ACTION BUTTONS
        ======================================== */
        .action-buttons .btn {
            margin-right: 0.25rem;
            margin-bottom: 0.25rem;
        }
        
        /* ========================================
           RESPONSIVE
        ======================================== */
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
            
            .sidebar.collapsed {
                width: 280px;
            }
            
            main {
                margin-left: 0;
                padding: 1rem;
            }
            
            main.expanded {
                margin-left: 0;
            }
            
            .stat-card {
                padding: 1.25rem;
            }
            
            .stat-card .stat-value {
                font-size: 1.75em;
            }
            
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
            
            .sidebar-toggle-btn {
                display: none !important;
            }
        }
        
        .mobile-menu-toggle {
            display: none;
        }
        
        @media (min-width: 769px) and (max-width: 991px) {
            .sidebar {
                width: 260px;
            }
            
            .sidebar.collapsed {
                width: 70px;
            }
            
            main {
                margin-left: 260px;
            }
            
            main.expanded {
                margin-left: 70px;
            }
        }
        
        @media (min-width: 992px) {
            .sidebar {
                width: var(--sidebar-width);
            }
            
            .sidebar.collapsed {
                width: 70px;
            }
            
            main {
                margin-left: var(--sidebar-width);
            }
            
            main.expanded {
                margin-left: 70px;
            }
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
            
            <!-- Desktop Sidebar Toggle -->
            <button class="sidebar-toggle-btn d-none d-md-flex" id="sidebarToggleDesktop">
                <i class="fas fa-bars"></i>
            </button>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i>
                            <span>{{ Auth::user()->name }}</span>
                            <span class="badge bg-primary">Admin OPD</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('opd.profile.index') }}">
                                    <i class="fas fa-user-circle"></i> Profil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i> Logout
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
            <div class="sidebar" id="sidebar">
                <div class="pt-3">
                    <!-- OPD Info -->
                    <div class="sidebar-section">
                        <h6><i class="fas fa-building"></i> Informasi OPD</h6>
                        <div class="opd-info">
                            <p class="fw-bold mb-1">{{ Auth::user()->opdUnit->nama_opd ?? 'OPD Tidak Diketahui' }}</p>
                            <small>{{ Auth::user()->opdUnit->kode_opd ?? '' }}</small>
                        </div>
                    </div>
                    
                    <!-- Navigation Menu -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('opd.dashboard') ? 'active' : '' }}" href="{{ route('opd.dashboard.index') }}">
                                <i class="fas fa-tachometer-alt"></i> 
                                <span>Dashboard</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('opd.assets.*') ? 'active' : '' }}" href="{{ route('opd.assets.index') }}">
                                <i class="fas fa-boxes"></i> 
                                <span>Aset</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('opd.transactions.*') ? 'active' : '' }}" href="{{ route('opd.transactions.index') }}">
                                <i class="fas fa-exchange-alt"></i> 
                                <span>Transaksi</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('opd.master.*') ? 'active' : '' }}" href="{{ route('opd.master.index') }}">
                                <i class="fas fa-database"></i> 
                                <span>Data Master</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('opd.profile') ? 'active' : '' }}" href="{{ route('opd.profile.index') }}">
                                <i class="fas fa-user"></i> 
                                <span>Profil</span>
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Quick Stats -->
                    <div class="sidebar-section">
                        <h6><i class="fas fa-chart-pie"></i> Ringkasan</h6>
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
            <main>
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <!-- Page Content -->
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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Initialize global components
        $(document).ready(function() {
            // Initialize DataTables
            $('.data-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
                },
                "responsive": true,
                "pageLength": 25
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
            
            // Desktop sidebar toggle
            $('#sidebarToggleDesktop').on('click', function() {
                $('#sidebar').toggleClass('collapsed');
                $('main').toggleClass('expanded');
                
                // Save state to localStorage
                const isCollapsed = $('#sidebar').hasClass('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            });
            
            // Load sidebar state from localStorage
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (sidebarCollapsed) {
                $('#sidebar').addClass('collapsed');
                $('main').addClass('expanded');
            }
            
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
            
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        });
        
        // Function to load sidebar statistics
        function loadSidebarStats() {
            $.ajax({
                url: "{{ route('opd.assets.stats') }}",
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#sidebar-asset-count').text(response.stats.total_assets.toLocaleString('id-ID'));
                        $('#sidebar-active-count').text(response.stats.active_assets.toLocaleString('id-ID'));
                        $('#sidebar-total-value').text(formatCurrency(response.stats.total_value));
                    }
                }
            });
        }
        
        // Format currency helper
        function formatCurrency(value) {
            if (value >= 1000000000) {
                return 'Rp ' + (value / 1000000000).toFixed(1) + 'M';
            } else if (value >= 1000000) {
                return 'Rp ' + (value / 1000000).toFixed(1) + 'Jt';
            } else if (value >= 1000) {
                return 'Rp ' + (value / 1000).toFixed(0) + 'K';
            }
            return 'Rp ' + value.toLocaleString('id-ID');
        }
        
        // Show toast notification
        function showToast(message, type = 'success') {
            const bgColor = type === 'success' ? 'bg-success' : type === 'danger' ? 'bg-danger' : type === 'warning' ? 'bg-warning' : 'bg-info';
            const icon = type === 'success' ? 'fa-check-circle' : type === 'danger' ? 'fa-exclamation-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
            
            const toast = $(`
                <div class="toast align-items-center text-white ${bgColor} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas ${icon} me-2"></i>
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
    </script>
    
    <!-- Toast Container -->
    <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
        <!-- Toasts will be inserted here -->
    </div>
    
    @stack('scripts')
</body>
</html>