@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
            <div class="d-flex gap-2">
                <form method="GET" class="d-flex gap-2">
                    <select name="period" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        <option value="monthly" {{ ($period ?? 'monthly') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        <option value="quarterly" {{ ($period ?? '') == 'quarterly' ? 'selected' : '' }}>Triwulan</option>
                        <option value="yearly" {{ ($period ?? '') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                    </select>
                    
                    <select name="opd_unit_id" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        <option value="">Semua OPD</option>
                        @foreach($opdUnits ?? [] as $opd)
                            <option value="{{ $opd->opd_unit_id }}" 
                                {{ ($selectedOpdUnitId ?? '') == $opd->opd_unit_id ? 'selected' : '' }}>
                                {{ $opd->kode_opd }} - {{ $opd->nama_opd }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    @php
        $stats = $dashboardData['asset_statistics'] ?? [];
    @endphp
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Total Aset</h6>
                    <h3 class="mb-0">{{ number_format($stats['total_assets'] ?? 0) }}</h3>
                    <p class="mb-0 text-muted">Nilai: {{ $stats['total_value'] ? 'Rp ' . number_format($stats['total_value'], 0, ',', '.') : '-' }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-cubes"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card" style="border-left-color: #27ae60;">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Aset Aktif</h6>
                    <h3 class="mb-0">{{ number_format($stats['active_assets'] ?? 0) }}</h3>
                    <p class="mb-0 text-muted">
                        @if($stats['total_assets'] ?? 0 > 0)
                            {{ round(($stats['active_assets'] / $stats['total_assets']) * 100, 1) }}%
                        @else
                            0%
                        @endif dari total
                    </p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle" style="color: #27ae60;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card" style="border-left-color: #f39c12;">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Pending Verifikasi</h6>
                    <h3 class="mb-0">{{ number_format($stats['total_assets'] - $stats['verified_assets'] ?? 0) }}</h3>
                    <p class="mb-0 text-muted">
                        Verifikasi: {{ $stats['verification_rate'] ?? 0 }}%
                    </p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock" style="color: #f39c12;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card" style="border-left-color: #3498db;">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Total OPD</h6>
                    <h3 class="mb-0">{{ count($opdUnits ?? []) }}</h3>
                    <p class="mb-0 text-muted">
                        {{ count($dashboardData['opd_units'] ?? []) }} aktif
                    </p>
                </div>
                <div class="icon">
                    <i class="fas fa-building" style="color: #3498db;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <!-- Asset Distribution -->
    <div class="col-lg-8 mb-4">
        <div class="card-custom">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2"></i> Distribusi Aset per Kategori
            </div>
            <div class="card-body">
                <canvas id="assetDistributionChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Status Distribution -->
    <div class="col-lg-4 mb-4">
        <div class="card-custom">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i> Status Aset
            </div>
            <div class="card-body">
                <canvas id="statusDistributionChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities & Proposals -->
<div class="row">
    <!-- Recent Activities -->
    <div class="col-lg-6 mb-4">
        <div class="card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-history me-2"></i> Aktivitas Terbaru
                </div>
                <a href="{{ route('admin.assets.index') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse(($recentActivities ?? []) as $activity)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $activity->asset_name ?? 'Aset' }}</h6>
                                    <p class="mb-1 text-muted small">{{ $activity->description ?? '' }}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>{{ $activity->user_name ?? 'System' }}
                                        <i class="fas fa-clock ms-3 me-1"></i>
                                        {{ \Carbon\Carbon::parse($activity->change_date ?? now())->diffForHumans() }}
                                    </small>
                                </div>
                                <span class="badge bg-light text-dark">
                                    {{ $activity->action ?? 'update' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-3"></i>
                            <p>Tidak ada aktivitas terbaru</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pending Proposals -->
    <div class="col-lg-6 mb-4">
        <div class="card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-clock me-2"></i> Proposal Menunggu
                </div>
                <a href="{{ route('admin.proposals.index') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @php
                        $pendingMutations = $dashboardData['mutation_statistics']['pending'] ?? 0;
                        $pendingDeletions = $dashboardData['deletion_statistics']['pending'] ?? 0;
                    @endphp
                    
                    <a href="{{ route('admin.proposals.index', ['type' => 'mutations', 'status' => 'diusulkan']) }}" 
                       class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Mutasi Aset</h6>
                                <p class="mb-0 text-muted small">Menunggu persetujuan</p>
                            </div>
                            <span class="badge bg-warning">{{ $pendingMutations }}</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.proposals.index', ['type' => 'deletions', 'status' => 'diusulkan']) }}" 
                       class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Penghapusan Aset</h6>
                                <p class="mb-0 text-muted small">Menunggu persetujuan</p>
                            </div>
                            <span class="badge bg-warning">{{ $pendingDeletions }}</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.assets.index', ['type' => 'pending-verification']) }}" 
                       class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Verifikasi Dokumen</h6>
                                <p class="mb-0 text-muted small">Aset menunggu verifikasi</p>
                            </div>
                            <span class="badge bg-info">{{ $stats['total_assets'] - $stats['verified_assets'] ?? 0 }}</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.assets.index', ['type' => 'pending-validation']) }}" 
                       class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Validasi Aset</h6>
                                <p class="mb-0 text-muted small">Aset menunggu validasi</p>
                            </div>
                            <span class="badge bg-info">{{ $stats['total_assets'] - $stats['verified_assets'] ?? 0 }}</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Asset Distribution Chart
    const assetDistributionData = @json($dashboardData['asset_statistics']['status_distribution'] ?? []);
    const distributionLabels = Object.keys(assetDistributionData);
    const distributionValues = Object.values(assetDistributionData).map(item => item.count || 0);
    
    const distributionCtx = document.getElementById('assetDistributionChart').getContext('2d');
    new Chart(distributionCtx, {
        type: 'pie',
        data: {
            labels: distributionLabels,
            datasets: [{
                data: distributionValues,
                backgroundColor: [
                    '#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6', '#1abc9c'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });
    
    // Status Distribution Chart
    const statusData = @json($dashboardData['asset_statistics']['status_distribution'] ?? []);
    const statusLabels = Object.keys(statusData);
    const statusCounts = Object.values(statusData).map(item => item.count || 0);
    
    const statusCtx = document.getElementById('statusDistributionChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: [
                    '#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
</script>
@endpush
@endsection