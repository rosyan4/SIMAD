@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0"><i class="fas fa-cog me-2"></i>{{ $title }}</h2>
        </div>
    </div>
</div>

<!-- Settings Sections -->
<div class="row">
    @foreach($sections as $section)
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="{{ route('admin.settings.manage', ['section' => $section['id'], 'action' => 'index']) }}" 
               class="card-custom text-decoration-none">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="{{ $section['icon'] }} fa-3x" style="color: var(--secondary-color);"></i>
                    </div>
                    <h5 class="card-title">{{ $section['name'] }}</h5>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="badge bg-primary">{{ $section['count'] }} data</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<!-- Category Statistics -->
@if(isset($categoryStats) && count($categoryStats) > 0)
    <div class="card-custom mt-4">
        <div class="card-header">
            <i class="fas fa-chart-pie me-2"></i> Statistik Kategori
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Kategori</th>
                            <th>Kode KIB</th>
                            <th>Jumlah Aset</th>
                            <th>Total Nilai</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categoryStats as $stat)
                            <tr>
                                <td>{{ $stat['name'] }}</td>
                                <td><span class="badge bg-info">{{ $stat['kib_code'] }}</span></td>
                                <td>{{ number_format($stat['asset_count']) }}</td>
                                <td>{{ $stat['formatted_value'] }}</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        @php
                                            $totalValueSum = array_sum(array_column($categoryStats, 'total_value'));
                                            $percentage = $totalValueSum > 0 
                                                ? ($stat['total_value'] / $totalValueSum) * 100 
                                                : 0;
                                        @endphp
                                        <div class="progress-bar" style="width: {{ $percentage }}%;">
                                            {{ round($percentage, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<!-- System Info -->
<div class="card-custom mt-4">
    <div class="card-header">
        <i class="fas fa-info-circle me-2"></i> Informasi Sistem
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <td width="40%">Versi Aplikasi</td>
                        <td><strong>SIMAD v1.0.0</strong></td>
                    </tr>
                    <tr>
                        <td>Framework</td>
                        <td>Laravel {{ app()->version() }}</td>
                    </tr>
                    <tr>
                        <td>Environment</td>
                        <td><span class="badge bg-success">{{ app()->environment() }}</span></td>
                    </tr>
                    <tr>
                        <td>Timezone</td>
                        <td>{{ config('app.timezone') }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <td width="40%">Total Pengguna</td>
                        <td><strong>{{ \App\Models\User::count() }}</strong></td>
                    </tr>
                    <tr>
                        <td>Total Aset</td>
                        <td><strong>{{ \App\Models\Asset::count() }}</strong></td>
                    </tr>
                    <tr>
                        <td>Total OPD</td>
                        <td><strong>{{ \App\Models\OpdUnit::count() }}</strong></td>
                    </tr>
                    <tr>
                        <td>Database</td>
                        <td>{{ config('database.default') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection