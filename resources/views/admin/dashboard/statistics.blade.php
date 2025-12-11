@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i>
                        {{ $title }}
                    </h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <form class="px-3 py-2" method="GET" action="{{ route('admin.dashboard.statistics') }}">
                                    <input type="hidden" name="type" value="{{ $type }}">
                                    <div class="form-group mb-2">
                                        <label>Filter OPD</label>
                                        <select name="opd_unit_id" class="form-control form-control-sm">
                                            <option value="">Semua OPD</option>
                                            @foreach($opdUnits as $opd)
                                            <option value="{{ $opd->opd_unit_id }}" 
                                                    {{ $selectedOpdUnitId == $opd->opd_unit_id ? 'selected' : '' }}>
                                                {{ $opd->kode_opd }} - {{ $opd->nama_opd }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary btn-block">
                                        Terapkan Filter
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if($type === 'assets')
    <!-- Statistik Aset -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($statistics['total_assets'], 0, ',', '.') }}</h3>
                    <p>Total Aset</p>
                </div>
                <div class="icon">
                    <i class="fas fa-cube"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($statistics['total_value'], 0, ',', '.') }}</h3>
                    <p>Total Nilai Aset</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($statistics['active_assets'], 0, ',', '.') }}</h3>
                    <p>Aset Aktif</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $statistics['verification_rate'] }}%</h3>
                    <p>Terverifikasi & Tervalidasi</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Distribusi Status Aset</h3>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="statusChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Distribusi Kondisi Aset</h3>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="conditionChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Status Aset</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Jumlah Aset</th>
                                <th>Total Nilai</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalAssets = $statistics['total_assets'];
                                $totalValue = $statistics['total_value'];
                            @endphp
                            
                            @foreach($statistics['status_distribution'] as $status => $data)
                            <tr>
                                <td>
                                    <span class="badge 
                                        @if($status == 'aktif') bg-success
                                        @elseif($status == 'dimutasi') bg-info
                                        @elseif($status == 'dihapus') bg-danger
                                        @elseif($status == 'dalam_perbaikan') bg-warning
                                        @else bg-secondary @endif">
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </span>
                                </td>
                                <td>{{ number_format($data['count'], 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($data['total_value'], 0, ',', '.') }}</td>
                                <td>
                                    <div class="progress progress-xs">
                                        <div class="progress-bar 
                                            @if($status == 'aktif') bg-success
                                            @elseif($status == 'dimutasi') bg-info
                                            @elseif($status == 'dihapus') bg-danger
                                            @elseif($status == 'dalam_perbaikan') bg-warning
                                            @else bg-secondary @endif" 
                                            style="width: {{ $totalAssets > 0 ? ($data['count'] / $totalAssets * 100) : 0 }}%">
                                        </div>
                                    </div>
                                    <span>{{ number_format($totalAssets > 0 ? ($data['count'] / $totalAssets * 100) : 0, 1) }}%</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    @elseif($type === 'mutations')
    <!-- Statistik Mutasi -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($statistics['total'], 0, ',', '.') }}</h3>
                    <p>Total Mutasi</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($statistics['pending'], 0, ',', '.') }}</h3>
                    <p>Menunggu Persetujuan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($statistics['completed'], 0, ',', '.') }}</h3>
                    <p>Mutasi Selesai</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $statistics['completion_rate'] }}%</h3>
                    <p>Completion Rate</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Statistik Mutasi (12 Bulan Terakhir)</h3>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="mutationMonthlyChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Distribusi Status Mutasi</h3>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="mutationStatusChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Mutasi Per Bulan</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Bulan/Tahun</th>
                                <th>Jumlah Mutasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statistics['monthly_statistics'] as $monthly)
                            <tr>
                                <td>{{ $monthly->month }}/{{ $monthly->year }}</td>
                                <td>{{ number_format($monthly->count, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    @elseif($type === 'deletions')
    <!-- Statistik Penghapusan -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($statistics['total'], 0, ',', '.') }}</h3>
                    <p>Total Pengajuan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-trash"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($statistics['pending'], 0, ',', '.') }}</h3>
                    <p>Menunggu Persetujuan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($statistics['completed'], 0, ',', '.') }}</h3>
                    <p>Penghapusan Selesai</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $statistics['approval_rate'] }}%</h3>
                    <p>Approval Rate</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Distribusi Status Penghapusan</h3>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="deletionStatusChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Statistik Penghapusan</h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Ditolak</span>
                                    <span class="info-box-number">{{ $statistics['rejected'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Disetujui</span>
                                    <span class="info-box-number">{{ $statistics['approved'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @endif
    
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Navigasi Statistik Lainnya</h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('admin.dashboard.statistics', ['type' => 'assets']) }}" 
                               class="btn btn-app {{ $type == 'assets' ? 'bg-primary' : '' }}">
                                <i class="fas fa-cubes"></i> Aset
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('admin.dashboard.statistics', ['type' => 'mutations']) }}" 
                               class="btn btn-app {{ $type == 'mutations' ? 'bg-primary' : '' }}">
                                <i class="fas fa-exchange-alt"></i> Mutasi
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('admin.dashboard.statistics', ['type' => 'deletions']) }}" 
                               class="btn btn-app {{ $type == 'deletions' ? 'bg-primary' : '' }}">
                                <i class="fas fa-trash"></i> Penghapusan
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('admin.dashboard.asset-distribution') }}" 
                               class="btn btn-app">
                                <i class="fas fa-chart-pie"></i> Distribusi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        @if($type === 'assets')
        // Chart Status Aset
        var statusCtx = document.getElementById('statusChart').getContext('2d');
        var statusLabels = [];
        var statusData = [];
        var statusColors = [];
        
        @foreach($statistics['status_distribution'] as $status => $data)
            statusLabels.push('{{ ucfirst(str_replace("_", " ", $status)) }}');
            statusData.push({{ $data['count'] }});
            
            @if($status == 'aktif')
                statusColors.push('#28a745');
            @elseif($status == 'dimutasi')
                statusColors.push('#17a2b8');
            @elseif($status == 'dihapus')
                statusColors.push('#dc3545');
            @elseif($status == 'dalam_perbaikan')
                statusColors.push('#ffc107');
            @else
                statusColors.push('#6c757d');
            @endif
        @endforeach
        
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: statusColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom'
                }
            }
        });
        
        // Chart Kondisi Aset
        var conditionCtx = document.getElementById('conditionChart').getContext('2d');
        var conditionLabels = [];
        var conditionData = [];
        var conditionColors = ['#28a745', '#ffc107', '#dc3545'];
        
        @foreach($statistics['condition_distribution'] as $condition => $count)
            conditionLabels.push('{{ $condition }}');
            conditionData.push({{ $count }});
        @endforeach
        
        new Chart(conditionCtx, {
            type: 'bar',
            data: {
                labels: conditionLabels,
                datasets: [{
                    label: 'Jumlah Aset',
                    data: conditionData,
                    backgroundColor: conditionColors,
                    borderColor: conditionColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        
        @elseif($type === 'mutations')
        // Chart Bulanan Mutasi
        var monthlyCtx = document.getElementById('mutationMonthlyChart').getContext('2d');
        var monthlyLabels = [];
        var monthlyData = [];
        
        @foreach($statistics['monthly_statistics'] as $monthly)
            monthlyLabels.push('{{ $monthly->month }}/{{ $monthly->year }}');
            monthlyData.push({{ $monthly->count }});
        @endforeach
        
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthlyLabels.reverse(),
                datasets: [{
                    label: 'Jumlah Mutasi',
                    data: monthlyData.reverse(),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        
        // Chart Status Mutasi
        var mutationStatusCtx = document.getElementById('mutationStatusChart').getContext('2d');
        var mutationData = [{{ $statistics['pending'] }}, {{ $statistics['approved'] }}, {{ $statistics['completed'] }}];
        
        new Chart(mutationStatusCtx, {
            type: 'pie',
            data: {
                labels: ['Menunggu', 'Disetujui', 'Selesai'],
                datasets: [{
                    data: mutationData,
                    backgroundColor: ['#ffc107', '#28a745', '#007bff'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom'
                }
            }
        });
        
        @elseif($type === 'deletions')
        // Chart Status Penghapusan
        var deletionStatusCtx = document.getElementById('deletionStatusChart').getContext('2d');
        var deletionData = [
            {{ $statistics['pending'] }},
            {{ $statistics['approved'] }},
            {{ $statistics['completed'] }},
            {{ $statistics['rejected'] }}
        ];
        
        new Chart(deletionStatusCtx, {
            type: 'pie',
            data: {
                labels: ['Menunggu', 'Disetujui', 'Selesai', 'Ditolak'],
                datasets: [{
                    data: deletionData,
                    backgroundColor: ['#ffc107', '#28a745', '#007bff', '#dc3545'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom'
                }
            }
        });
        @endif
    });
</script>
@endpush

@push('styles')
<style>
    .small-box {
        border-radius: .25rem;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        margin-bottom: 20px;
        position: relative;
        display: block;
    }
    
    .small-box > .inner {
        padding: 10px;
    }
    
    .small-box h3 {
        font-size: 2.2rem;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
    
    .small-box p {
        font-size: 1rem;
    }
    
    .small-box .icon {
        position: absolute;
        top: -10px;
        right: 10px;
        z-index: 0;
        font-size: 70px;
        color: rgba(0,0,0,0.15);
    }
    
    .btn-app {
        border-radius: 3px;
        position: relative;
        padding: 15px 5px;
        margin: 0 0 10px 10px;
        min-width: 80px;
        height: 60px;
        text-align: center;
        color: #666;
        border: 1px solid #ddd;
        background-color: #f4f4f4;
        font-size: 12px;
    }
    
    .btn-app:hover {
        background: #e9e9e9;
        color: #333;
        border-color: #aaa;
    }
    
    .btn-app > .fas, .btn-app > .far, .btn-app > .fab, .btn-app > .glyphicon, .btn-app > .ion {
        font-size: 20px;
        display: block;
    }
    
    .info-box {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        border-radius: .25rem;
        background: #fff;
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .info-box-icon {
        border-radius: .25rem 0 0 .25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 70px;
        font-size: 30px;
        color: #fff;
    }
    
    .info-box-content {
        padding: 5px 10px;
        margin-left: 70px;
    }
    
    .info-box-text {
        text-transform: uppercase;
        display: block;
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .info-box-number {
        display: block;
        font-weight: bold;
        font-size: 18px;
    }
</style>
@endpush