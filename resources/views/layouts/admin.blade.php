<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIMAD - Admin Dashboard')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
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
            --navbar-height: 70px;
            --sidebar-width: 280px;
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 6px 16px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 24px rgba(0, 0, 0, 0.12);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.6;
            font-size: 15px;
            overflow-x: hidden;
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
            display: flex;
            align-items: center;
        }
        
        .navbar-brand {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0;
            margin: 0;
            letter-spacing: -0.5px;
        }
        
        .navbar-brand i {
            color: var(--primary-color);
            font-size: 1.75rem;
        }
        
        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: transparent;
            border: none;
            color: var(--primary-color);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            margin-right: 0.5rem;
            border-radius: var(--border-radius-sm);
            transition: all 0.2s ease;
        }
        
        .mobile-menu-toggle:hover {
            background-color: var(--gray-100);
        }
        
        /* Desktop Sidebar Toggle */
        .sidebar-toggle-btn {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius-sm);
            padding: 0.5rem 0.75rem;
            color: var(--gray-700);
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            margin-left: 1rem;
        }
        
        .sidebar-toggle-btn:hover {
            background-color: var(--gray-100);
            color: var(--primary-color);
            border-color: var(--gray-300);
        }
        
        .sidebar-toggle-btn i {
            font-size: 1.1rem;
        }
        
        .navbar .nav-link {
            color: var(--gray-700) !important;
            font-weight: 600;
            padding: 0.625rem 1rem;
            border-radius: var(--border-radius-sm);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            height: 42px;
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
            padding: 0.65rem 1rem;
            border-radius: var(--border-radius-sm);
            color: var(--gray-700);
            font-weight: 600;
            transition: all 0.2s ease;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
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
            border-radius: 6px;
        }
        
        /* ========================================
           LAYOUT CONTAINER
        ======================================== */
        .layout-container {
            padding-top: var(--navbar-height);
            min-height: 100vh;
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
            border-right: 2px solid var(--gray-200);
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar.collapsed .sidebar-section h6,
        .sidebar.collapsed .admin-info p,
        .sidebar.collapsed .admin-info small,
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
        
        .sidebar.collapsed .admin-info {
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
            background: var(--primary-color);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
        
        .sidebar .nav-link {
            color: var(--gray-700);
            padding: 0.875rem 1.25rem;
            margin: 0.25rem 1rem;
            border-radius: var(--border-radius-sm);
            transition: all 0.2s ease;
            font-weight: 600;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
            text-decoration: none;
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
        
        .sidebar .nav-link.active:hover i {
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .admin-info {
            background-color: var(--gray-50);
            padding: 1rem;
            border-radius: var(--border-radius-sm);
            border-left: 3px solid var(--primary-color);
        }
        
        .admin-info p {
            margin-bottom: 0.25rem;
            font-size: 0.95rem;
            color: var(--gray-900);
            font-weight: 600;
        }
        
        .admin-info small {
            color: var(--gray-500);
            font-size: 0.8rem;
            font-weight: 500;
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
            font-weight: 600;
        }
        
        .stats-widget .stat-value {
            color: var(--gray-900);
            font-weight: 700;
            font-size: 0.9rem;
        }
        
        /* Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1015;
        }
        
        .sidebar-overlay.show {
            display: block;
        }
        
        /* ========================================
           MAIN CONTENT
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
            font-family: 'Outfit', sans-serif;
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
           CARD STYLING
        ======================================== */
        .card {
            border-radius: var(--border-radius);
            border: 2px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            background: var(--white);
            overflow: hidden;
        }
        
        .card-header {
            background-color: var(--gray-50);
            border-bottom: 2px solid var(--gray-200);
            font-weight: 700;
            padding: 1.25rem 1.5rem;
            font-size: 1.125rem;
            color: var(--gray-900);
            font-family: 'Outfit', sans-serif;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .card-custom {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            background: var(--white);
        }
        
        .card-custom .card-header {
            background-color: var(--gray-50);
            border-bottom: 2px solid var(--gray-200);
            font-weight: 700;
            padding: 1.25rem 1.5rem;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
        }
        
        /* ========================================
           BUTTON STYLING
        ======================================== */
        .btn {
            border-radius: var(--border-radius-sm);
            padding: 0.65rem 1.25rem;
            font-weight: 600;
            transition: all 0.2s ease;
            border: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            min-height: 42px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            color: var(--white);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            color: var(--white);
        }
        
        .btn-secondary:hover {
            background-color: #0284c7;
            color: var(--white);
            transform: translateY(-1px);
        }
        
        .btn-success {
            background-color: var(--success-color);
            color: var(--white);
        }
        
        .btn-success:hover {
            background-color: #059669;
            color: var(--white);
            transform: translateY(-1px);
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            color: var(--white);
        }
        
        .btn-warning:hover {
            background-color: #d97706;
            color: var(--white);
            transform: translateY(-1px);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: var(--white);
        }
        
        .btn-danger:hover {
            background-color: #dc2626;
            color: var(--white);
            transform: translateY(-1px);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background-color: transparent;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: var(--white);
            border-color: var(--primary-color);
        }
        
        .btn-outline-secondary {
            border: 2px solid var(--gray-300);
            color: var(--gray-700);
            background-color: transparent;
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--gray-100);
            color: var(--gray-900);
            border-color: var(--gray-400);
        }
        
        .btn-outline-light {
            border: 2px solid var(--gray-200);
            color: var(--gray-700);
            background-color: var(--white);
        }
        
        .btn-outline-light:hover {
            background-color: var(--gray-100);
            color: var(--gray-900);
            border-color: var(--gray-300);
        }
        
        .btn-sm {
            padding: 0.4rem 0.85rem;
            font-size: 0.85rem;
            min-height: 36px;
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
        
        .badge-status {
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .badge-pending { 
            background-color: #fef3c7; 
            color: #92400e; 
        }
        
        .badge-approved { 
            background-color: #d1fae5; 
            color: #065f46; 
        }
        
        .badge-rejected { 
            background-color: #fee2e2; 
            color: #991b1b; 
        }
        
        .badge-completed { 
            background-color: #dbeafe; 
            color: #1e40af; 
        }
        
        .badge {
            border-radius: 6px;
            padding: 0.35em 0.65em;
            font-weight: 600;
            font-size: 0.75rem;
        }
        
        /* ========================================
           TABLE STYLING
        ======================================== */
        .table-responsive {
            border-radius: var(--border-radius);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table {
            margin-bottom: 0;
            border-radius: var(--border-radius);
            overflow: hidden;
            border: 2px solid var(--gray-200);
        }
        
        .table th {
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
            padding: 1rem;
            font-family: 'Outfit', sans-serif;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
            color: var(--gray-700);
            border-bottom: 1px solid var(--gray-200);
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .table tbody tr {
            background-color: var(--white);
        }
        
        .table tbody tr:hover {
            background-color: var(--gray-50);
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: var(--gray-50);
        }
        
        .table-actions {
            white-space: nowrap;
            width: 150px;
        }
        
        /* ========================================
           FORM CONTROLS
        ======================================== */
        .form-control, .form-select {
            border: 2px solid var(--gray-300);
            border-radius: var(--border-radius-sm);
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
            font-size: 0.95rem;
            font-weight: 500;
            background-color: var(--white);
            min-height: 44px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background-color: var(--white);
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
            border: 2px solid;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        
        .alert i {
            font-size: 1.2rem;
            margin-right: 0.75rem;
            padding: 0.5rem;
            border-radius: 8px;
            min-width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border-color: var(--success-color);
        }
        
        .alert-success i {
            background-color: var(--success-color);
            color: var(--white);
        }
        
        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border-color: var(--danger-color);
        }
        
        .alert-danger i {
            background-color: var(--danger-color);
            color: var(--white);
        }
        
        .alert-warning {
            background-color: #fef3c7;
            color: #92400e;
            border-color: var(--warning-color);
        }
        
        .alert-warning i {
            background-color: var(--warning-color);
            color: var(--white);
        }
        
        .alert-info {
            background-color: #dbeafe;
            color: #1e40af;
            border-color: var(--info-color);
        }
        
        .alert-info i {
            background-color: var(--info-color);
            color: var(--white);
        }
        
        /* ========================================
           STAT CARD STYLING
        ======================================== */
        .stat-card {
            text-align: center;
            padding: 2rem 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            border: 2px solid var(--gray-200);
            background: var(--white);
            transition: all 0.2s ease;
        }
        
        .stat-card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--gray-300);
            transform: translateY(-2px);
        }
        
        .stat-card i, .stat-card .icon {
            font-size: 2.5em;
            margin-bottom: 1rem;
        }
        
        .stat-card.stat-primary { border-top: 4px solid var(--primary-color); }
        .stat-card.stat-primary i, .stat-card.stat-primary .icon { color: var(--primary-color); }
        
        .stat-card.stat-success { border-top: 4px solid var(--success-color); }
        .stat-card.stat-success i, .stat-card.stat-success .icon { color: var(--success-color); }
        
        .stat-card.stat-warning { border-top: 4px solid var(--warning-color); }
        .stat-card.stat-warning i, .stat-card.stat-warning .icon { color: var(--warning-color); }
        
        .stat-card.stat-danger { border-top: 4px solid var(--danger-color); }
        .stat-card.stat-danger i, .stat-card.stat-danger .icon { color: var(--danger-color); }
        
        .stat-card.stat-secondary { border-top: 4px solid var(--secondary-color); }
        .stat-card.stat-secondary i, .stat-card.stat-secondary .icon { color: var(--secondary-color); }
        
        .stat-card .stat-value {
            font-size: 2.25em;
            font-weight: 700;
            margin: 0.5rem 0;
            color: var(--gray-900);
            font-family: 'Outfit', sans-serif;
        }
        
        .stat-card .stat-label {
            color: var(--gray-600);
            font-size: 0.95em;
            font-weight: 600;
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
            transition: all 0.2s ease;
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
            box-shadow: var(--shadow-sm);
        }
        
        /* ========================================
           RESPONSIVE
        ======================================== */
        @media (max-width: 768px) {
            body {
                font-size: 14px;
            }
            
            .navbar .container-fluid {
                padding: 0 0.75rem;
            }
            
            .navbar-brand {
                font-size: 1.1rem;
            }
            
            .navbar-brand i {
                font-size: 1.3rem;
            }
            
            .mobile-menu-toggle {
                display: inline-block;
            }
            
            .sidebar-toggle-btn {
                display: none !important;
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
                padding: 1rem 0.75rem;
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
            
            .page-header h1 {
                font-size: 1.5rem;
            }
            
            .card-header {
                padding: 0.85rem 1rem;
                font-size: 0.95rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .btn {
                padding: 0.6rem 1rem;
                font-size: 0.85rem;
            }
            
            .btn-sm {
                padding: 0.4rem 0.75rem;
                font-size: 0.8rem;
            }
            
            .table thead th,
            .table tbody td {
                padding: 0.75rem 0.75rem;
                font-size: 0.85rem;
            }
            
            .alert {
                padding: 0.85rem;
                font-size: 0.85rem;
            }
            
            .alert i {
                font-size: 1.1rem;
                min-width: 32px;
                height: 32px;
            }
        }
        
        @media (max-width: 576px) {
            .navbar .container-fluid {
                padding: 0 0.5rem;
            }
            
            .navbar-brand {
                font-size: 1rem;
            }
            
            .navbar-brand span {
                max-width: 120px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            
            .page-header h1 {
                font-size: 1.35rem;
            }
            
            .card-header {
                font-size: 0.9rem;
            }
            
            .table thead th,
            .table tbody td {
                padding: 0.6rem 0.5rem;
                font-size: 0.8rem;
            }
        }
        
        /* Selection */
        ::selection {
            background: rgba(37, 99, 235, 0.2);
            color: var(--gray-900);
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
            
            <a class="navbar-brand" href="{{ route('admin.dashboard.index') }}">
                <i class="fas fa-cubes"></i>
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
                    <li class="nav-item">
                        <span class="nav-link">
                            <span class="badge bg-primary">{{ now()->translatedFormat('l, d F Y') }}</span>
                        </span>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i>
                            <span>{{ Auth::user()->name }}</span>
                            <span class="badge bg-success">{{ Auth::user()->display_role }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                                    <i class="fas fa-cog"></i> Pengaturan
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

    <!-- Layout Container -->
    <div class="layout-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="pt-3">
                <!-- Admin Info -->
                <div class="sidebar-section">
                    <h6><i class="fas fa-user-shield"></i> Administrator</h6>
                    <div class="admin-info">
                        <p class="mb-1">{{ Auth::user()->name }}</p>
                        <small>{{ Auth::user()->display_role }}</small>
                    </div>
                </div>
                
                <!-- Navigation Menu -->
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}" href="{{ route('admin.dashboard.index') }}">
                            <i class="fas fa-tachometer-alt"></i> 
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.assets.*') ? 'active' : '' }}" href="{{ route('admin.assets.index') }}">
                            <i class="fas fa-cube"></i> 
                            <span>Aset</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.proposals.*') ? 'active' : '' }}" href="{{ route('admin.proposals.index') }}">
                            <i class="fas fa-exchange-alt"></i> 
                            <span>Proposal</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                            <i class="fas fa-chart-bar"></i> 
                            <span>Laporan</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.audits.*') ? 'active' : '' }}" href="{{ route('admin.audits.index') }}">
                            <i class="fas fa-clipboard-check"></i> 
                            <span>Audit</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                            <i class="fas fa-cog"></i> 
                            <span>Pengaturan</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Content Area -->
        <main>
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ session('warning') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle"></i>
                    <span>{{ session('info') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('errors'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Beberapa error terjadi:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach(session('errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <!-- Page Content -->
            @yield('content')
        </main>
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
            
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
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
                document.body.style.overflow = $('#sidebar').hasClass('show') ? 'hidden' : '';
            });
            
            // Close sidebar when clicking overlay
            $('#sidebarOverlay').on('click', function() {
                $('#sidebar').removeClass('show');
                $('#sidebarOverlay').removeClass('show');
                document.body.style.overflow = '';
            });
            
            // Close sidebar when clicking a link on mobile
            $('#sidebar .nav-link').on('click', function() {
                if ($(window).width() < 769) {
                    $('#sidebar').removeClass('show');
                    $('#sidebarOverlay').removeClass('show');
                    document.body.style.overflow = '';
                }
            });
            
            // Handle window resize
            $(window).on('resize', function() {
                if ($(window).width() >= 769) {
                    $('#sidebar').removeClass('show');
                    $('#sidebarOverlay').removeClass('show');
                    document.body.style.overflow = '';
                }
            });
            
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        });
        
        // Function to load sidebar statistics
        function loadSidebarStats() {
            // You can implement AJAX call to load stats
            // Example placeholder values
            $('#sidebar-asset-count').text('-');
            $('#sidebar-proposal-count').text('-');
            $('#sidebar-total-value').text('-');
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
        
        // Confirmation for delete actions
        function confirmAction(message, formId) {
            if (confirm(message)) {
                document.getElementById(formId).submit();
            }
        }
        
        // Tab functionality
        function switchTab(tabId, targetElement) {
            // Hide all tab contents
            document.querySelectorAll('.tab-pane').forEach(tab => {
                tab.classList.remove('show', 'active');
            });
            
            // Show selected tab content
            document.getElementById(tabId).classList.add('show', 'active');
            
            // Update active tab link
            document.querySelectorAll('.nav-tabs .nav-link').forEach(link => {
                link.classList.remove('active');
            });
            targetElement.classList.add('active');
            
            // Update URL if needed
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.pushState({}, '', url);
        }
    </script>
    
    <!-- Toast Container -->
    <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
        <!-- Toasts will be inserted here -->
    </div>
    
    @stack('scripts')
</body>
</html>