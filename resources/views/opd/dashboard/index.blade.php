@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-3 mb-4 border-bottom">
        <h1 class="h3">Dashboard OPD: {{ $opdUnit->nama_opd }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Dashboard Tabs -->
    <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'overview' ? 'active' : '' }}" 
                    id="overview-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#overview"
                    type="button"
                    onclick="window.location.href='{{ route('opd.dashboard.index') }}?tab=overview'">
                <i class="fas fa-tachometer-alt me-1"></i> Overview
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'statistics' ? 'active' : '' }}" 
                    id="statistics-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#statistics"
                    type="button"
                    onclick="window.location.href='{{ route('opd.dashboard.index') }}?tab=statistics'">
                <i class="fas fa-chart-bar me-1"></i> Statistik
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'maintenance' ? 'active' : '' }}" 
                    id="maintenance-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#maintenance"
                    type="button"
                    onclick="window.location.href='{{ route('opd.dashboard.index') }}?tab=maintenance'">
                <i class="fas fa-tools me-1"></i> Pemeliharaan
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'activities' ? 'active' : '' }}" 
                    id="activities-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#activities"
                    type="button"
                    onclick="window.location.href='{{ route('opd.dashboard.index') }}?tab=activities'">
                <i class="fas fa-history me-1"></i> Aktivitas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'quick-actions' ? 'active' : '' }}" 
                    id="quick-actions-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#quick-actions"
                    type="button"
                    onclick="window.location.href='{{ route('opd.dashboard.index') }}?tab=quick-actions'">
                <i class="fas fa-bolt me-1"></i> Quick Actions
            </button>
        </li>
    </ul>

    <div class="tab-content" id="dashboardTabsContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade {{ $tab == 'overview' ? 'show active' : '' }}" id="overview">
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card" style="background-color: #e3f2fd;">
                        <i class="fas fa-boxes text-primary"></i>
                        <div class="stat-value" id="totalAssets">
                            {{ number_format($dashboardData['asset_statistics']['total_assets'] ?? 0) }}
                        </div>
                        <div class="stat-label">Total Aset</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="background-color: #e8f5e9;">
                        <i class="fas fa-check-circle text-success"></i>
                        <div class="stat-value" id="verifiedAssets">
                            {{ number_format($dashboardData['asset_statistics']['verified_assets'] ?? 0) }}
                        </div>
                        <div class="stat-label">Aset Terverifikasi</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="background-color: #fff3e0;">
                        <i class="fas fa-tools text-warning"></i>
                        <div class="stat-value" id="pendingMaintenance">
                            {{ $dashboardData['maintenance_statistics']['scheduled'] ?? 0 }}
                        </div>
                        <div class="stat-label">Pemeliharaan</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="background-color: #ffebee;">
                        <i class="fas fa-exchange-alt text-danger"></i>
                        <div class="stat-value" id="pendingTransactions">
                            {{ ($dashboardData['mutation_statistics']['pending'] ?? 0) + ($dashboardData['deletion_statistics']['pending'] ?? 0) }}
                        </div>
                        <div class="stat-label">Transaksi Tertunda</div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-line me-2"></i> Trend Nilai Aset (12 Bulan Terakhir)
                        </div>
                        <div class="card-body">
                            <canvas id="assetValueChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-2"></i> Distribusi Kondisi Aset
                        </div>
                        <div class="card-body">
                            <canvas id="conditionChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-history me-2"></i> Aktivitas Terbaru
                            </div>
                            <a href="{{ route('opd.dashboard.index') }}?tab=activities" class="btn btn-sm btn-outline-primary">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Aksi</th>
                                            <th>Aset</th>
                                            <th>User</th>
                                            <th>Deskripsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(($dashboardData['recent_activities'] ?? []) as $activity)
                                            <tr>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($activity->change_date)->format('d/m/Y H:i') }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ ucfirst($activity->action) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('opd.assets.show', $activity->asset_id) }}" class="text-decoration-none">
                                                        {{ $activity->asset_name }}
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">{{ $activity->asset_code }}</small>
                                                </td>
                                                <td>{{ $activity->user_name }}</td>
                                                <td>{{ $activity->description }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Tidak ada aktivitas terbaru</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Tab -->
        <div class="tab-pane fade {{ $tab == 'statistics' ? 'show active' : '' }}" id="statistics">
            @if(isset($data['statistics']))
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-2"></i> Statistik Aset
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h5>Ringkasan</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Total Aset</td>
                                            <td class="text-end fw-bold">{{ number_format($data['statistics']['total_assets']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Nilai</td>
                                            <td class="text-end fw-bold">Rp {{ number_format($data['statistics']['total_value'], 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Aset Aktif</td>
                                            <td class="text-end fw-bold">{{ number_format($data['statistics']['active_assets']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Dalam Perbaikan</td>
                                            <td class="text-end fw-bold">{{ number_format($data['statistics']['under_maintenance']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Dalam Mutasi</td>
                                            <td class="text-end fw-bold">{{ number_format($data['statistics']['mutated_assets']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Terhapus</td>
                                            <td class="text-end fw-bold">{{ number_format($data['statistics']['deleted_assets']) }}</td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div class="col-md-4">
                                    <h5>Distribusi Kondisi</h5>
                                    <table class="table table-sm">
                                        @foreach($data['conditionDistribution'] as $condition => $count)
                                            <tr>
                                                <td>{{ $condition }}</td>
                                                <td class="text-end fw-bold">{{ number_format($count) }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                                
                                <div class="col-md-4">
                                    <h5>Distribusi Status</h5>
                                    <table class="table table-sm">
                                        @foreach($data['statusDistribution'] as $status => $count)
                                            <tr>
                                                <td>
                                                    @php
                                                        $statusNames = [
                                                            'aktif' => 'Aktif',
                                                            'dimutasi' => 'Dalam Mutasi',
                                                            'dihapus' => 'Terhapus',
                                                            'dalam_perbaikan' => 'Dalam Perbaikan',
                                                            'nonaktif' => 'Nonaktif'
                                                        ];
                                                    @endphp
                                                    {{ $statusNames[$status] ?? $status }}
                                                </td>
                                                <td class="text-end fw-bold">{{ number_format($count) }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Maintenance Tab -->
        <div class="tab-pane fade {{ $tab == 'maintenance' ? 'show active' : '' }}" id="maintenance">
            @if(isset($data['maintenances']))
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-tools me-2"></i> Jadwal Pemeliharaan
                            </div>
                            <a href="{{ route('opd.transactions.create', ['type' => 'maintenance']) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> Jadwalkan
                            </a>
                        </div>
                        <div class="card-body">
                            <!-- Overdue Maintenance -->
                            @if(isset($data['overdue']) && $data['overdue']->count() > 0)
                                <div class="alert alert-warning">
                                    <h6><i class="fas fa-exclamation-triangle me-2"></i> Pemeliharaan Terlambat</h6>
                                    <ul class="mb-0">
                                        @foreach($data['overdue'] as $maintenance)
                                            <li>
                                                <a href="{{ route('opd.transactions.show', ['maintenance', $maintenance->maintenance_id]) }}" class="text-decoration-none">
                                                    {{ $maintenance->asset->name }} - {{ $maintenance->title }}
                                                </a>
                                                ({{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('d/m/Y') }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Upcoming Maintenance -->
                            @if(isset($data['upcoming']) && $data['upcoming']->count() > 0)
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-calendar-alt me-2"></i> Pemeliharaan Mendatang</h6>
                                    <ul class="mb-0">
                                        @foreach($data['upcoming'] as $maintenance)
                                            <li>
                                                <a href="{{ route('opd.transactions.show', ['maintenance', $maintenance->maintenance_id]) }}" class="text-decoration-none">
                                                    {{ $maintenance->asset->name }} - {{ $maintenance->title }}
                                                </a>
                                                ({{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('d/m/Y') }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Maintenance Table -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Aset</th>
                                            <th>Jenis</th>
                                            <th>Judul</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Biaya</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['maintenances'] as $maintenance)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('opd.assets.show', $maintenance->asset_id) }}" class="text-decoration-none">
                                                        {{ $maintenance->asset->name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @php
                                                        $types = [
                                                            'rutin' => 'Rutin',
                                                            'perbaikan' => 'Perbaikan',
                                                            'kalibrasi' => 'Kalibrasi',
                                                            'penggantian' => 'Penggantian',
                                                            'lainnya' => 'Lainnya'
                                                        ];
                                                    @endphp
                                                    {{ $types[$maintenance->maintenance_type] ?? $maintenance->maintenance_type }}
                                                </td>
                                                <td>{{ $maintenance->title }}</td>
                                                <td>{{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('d/m/Y') }}</td>
                                                <td>
                                                    @php
                                                        $statusBadges = [
                                                            'dijadwalkan' => 'warning',
                                                            'dalam_pengerjaan' => 'info',
                                                            'selesai' => 'success',
                                                            'dibatalkan' => 'danger',
                                                            'ditunda' => 'secondary'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $statusBadges[$maintenance->status] ?? 'secondary' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($maintenance->cost)
                                                        Rp {{ number_format($maintenance->cost, 0, ',', '.') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('opd.transactions.show', ['maintenance', $maintenance->maintenance_id]) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-center">
                                {{ $data['maintenances']->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Activities Tab -->
        <div class="tab-pane fade {{ $tab == 'activities' ? 'show active' : '' }}" id="activities">
            @if(isset($data['activities']))
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-history me-2"></i> Log Aktivitas
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Aksi</th>
                                            <th>Aset</th>
                                            <th>User</th>
                                            <th>IP Address</th>
                                            <th>Deskripsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['activities'] as $activity)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($activity->change_date)->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    @php
                                                        $actionColors = [
                                                            'create' => 'success',
                                                            'update' => 'primary',
                                                            'delete' => 'danger',
                                                            'restore' => 'info',
                                                            'verifikasi' => 'warning',
                                                            'validasi' => 'dark'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $actionColors[$activity->action] ?? 'secondary' }}">
                                                        {{ ucfirst($activity->action) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('opd.assets.show', $activity->asset_id) }}" class="text-decoration-none">
                                                        {{ $activity->asset_name }}
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">{{ $activity->asset_code }}</small>
                                                </td>
                                                <td>{{ $activity->user_name }}</td>
                                                <td><small class="text-muted">{{ $activity->ip_address }}</small></td>
                                                <td>{{ $activity->description }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-center">
                                {{ $data['activities']->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Quick Actions Tab -->
        <div class="tab-pane fade {{ $tab == 'quick-actions' ? 'show active' : '' }}" id="quick-actions">
            @if(isset($data['quickStats']))
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-bolt me-2"></i> Quick Actions & Prioritas
                        </div>
                        <div class="card-body">
                            <!-- Quick Stats -->
                            <div class="row mb-4">
                                @if($data['quickStats']['pending_maintenance'] > 0)
                                <div class="col-md-6 col-lg-4">
                                    <div class="alert alert-warning">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">
                                                    <i class="fas fa-tools me-2"></i> Pemeliharaan Tertunda
                                                </h6>
                                                <p class="mb-0">{{ $data['quickStats']['pending_maintenance'] }} item</p>
                                            </div>
                                            <a href="{{ route('opd.transactions.index', ['tab' => 'maintenances', 'status' => 'dijadwalkan']) }}" class="btn btn-sm btn-warning">
                                                Lihat
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                @if($data['quickStats']['assets_needing_attention'] > 0)
                                <div class="col-md-6 col-lg-4">
                                    <div class="alert alert-danger">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">
                                                    <i class="fas fa-exclamation-triangle me-2"></i> Aset Perlu Perhatian
                                                </h6>
                                                <p class="mb-0">{{ $data['quickStats']['assets_needing_attention'] }} aset rusak</p>
                                            </div>
                                            <a href="{{ route('opd.assets.index', ['condition' => 'Rusak Ringan']) }}" class="btn btn-sm btn-danger">
                                                Periksa
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                @if($data['quickStats']['pending_deletions'] > 0)
                                <div class="col-md-6 col-lg-4">
                                    <div class="alert alert-info">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">
                                                    <i class="fas fa-trash-alt me-2"></i> Penghapusan Tertunda
                                                </h6>
                                                <p class="mb-0">{{ $data['quickStats']['pending_deletions'] }} proposal</p>
                                            </div>
                                            <a href="{{ route('opd.transactions.index', ['tab' => 'deletions', 'status' => 'diusulkan']) }}" class="btn btn-sm btn-info">
                                                Proses
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                @if($data['quickStats']['pending_mutations'] > 0)
                                <div class="col-md-6 col-lg-4">
                                    <div class="alert alert-primary">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">
                                                    <i class="fas fa-exchange-alt me-2"></i> Mutasi Keluar
                                                </h6>
                                                <p class="mb-0">{{ $data['quickStats']['pending_mutations'] }} proposal</p>
                                            </div>
                                            <a href="{{ route('opd.transactions.index', ['tab' => 'mutations', 'status' => 'diusulkan']) }}" class="btn btn-sm btn-primary">
                                                Lihat
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                @if($data['quickStats']['incoming_mutations'] > 0)
                                <div class="col-md-6 col-lg-4">
                                    <div class="alert alert-success">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">
                                                    <i class="fas fa-download me-2"></i> Mutasi Masuk
                                                </h6>
                                                <p class="mb-0">{{ $data['quickStats']['incoming_mutations'] }} aset</p>
                                            </div>
                                            <a href="{{ route('opd.transactions.index', ['tab' => 'mutations']) }}" class="btn btn-sm btn-success">
                                                Terima
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Quick Action Buttons -->
                            <h5 class="mb-3">Aksi Cepat</h5>
                            <div class="quick-actions mb-4">
                                <a href="{{ route('opd.assets.create') }}" class="quick-action-btn">
                                    <i class="fas fa-plus text-success"></i>
                                    <span>Tambah Aset Baru</span>
                                </a>
                                
                                <a href="{{ route('opd.transactions.create', ['type' => 'maintenance']) }}" class="quick-action-btn">
                                    <i class="fas fa-tools text-warning"></i>
                                    <span>Jadwalkan Pemeliharaan</span>
                                </a>
                                
                                <a href="{{ route('opd.transactions.create', ['type' => 'deletion']) }}" class="quick-action-btn">
                                    <i class="fas fa-trash-alt text-danger"></i>
                                    <span>Ajukan Penghapusan</span>
                                </a>
                                
                                <a href="{{ route('opd.transactions.create', ['type' => 'mutation']) }}" class="quick-action-btn">
                                    <i class="fas fa-exchange-alt text-primary"></i>
                                    <span>Ajukan Mutasi</span>
                                </a>
                                
                                <a href="{{ route('opd.master.index', ['tab' => 'locations']) }}" class="quick-action-btn">
                                    <i class="fas fa-map-marker-alt text-info"></i>
                                    <span>Kelola Lokasi</span>
                                </a>
                            </div>

                            <!-- Export Options -->
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-file-export me-2"></i> Ekspor Data
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <button class="btn btn-outline-success w-100 mb-2" onclick="exportData('excel')">
                                                <i class="fas fa-file-excel me-2"></i> Excel
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-outline-danger w-100 mb-2" onclick="exportData('pdf')">
                                                <i class="fas fa-file-pdf me-2"></i> PDF
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-outline-secondary w-100 mb-2" onclick="exportData('csv')">
                                                <i class="fas fa-file-csv me-2"></i> CSV
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Load chart data
        $(document).ready(function() {
            loadChartData();
            
            // Refresh charts when tab changes
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                if (e.target.id === 'overview-tab') {
                    loadChartData();
                }
            });
        });
        
        function loadChartData() {
            $.ajax({
                url: "{{ route('opd.dashboard.chartData') }}",
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderAssetValueChart(response.trendData);
                    }
                }
            });
            
            // Render condition chart
            renderConditionChart(@json($dashboardData['asset_statistics']['condition_distribution'] ?? []));
        }
        
        function renderAssetValueChart(trendData) {
            const ctx = document.getElementById('assetValueChart').getContext('2d');
            
            const labels = trendData.map(item => item.period);
            const values = trendData.map(item => item.total_value / 1000000); // Convert to millions
            
            if (window.assetValueChart) {
                window.assetValueChart.destroy();
            }
            
            window.assetValueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nilai Aset (Juta Rp)',
                        data: values,
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Nilai: Rp ' + (context.raw * 1000000).toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value * 1).toLocaleString('id-ID') + ' Jt';
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function renderConditionChart(conditionData) {
            const ctx = document.getElementById('conditionChart').getContext('2d');
            
            const labels = Object.keys(conditionData);
            const values = Object.values(conditionData);
            
            const backgroundColors = [
                'rgba(39, 174, 96, 0.7)',  // Baik - green
                'rgba(243, 156, 18, 0.7)', // Rusak Ringan - orange
                'rgba(231, 76, 60, 0.7)'   // Rusak Berat - red
            ];
            
            if (window.conditionChart) {
                window.conditionChart.destroy();
            }
            
            window.conditionChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: backgroundColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} aset (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function exportData(format) {
            $.ajax({
                url: "{{ route('opd.assets.export') }}",
                method: 'GET',
                data: { format: format },
                success: function(response) {
                    if (response.success) {
                        showToast(`Data berhasil diekspor (${response.count} records)`, 'success');
                        
                        // In real implementation, you would trigger file download here
                        // window.location.href = response.download_url;
                    }
                },
                error: handleAjaxError
            });
        }
    </script>
    @endpush
@endsection