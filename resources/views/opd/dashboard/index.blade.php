@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'overview' ? 'active' : '' }}" 
                   href="{{ route('opd.dashboard.index', ['tab' => 'overview']) }}">
                    <i class="fas fa-chart-pie me-1"></i> Overview
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'statistics' ? 'active' : '' }}" 
                   href="{{ route('opd.dashboard.index', ['tab' => 'statistics']) }}">
                    <i class="fas fa-chart-bar me-1"></i> Statistik
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'maintenance' ? 'active' : '' }}" 
                   href="{{ route('opd.dashboard.index', ['tab' => 'maintenance']) }}">
                    <i class="fas fa-tools me-1"></i> Pemeliharaan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'activities' ? 'active' : '' }}" 
                   href="{{ route('opd.dashboard.index', ['tab' => 'activities']) }}">
                    <i class="fas fa-history me-1"></i> Aktivitas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'quick-actions' ? 'active' : '' }}" 
                   href="{{ route('opd.dashboard.index', ['tab' => 'quick-actions']) }}">
                    <i class="fas fa-bolt me-1"></i> Aksi Cepat
                </a>
            </li>
        </ul>
        
        <div class="tab-content mt-3">
            @if($tab == 'overview')
                <!-- Overview Tab -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card bg-primary text-white">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="stat-label">Total Aset</h6>
                                    <h3 class="stat-value">{{ number_format($dashboardData['asset_statistics']['total_assets']) }}</h3>
                                </div>
                                <i class="fas fa-boxes"></i>
                            </div>
                            <small>Rp {{ number_format($dashboardData['asset_statistics']['total_value'], 0, ',', '.') }}</small>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card bg-success text-white">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="stat-label">Aktif</h6>
                                    <h3 class="stat-value">{{ number_format($dashboardData['asset_statistics']['active_assets']) }}</h3>
                                </div>
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <small>{{ number_format($dashboardData['asset_statistics']['verification_rate'], 1) }}% tervalidasi</small>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card bg-warning text-white">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="stat-label">Perbaikan</h6>
                                    <h3 class="stat-value">{{ number_format($dashboardData['maintenance_statistics']['scheduled']) }}</h3>
                                </div>
                                <i class="fas fa-tools"></i>
                            </div>
                            <small>{{ number_format($dashboardData['maintenance_statistics']['overdue']) }} overdue</small>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card bg-info text-white">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="stat-label">Transaksi</h6>
                                    <h3 class="stat-value">{{ number_format($dashboardData['deletion_statistics']['pending'] + $dashboardData['mutation_statistics']['pending']) }}</h3>
                                </div>
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <small>Menunggu persetujuan</small>
                        </div>
                    </div>
                </div>
                
                <!-- Charts -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Distribusi Kondisi Aset</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="conditionChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Trend Nilai Aset (6 Bulan)</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="valueTrendChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activities -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Aktivitas Terbaru</h6>
                                <a href="{{ route('opd.dashboard.index', ['tab' => 'activities']) }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Waktu</th>
                                                <th>Aktivitas</th>
                                                <th>Aset</th>
                                                <th>User</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dashboardData['recent_activities'] as $activity)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($activity->change_date)->format('d/m/Y H:i') }}</td>
                                                <td>{{ $activity->description }}</td>
                                                <td>{{ $activity->asset_name }}</td>
                                                <td>{{ $activity->user_name }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @push('scripts')
                <script>
                    $(document).ready(function() {
                        // Condition Distribution Chart
                        const conditionCtx = document.getElementById('conditionChart').getContext('2d');
                        const conditionChart = new Chart(conditionCtx, {
                            type: 'doughnut',
                            data: {
                                labels: ['Baik', 'Rusak Ringan', 'Rusak Berat'],
                                datasets: [{
                                    data: [
                                        {{ $dashboardData['asset_statistics']['condition_distribution']['Baik'] ?? 0 }},
                                        {{ $dashboardData['asset_statistics']['condition_distribution']['Rusak Ringan'] ?? 0 }},
                                        {{ $dashboardData['asset_statistics']['condition_distribution']['Rusak Berat'] ?? 0 }}
                                    ],
                                    backgroundColor: ['#27ae60', '#f39c12', '#e74c3c']
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }
                        });
                        
                        // Value Trend Chart
                        const trendCtx = document.getElementById('valueTrendChart').getContext('2d');
                        const trendChart = new Chart(trendCtx, {
                            type: 'line',
                            data: {
                                labels: @json(collect($dashboardData['asset_value_trend'])->pluck('period')),
                                datasets: [{
                                    label: 'Nilai Aset (Juta)',
                                    data: @json(collect($dashboardData['asset_value_trend'])->map(function($item) { return $item['total_value'] / 1000000; })),
                                    borderColor: '#3498db',
                                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Juta Rupiah'
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>
                @endpush
                
            @elseif($tab == 'statistics')
                <!-- Statistics Tab -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Statistik Aset</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <td>Total Aset</td>
                                        <td class="text-end">{{ number_format($data['statistics']['total_assets']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Nilai</td>
                                        <td class="text-end">Rp {{ number_format($data['statistics']['total_value'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Aset Aktif</td>
                                        <td class="text-end">{{ number_format($data['statistics']['active_assets']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Dalam Perbaikan</td>
                                        <td class="text-end">{{ number_format($data['statistics']['under_maintenance']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Telah Dimutasi</td>
                                        <td class="text-end">{{ number_format($data['statistics']['mutated_assets']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Telah Dihapus</td>
                                        <td class="text-end">{{ number_format($data['statistics']['deleted_assets']) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Distribusi Kondisi</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <td>Baik</td>
                                        <td class="text-end">{{ number_format($data['conditionDistribution']['Baik'] ?? 0) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Rusak Ringan</td>
                                        <td class="text-end">{{ number_format($data['conditionDistribution']['Rusak Ringan'] ?? 0) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Rusak Berat</td>
                                        <td class="text-end">{{ number_format($data['conditionDistribution']['Rusak Berat'] ?? 0) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
            @elseif($tab == 'maintenance')
                <!-- Maintenance Tab -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Jadwal Pemeliharaan</h6>
                                <a href="{{ route('opd.transactions.create', ['type' => 'maintenance']) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-1"></i> Jadwalkan
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table data-table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Aset</th>
                                                <th>Jenis</th>
                                                <th>Vendor</th>
                                                <th>Biaya</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data['maintenances'] as $maintenance)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('d/m/Y') }}</td>
                                                <td>{{ $maintenance->asset->name }}</td>
                                                <td>
                                                    @php
                                                        $types = ['rutin' => 'Rutin', 'perbaikan' => 'Perbaikan', 'kalibrasi' => 'Kalibrasi'];
                                                    @endphp
                                                    {{ $types[$maintenance->maintenance_type] ?? $maintenance->maintenance_type }}
                                                </td>
                                                <td>{{ $maintenance->vendor ?? '-' }}</td>
                                                <td>Rp {{ number_format($maintenance->cost, 0, ',', '.') }}</td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'dijadwalkan' => 'warning',
                                                            'dalam_pengerjaan' => 'info',
                                                            'selesai' => 'success',
                                                            'dibatalkan' => 'secondary'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColors[$maintenance->status] ?? 'secondary' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                                                    </span>
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
                            </div>
                        </div>
                    </div>
                </div>
                
            @elseif($tab == 'activities')
                <!-- Activities Tab -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Riwayat Aktivitas</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table data-table">
                                        <thead>
                                            <tr>
                                                <th>Waktu</th>
                                                <th>Aksi</th>
                                                <th>Deskripsi</th>
                                                <th>Aset</th>
                                                <th>User</th>
                                                <th>IP Address</th>
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
                                                            'update' => 'warning',
                                                            'delete' => 'danger',
                                                            'verifikasi' => 'info',
                                                            'validasi' => 'primary'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $actionColors[$activity->action] ?? 'secondary' }}">
                                                        {{ ucfirst($activity->action) }}
                                                    </span>
                                                </td>
                                                <td>{{ $activity->description }}</td>
                                                <td>
                                                    {{ $activity->asset_name }}
                                                    <br><small class="text-muted">{{ $activity->asset_code }}</small>
                                                </td>
                                                <td>{{ $activity->user_name }}</td>
                                                <td><small class="text-muted">{{ $activity->ip_address }}</small></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-flex justify-content-center">
                                    {{ $data['activities']->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            @elseif($tab == 'quick-actions')
                <!-- Quick Actions Tab -->
                <div class="row">
                    @php
                        $quickStats = $data['quickStats'];
                    @endphp
                    
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="mb-3">
                                    <i class="fas fa-tools fa-3x text-warning"></i>
                                </div>
                                <h4>{{ $quickStats['pending_maintenance'] }}</h4>
                                <p class="text-muted">Pemeliharaan Tertunda</p>
                                <a href="{{ route('opd.transactions.index', ['tab' => 'maintenances']) }}" class="btn btn-outline-warning">Lihat</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="mb-3">
                                    <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
                                </div>
                                <h4>{{ $quickStats['assets_needing_attention'] }}</h4>
                                <p class="text-muted">Aset Perlu Perhatian</p>
                                <a href="{{ route('opd.assets.index', ['condition' => 'Rusak Ringan']) }}" class="btn btn-outline-danger">Lihat</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="mb-3">
                                    <i class="fas fa-exchange-alt fa-3x text-info"></i>
                                </div>
                                <h4>{{ $quickStats['incoming_mutations'] }}</h4>
                                <p class="text-muted">Mutasi Masuk</p>
                                <a href="{{ route('opd.transactions.index', ['tab' => 'mutations']) }}" class="btn btn-outline-info">Lihat</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Aksi Cepat</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <a href="{{ route('opd.assets.create') }}" class="btn btn-primary w-100 text-start py-3">
                                            <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                            <h6>Tambah Aset Baru</h6>
                                            <small class="text-light">Tambahkan aset baru ke inventaris</small>
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="{{ route('opd.transactions.create', ['type' => 'maintenance']) }}" class="btn btn-warning w-100 text-start py-3">
                                            <i class="fas fa-tools fa-2x mb-2"></i>
                                            <h6>Jadwalkan Perbaikan</h6>
                                            <small class="text-dark">Buat jadwal pemeliharaan aset</small>
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="{{ route('opd.master.index', ['tab' => 'locations']) }}" class="btn btn-success w-100 text-start py-3">
                                            <i class="fas fa-map-marker-alt fa-2x mb-2"></i>
                                            <h6>Kelola Lokasi</h6>
                                            <small class="text-light">Tambah/Edit lokasi aset</small>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection